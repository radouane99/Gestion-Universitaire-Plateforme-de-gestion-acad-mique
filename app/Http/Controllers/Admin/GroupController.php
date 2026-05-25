<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\ActivityLog;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('filiere')->get();
        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        $filieres = \App\Models\Filiere::all();
        return view('admin.groups.create', compact('filieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'level' => 'required|string|max:255',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $group = Group::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Group',
            'description' => "Création du groupe '{$group->name}' (Niveau: {$group->level}).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.groups.index')->with('success', 'Groupe créé avec succès.');
    }

    public function edit(Group $group)
    {
        $filieres = \App\Models\Filiere::all();
        return view('admin.groups.edit', compact('group', 'filieres'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
            'level' => 'required|string|max:255',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $group->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Group',
            'description' => "Mise à jour du groupe '{$group->name}' (Niveau: {$group->level}).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.groups.index')->with('success', 'Groupe mis à jour avec succès.');
    }

    public function destroy(Group $group)
    {
        $groupName = $group->name;
        $group->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'Group',
            'description' => "Suppression du groupe '{$groupName}'.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.groups.index')->with('success', 'Groupe supprimé avec succès.');
    }

    /**
     * Show import form for CSV/Excel groups.
     */
    public function showImportForm()
    {
        return view('admin.groups.import');
    }

    /**
     * Download CSV template for groups.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="groupes_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['name', 'level']);
            fputcsv($file, ['Génie Informatique 1 (GI-1)', 'L1']);
            fputcsv($file, ['Ingénierie des Données & Systèmes 1 (IDS-1)', 'L3']);
            fputcsv($file, ['Cybersécurité & Cloud 2 (CSC-2)', 'M1']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import groups from CSV file.
     */
    public function importGroups(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('import_file');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            return back()->with('error', 'Le fichier CSV est vide ou invalide.');
        }

        $header = array_map(function($col) {
            return strtolower(trim($col));
        }, $header);

        $importedCount = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;
            if (count($row) < count($header)) {
                $errors[] = "Ligne {$rowNum}: colonnes manquantes.";
                continue;
            }

            $data = array_combine($header, array_slice($row, 0, count($header)));
            
            $name = trim($data['name'] ?? '');
            $level = strtoupper(trim($data['level'] ?? ''));
            
            if (empty($name) || empty($level)) {
                $errors[] = "Ligne {$rowNum}: Nom et Niveau d'étude sont requis.";
                continue;
            }

            if (Group::where('name', $name)->exists()) {
                $errors[] = "Ligne {$rowNum}: Le groupe '{$name}' existe déjà.";
                continue;
            }

            Group::create([
                'name' => $name,
                'level' => $level,
            ]);

            $importedCount++;
        }

        fclose($handle);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Group',
            'description' => "Importation CSV réussie de {$importedCount} groupe(s).",
            'ip_address' => $request->ip()
        ]);

        if (count($errors) > 0) {
            $msg = "Importation de {$importedCount} groupes complétée avec des avertissements : " . implode(' | ', $errors);
            return redirect()->route('admin.groups.index')->with('warning', $msg);
        }

        return redirect()->route('admin.groups.index')->with('success', "{$importedCount} groupes importés avec succès !");
    }
}
