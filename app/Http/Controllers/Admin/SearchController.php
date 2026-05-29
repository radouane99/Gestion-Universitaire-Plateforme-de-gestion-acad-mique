<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Module;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function query(Request $request)
    {
        $term = $request->input('q');
        if (!$term) return response()->json([]);

        $students = Student::whereHas('user', function($q) use ($term) {
            $q->where('name', 'like', "%$term%");
        })->with('user')->get()->map(fn($s) => [
            'title' => $s->user->name,
            'type' => 'Student',
            'url' => route('admin.users.index') // Simplified for now
        ]);

        $professors = Professor::whereHas('user', function($q) use ($term) {
            $q->where('name', 'like', "%$term%");
        })->with('user')->get()->map(fn($s) => [
            'title' => $s->user->name,
            'type' => 'Professor',
            'url' => route('admin.users.index')
        ]);

        $modules = Module::where('name', 'like', "%$term%")
            ->get()->map(fn($m) => [
            'title' => $m->name,
            'type' => 'Module',
            'url' => route('admin.modules.index')
        ]);

        $rooms = Room::where('name', 'like', "%$term%")
            ->get()->map(fn($r) => [
            'title' => $r->name,
            'type' => 'Salle',
            'url' => route('admin.rooms.index')
        ]);

        $requests = \App\Models\Request::where('type', 'like', "%$term%")
            ->orWhereHas('user', function($q) use ($term) {
                $q->where('name', 'like', "%$term%");
            })
            ->with('user')
            ->get()->map(fn($req) => [
            'title' => $req->type . ' - ' . ($req->user->name ?? ''),
            'type' => 'Demande',
            'url' => route('admin.requests.index')
        ]);

        return response()->json($students->concat($professors)->concat($modules)->concat($rooms)->concat($requests));
    }
}
