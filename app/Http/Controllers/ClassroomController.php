<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\ClassroomAuthorization;

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
                            'group' => $s->group ?? \App\Models\Group::find($groupId),
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
            $postsQuery = \App\Models\ClassroomPost::where('group_id', $class['group']->id)
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
        $group = \App\Models\Group::findOrFail($groupId);
        $module = \App\Models\Module::findOrFail($moduleId);
        
        $this->authorizeClassroomAccess($groupId, $moduleId);

        $posts = \App\Models\ClassroomPost::where('group_id', $groupId)
            ->where('module_id', $moduleId)
            ->with(['user', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('classroom.show', compact('posts', 'group', 'module'));
    }

    public function storePost(Request $request, $groupId, $moduleId)
    {
        $group = \App\Models\Group::findOrFail($groupId);
        $module = \App\Models\Module::findOrFail($moduleId);
        
        $this->authorizeClassroomAccess($groupId, $moduleId);

        $validated = $request->validate([
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,png,docx,pptx,zip|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('classroom_files');
        }

        $post = \App\Models\ClassroomPost::create([
            'user_id' => Auth::id(),
            'group_id' => $groupId,
            'module_id' => $moduleId,
            'title' => '',
            'content' => $validated['content'],
            'file_path' => $filePath,
        ]);

        if ($request->has('notify_students')) {
            $group = \App\Models\Group::with('students.user')->find($groupId);
            $module = \App\Models\Module::find($moduleId);
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

    public function storeComment(Request $request, \App\Models\ClassroomPost $post)
    {
        $this->authorizeClassroomAccess($post->group_id, $post->module_id);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        \App\Models\Comment::create([
            'user_id' => Auth::id(),
            'classroom_post_id' => $post->id,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Commentaire ajouté.');
    }

    /**
     * Authorize classroom access.
     * Note: Admins have full access to view, post, and comment everywhere.
     * Students must belong to the group, and professors must teach the class.
     */
    private function authorizeClassroomAccess($groupId, $moduleId)
    {
        $user = Auth::user();

        // Les admins ont explicitement le droit de tout faire (lire, publier, commenter)
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return;
        }

        $isAuthorizedStudent = ClassroomAuthorization::authorizeStudent($user, $groupId);
        $isAuthorizedProfessor = ClassroomAuthorization::authorizeProfessor($user, $groupId, $moduleId);

        if (!$isAuthorizedStudent && !$isAuthorizedProfessor) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette classe ou publier/commenter.');
        }
    }

    public function downloadFile(\App\Models\ClassroomPost $post)
    {
        $this->authorizeClassroomAccess($post->group_id, $post->module_id);
        
        if (!$post->file_path || !\Illuminate\Support\Facades\Storage::exists($post->file_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return \Illuminate\Support\Facades\Storage::download($post->file_path);
    }
}
