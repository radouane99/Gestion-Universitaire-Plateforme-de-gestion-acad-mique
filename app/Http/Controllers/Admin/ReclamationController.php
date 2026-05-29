<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reclamation;
use Illuminate\Http\Request;

class ReclamationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reclamation::with(['student.user', 'module', 'exam', 'grade']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reclamations = $query->latest()->paginate(15);

        $stats = [
            'total' => Reclamation::count(),
            'pending' => Reclamation::where('status', 'pending')->count(),
            'resolved' => Reclamation::whereIn('status', ['accepted', 'rejected'])->count(),
        ];

        return view('admin.reclamations.index', compact('reclamations', 'stats'));
    }
}
