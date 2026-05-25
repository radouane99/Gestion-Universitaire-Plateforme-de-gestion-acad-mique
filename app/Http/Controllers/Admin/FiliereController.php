<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FiliereController extends Controller
{
    public function index()
    {
        $filieres = Filiere::withCount(['groups', 'modules'])->get();
        return view('admin.filieres.index', compact('filieres'));
    }

    public function create()
    {
        return view('admin.filieres.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:filieres',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $filiere = Filiere::create($validated);

        ActivityLog::log(
            'created',
            'Filiere',
            "Création de la filière '{$filiere->name}' (Code: {$filiere->code}) par l'administration (" . (auth()->user()->name ?? '') . ")"
        );

        return redirect()->route('admin.filieres.index')->with('success', 'Filière créée avec succès.');
    }

    public function edit(Filiere $filiere)
    {
        return view('admin.filieres.edit', compact('filiere'));
    }

    public function update(Request $request, Filiere $filiere)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:filieres,code,' . $filiere->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $filiere->update($validated);

        ActivityLog::log(
            'updated',
            'Filiere',
            "Modification de la filière '{$filiere->name}' par l'administration (" . (auth()->user()->name ?? '') . ")"
        );

        return redirect()->route('admin.filieres.index')->with('success', 'Filière modifiée avec succès.');
    }

    public function destroy(Filiere $filiere)
    {
        $name = $filiere->name;
        
        if ($filiere->groups()->count() > 0 || $filiere->modules()->count() > 0) {
            return redirect()->route('admin.filieres.index')->with('error', 'Impossible de supprimer cette filière car elle contient des groupes ou des modules.');
        }

        $filiere->delete();

        ActivityLog::log(
            'deleted',
            'Filiere',
            "Suppression de la filière '{$name}' par l'administration (" . (auth()->user()->name ?? '') . ")"
        );

        return redirect()->route('admin.filieres.index')->with('success', 'Filière supprimée avec succès.');
    }
}
