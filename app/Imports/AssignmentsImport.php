<?php

namespace App\Imports;

use App\Models\Assignment;
use App\Models\Professor;
use App\Models\Module;
use App\Models\Group;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\UploadedFile;

class AssignmentsImport
{
    protected $academicYearId;
    public int $importedCount = 0;
    public int $skippedCount  = 0;
    public array $errors      = [];

    public function __construct(int $academicYearId)
    {
        $this->academicYearId = $academicYearId;
    }

    public function import(UploadedFile $file): void
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, false);

        if (empty($rows)) return;

        // Detect header row (row 0) and build column map
        $headers = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
        $colMap  = array_flip($headers);

        $emailCol  = $colMap['email_professeur'] ?? $colMap['email'] ?? null;
        $codeCol   = $colMap['module_code'] ?? $colMap['code_module'] ?? null;
        $groupeCol = $colMap['groupe'] ?? $colMap['group'] ?? null;

        if ($emailCol === null || $codeCol === null || $groupeCol === null) {
            $this->errors[] = 'Colonnes requises manquantes. Attendu : email_professeur, module_code, groupe';
            return;
        }

        // Process data rows
        foreach (array_slice($rows, 1) as $index => $row) {
            $email  = trim((string)($row[$emailCol] ?? ''));
            $code   = trim((string)($row[$codeCol] ?? ''));
            $groupe = trim((string)($row[$groupeCol] ?? ''));

            if (empty($email) || empty($code) || empty($groupe)) {
                $this->skippedCount++;
                continue;
            }

            // Resolve professor
            $user = User::where('email', $email)->first();
            if (!$user || !$user->professor) {
                $this->skippedCount++;
                $this->errors[] = "Ligne " . ($index + 2) . ": Professeur introuvable ({$email})";
                continue;
            }

            // Resolve module
            $module = Module::where('code', $code)->first();
            if (!$module) {
                $this->skippedCount++;
                $this->errors[] = "Ligne " . ($index + 2) . ": Module introuvable ({$code})";
                continue;
            }

            // Resolve group
            $group = Group::where('name', $groupe)->first();
            if (!$group) {
                $this->skippedCount++;
                $this->errors[] = "Ligne " . ($index + 2) . ": Groupe introuvable ({$groupe})";
                continue;
            }

            // Skip duplicates
            $exists = Assignment::where('professor_id', $user->professor->id)
                ->where('module_id', $module->id)
                ->where('group_id', $group->id)
                ->where('academic_year_id', $this->academicYearId)
                ->exists();

            if ($exists) {
                $this->skippedCount++;
                continue;
            }

            Assignment::create([
                'professor_id'     => $user->professor->id,
                'module_id'        => $module->id,
                'group_id'         => $group->id,
                'academic_year_id' => $this->academicYearId,
            ]);

            $this->importedCount++;
        }
    }
}
