<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\ActivityLog;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|string',
        ]);

        $room = Room::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Room',
            'description' => "Création de la salle '{$room->name}' (Type: {$room->type}, Capacité: {$room->capacity} places).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Salle créée avec succès.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'capacity' => 'required|integer|min:1',
            'type' => 'required|string',
        ]);

        $room->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'model_type' => 'Room',
            'description' => "Mise à jour de la salle '{$room->name}' (Type: {$room->type}, Capacité: {$room->capacity} places).",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Salle mise à jour avec succès.');
    }

    public function destroy(Room $room)
    {
        $roomName = $room->name;
        $room->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'model_type' => 'Room',
            'description' => "Suppression de la salle '{$roomName}'.",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Salle supprimée avec succès.');
    }

    /**
     * Show import form for CSV/Excel rooms.
     */
    public function showImportForm()
    {
        return view('admin.rooms.import');
    }

    /**
     * Download CSV template for rooms.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="salles_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['name', 'capacity', 'type']);
            fputcsv($file, ['Amphi Ibn Battouta', '120', 'course']);
            fputcsv($file, ['Salle de TP E-10', '25', 'TP']);
            fputcsv($file, ['Salle TD B-02', '45', 'TD']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import rooms from CSV file.
     */
    public function importRooms(Request $request)
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
            $capacity = trim($data['capacity'] ?? '');
            $type = trim($data['type'] ?? '');
            
            if (empty($name) || empty($capacity) || empty($type)) {
                $errors[] = "Ligne {$rowNum}: Nom, Capacité, et Type sont requis.";
                continue;
            }

            if (Room::where('name', $name)->exists()) {
                $errors[] = "Ligne {$rowNum}: La salle '{$name}' existe déjà.";
                continue;
            }

            Room::create([
                'name' => $name,
                'capacity' => intval($capacity),
                'type' => $type,
            ]);

            $importedCount++;
        }

        fclose($handle);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'model_type' => 'Room',
            'description' => "Importation CSV réussie de {$importedCount} salle(s).",
            'ip_address' => $request->ip()
        ]);

        if (count($errors) > 0) {
            $msg = "Importation de {$importedCount} salles complétée avec des avertissements : " . implode(' | ', $errors);
            return redirect()->route('admin.rooms.index')->with('warning', $msg);
        }

        return redirect()->route('admin.rooms.index')->with('success', "{$importedCount} salles importées avec succès !");
    }
}
