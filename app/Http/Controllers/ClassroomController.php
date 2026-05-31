<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\ClassroomAuthorization;
use App\Models\ClassroomHomework;
use App\Models\ClassroomSubmission;
use App\Models\ClassroomMessage;
use App\Models\Group;
use App\Models\Module;
use App\Models\ClassroomPost;
use App\Models\Comment;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $classes = [];

        if ($user->isStudent()) {
            $student = $user->student;
            if ($student) {
                $groupId = $student->group_id;
                $schedules = \App\Models\Schedule::where('group_id', $groupId)
                    ->with(['module', 'group', 'professor.user'])
                    ->get();

                foreach ($schedules as $s) {
                    if (!$s->module) continue;
                    $key = $groupId . '_' . $s->module_id;
                    if (!isset($classes[$key])) {
                        $classes[$key] = [
                            'group' => $s->group ?? Group::find($groupId),
                            'module' => $s->module,
                            'professor' => $s->professor,
                        ];
                    }
                }
            }
        } elseif ($user->isProfessor()) {
            $professor = $user->professor;
            if ($professor) {
                $schedules = \App\Models\Schedule::where('professor_id', $professor->id)
                    ->with(['module', 'group'])
                    ->get();

                foreach ($schedules as $s) {
                    if (!$s->module || !$s->group) continue;
                    $key = $s->group_id . '_' . $s->module_id;
                    if (!isset($classes[$key])) {
                        $classes[$key] = [
                            'group' => $s->group,
                            'module' => $s->module,
                            'professor' => $professor,
                        ];
                    }
                }
            }
        } else {
            // Admins can see all classrooms
            $schedules = \App\Models\Schedule::with(['module', 'group', 'professor.user'])->get();
            foreach ($schedules as $s) {
                if (!$s->module || !$s->group) continue;
                $key = $s->group_id . '_' . $s->module_id;
                if (!isset($classes[$key])) {
                    $classes[$key] = [
                        'group' => $s->group,
                        'module' => $s->module,
                        'professor' => $s->professor,
                    ];
                }
            }
        }

        // Enrich each class with post stats
        foreach ($classes as &$class) {
            $postsQuery = ClassroomPost::where('group_id', $class['group']->id)
                ->where('module_id', $class['module']->id);
            $class['post_count'] = $postsQuery->count();
            $class['file_count'] = (clone $postsQuery)->whereNotNull('file_path')->count();
            $class['last_post']  = $postsQuery->orderByDesc('created_at')->first();
        }
        unset($class);

        return view('classroom.index', compact('classes'));
    }

    public function showClassroom($groupId, $moduleId)
    {
        $group = Group::findOrFail($groupId);
        $module = Module::findOrFail($moduleId);
        
        $this->authorizeClassroomAccess($groupId, $moduleId);

        // Historical posts
        $posts = ClassroomPost::where('group_id', $groupId)
            ->where('module_id', $moduleId)
            ->with(['user', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Homeworks
        $homeworks = ClassroomHomework::where('group_id', $groupId)
            ->where('module_id', $moduleId)
            ->with(['submissions.student.user'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Messages de chat
        $messages = ClassroomMessage::where('group_id', $groupId)
            ->where('module_id', $moduleId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('classroom.show', compact('posts', 'group', 'module', 'homeworks', 'messages'));
    }

    public function storePost(Request $request, $groupId, $moduleId)
    {
        $group = Group::findOrFail($groupId);
        $module = Module::findOrFail($moduleId);
        
        $this->authorizeClassroomAccess($groupId, $moduleId);

        $validated = $request->validate([
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,png,docx,pptx,zip|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('classroom_files');
        }

        $post = ClassroomPost::create([
            'user_id' => Auth::id(),
            'group_id' => $groupId,
            'module_id' => $moduleId,
            'title' => '',
            'content' => $validated['content'],
            'file_path' => $filePath,
        ]);

        if ($request->has('notify_students')) {
            $group = Group::with('students.user')->find($groupId);
            $module = Module::find($moduleId);
            $notifMessage = $filePath ? "📎 Nouveau support déposé dans {$module->name}" : "📣 Nouvelle annonce dans {$module->name}";
            $notifUrl = route('classroom.show', [$groupId, $moduleId]);

            foreach ($group->students as $student) {
                if ($student->user) {
                    $student->user->notify(new \App\Notifications\AcademicNotification(
                        $notifMessage,
                        'info',
                        $notifUrl
                    ));
                }
            }
        }

        return back()->with('success', 'Publication partagée avec succès.');
    }

    public function storeComment(Request $request, ClassroomPost $post)
    {
        $this->authorizeClassroomAccess($post->group_id, $post->module_id);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'classroom_post_id' => $post->id,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Commentaire ajouté.');
    }

    /* ----------------------------------------------------
       📚 OPTION A: DEVOIRS & SOUMISSIONS METHODS
    ---------------------------------------------------- */

    public function storeHomework(Request $request, $groupId, $moduleId)
    {
        $this->authorizeClassroomAccess($groupId, $moduleId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'attachment' => 'nullable|file|mimes:pdf,zip,jpg,png,docx,xlsx|max:15360',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('homework_attachments');
        }

        ClassroomHomework::create([
            'professor_id' => Auth::user()->professor->id,
            'group_id' => $groupId,
            'module_id' => $moduleId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
            'due_date' => $validated['due_date'],
        ]);

        // Notify students
        $group = Group::with('students.user')->find($groupId);
        $module = Module::find($moduleId);
        foreach ($group->students as $student) {
            $student->user?->notify(new \App\Notifications\AcademicNotification(
                "📅 Nouveau devoir de {$module->name} publié : {$validated['title']}",
                'info',
                route('classroom.show', [$groupId, $moduleId])
            ));
        }

        return back()->with('success', 'Le devoir a été publié avec succès.');
    }

    public function storeSubmission(Request $request, ClassroomHomework $homework)
    {
        $this->authorizeClassroomAccess($homework->group_id, $homework->module_id);

        $validated = $request->validate([
            'submission_file' => 'required|file|mimes:pdf,zip,rar,doc,docx|max:30720', // Max 30MB
        ]);

        $filePath = $request->file('submission_file')->store('classroom_submissions');

        ClassroomSubmission::updateOrCreate([
            'classroom_homework_id' => $homework->id,
            'student_id' => Auth::user()->student->id,
        ], [
            'file_path' => $filePath,
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Votre travail a été rendu avec succès !');
    }

    public function gradeSubmission(Request $request, ClassroomSubmission $submission)
    {
        $homework = $submission->homework;
        $this->authorizeClassroomAccess($homework->group_id, $homework->module_id);

        if (!Auth::user()->isProfessor() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'grade' => 'required|numeric|between:0,20',
            'professor_comment' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'grade' => $validated['grade'],
            'professor_comment' => $validated['professor_comment'],
        ]);

        // Notify student of grading
        $student = $submission->student;
        $student->user?->notify(new \App\Notifications\AcademicNotification(
            "📝 Votre devoir '{$homework->title}' a été noté : {$validated['grade']}/20",
            'success',
            route('classroom.show', [$homework->group_id, $homework->module_id])
        ));

        return back()->with('success', 'La note a été attribuée avec succès.');
    }

    public function downloadHomeworkFile(ClassroomHomework $homework)
    {
        $this->authorizeClassroomAccess($homework->group_id, $homework->module_id);

        if (!$homework->attachment_path || !Storage::exists($homework->attachment_path)) {
            abort(404, 'Pièce jointe introuvable.');
        }

        return Storage::download($homework->attachment_path);
    }

    public function downloadSubmissionFile(ClassroomSubmission $submission)
    {
        $homework = $submission->homework;
        $this->authorizeClassroomAccess($homework->group_id, $homework->module_id);

        if (!$submission->file_path || !Storage::exists($submission->file_path)) {
            abort(404, 'Fichier rendu introuvable.');
        }

        return Storage::download($submission->file_path);
    }

    /* ----------------------------------------------------
       💬 OPTION B: CHAT DE GROUPE METHODS
    ---------------------------------------------------- */

    public function getMessages($groupId, $moduleId)
    {
        $this->authorizeClassroomAccess($groupId, $moduleId);

        $messages = ClassroomMessage::where('group_id', $groupId)
            ->where('module_id', $moduleId)
            ->with(['user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages->map(fn($m) => [
            'id' => $m->id,
            'user_name' => $m->user->name,
            'user_id' => $m->user_id,
            'message' => $m->message,
            'file_name' => $m->file_path ? basename($m->file_path) : null,
            'file_url' => $m->file_path ? route('classroom.chat.download', $m->id) : null,
            'time' => $m->created_at->format('H:i'),
        ]));
    }

    public function postMessage(Request $request, $groupId, $moduleId)
    {
        $this->authorizeClassroomAccess($groupId, $moduleId);

        $validated = $request->validate([
            'message' => 'required|string',
            'chat_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,zip|max:10240', // Max 10MB
        ]);

        $filePath = null;
        if ($request->hasFile('chat_file')) {
            $filePath = $request->file('chat_file')->store('classroom_chat_files');
        }

        ClassroomMessage::create([
            'user_id' => Auth::id(),
            'group_id' => $groupId,
            'module_id' => $moduleId,
            'message' => $validated['message'],
            'file_path' => $filePath,
        ]);

        return response()->json(['success' => true]);
    }

    public function downloadChatFile(ClassroomMessage $message)
    {
        $this->authorizeClassroomAccess($message->group_id, $message->module_id);

        if (!$message->file_path || !Storage::exists($message->file_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::download($message->file_path);
    }

    /* ----------------------------------------------------
       PRIVATE HELPERS & SECURITY
    ---------------------------------------------------- */

    private function authorizeClassroomAccess($groupId, $moduleId)
    {
        $user = Auth::user();

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return;
        }

        $isAuthorizedStudent = ClassroomAuthorization::authorizeStudent($user, $groupId);
        $isAuthorizedProfessor = ClassroomAuthorization::authorizeProfessor($user, $groupId, $moduleId);

        if (!$isAuthorizedStudent && !$isAuthorizedProfessor) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette classe.');
        }
    }

    public function downloadFile(ClassroomPost $post)
    {
        $this->authorizeClassroomAccess($post->group_id, $post->module_id);
        
        if (!$post->file_path || !Storage::exists($post->file_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return Storage::download($post->file_path);
    }
}
