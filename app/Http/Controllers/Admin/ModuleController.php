<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;
use App\Models\ActivityLog;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with('filiere')->get();
        return view('admin.modules.index', compact('modules'));
    }

    public function create()
    {
        $filieres = \App\Models\Filiere::all();
        return view('admin.modules.create', compact('filieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:modules',
            'name' => 'required|string|max:255',
            'coefficient' => 'required|numeric|min:1',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $module = Module::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Module',
            'description' => "Création manuelle du module '{$module->name}' (Code: {$module->code}).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.modules.index')->with('success', 'Module créé avec succès.');
    }

    public function destroy(Module $module)
    {
        $moduleName = $module->name;
        $module->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'Module',
            'description' => "Suppression du module '{$moduleName}'.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.modules.index')->with('success', 'Module supprimé avec succès.');
    }

    /**
     * Show import form for CSV/Excel modules.
     */
    public function showImportForm()
    {
        return view('admin.modules.import');
    }

    /**
     * Download CSV template for modules.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="modules_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['code', 'name', 'coefficient']);
            fputcsv($file, ['INF-301', 'Intelligence Artificielle & Réseaux de Neurones', '2.00']);
            fputcsv($file, ['INF-302', 'Bases de Données Avancées (NoSQL & SQL)', '1.50']);
            fputcsv($file, ['INF-303', 'Sécurité Informatique & Cryptographie', '2.00']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import modules from CSV file.
     */
    public function importModules(Request $request)
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
            
            $code = strtoupper(trim($data['code'] ?? ''));
            $name = trim($data['name'] ?? '');
            $coefficient = trim($data['coefficient'] ?? '1.0');
            
            if (empty($code) || empty($name)) {
                $errors[] = "Ligne {$rowNum}: Code et Désignation sont requis.";
                continue;
            }

            if (Module::where('code', $code)->exists()) {
                $errors[] = "Ligne {$rowNum}: Le code '{$code}' existe déjà.";
                continue;
            }

            Module::create([
                'code' => $code,
                'name' => $name,
                'coefficient' => floatval($coefficient),
            ]);

            $importedCount++;
        }

        fclose($handle);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Module',
            'description' => "Importation CSV réussie de {$importedCount} module(s).",
            'ip_address' => $request->ip()
        ]);

        if (count($errors) > 0) {
            $msg = "Importation de {$importedCount} modules complétée avec des avertissements : " . implode(' | ', $errors);
            return redirect()->route('admin.modules.index')->with('warning', $msg);
        }

        return redirect()->route('admin.modules.index')->with('success', "{$importedCount} modules importés avec succès !");
    }

    public function edit(Module $module)
    {
        $filieres = \App\Models\Filiere::all();
        return view('admin.modules.edit', compact('module', 'filieres'));
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:modules,code,' . $module->id,
            'name' => 'required|string|max:255',
            'coefficient' => 'required|numeric|min:1',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $module->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Module',
            'description' => "Mise à jour du module '{$module->name}' (Code: {$module->code}).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.modules.index')->with('success', 'Module mis à jour avec succès.');
    }
}
