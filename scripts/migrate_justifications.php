<?php
/**
 * migrate_justifications.php
 *
 * Moves legacy justification PDFs from the public web‑accessible folder
 * to the private local storage disk and updates the DB references.
 *
 * Usage (from the project root):
 *   php scripts\migrate_justifications.php
 *
 * --------------------------------------------------------------------
 * IMPORTANT:
 *   * Run this **once** after pulling the latest code.
 *   * Ensure the DB is reachable (check .env DB settings).
 *   * Verify that the public/justifications folder contains ONLY the
 *     files you want to migrate – the script will delete them after
 *     moving.
 * --------------------------------------------------------------------
 */

declare(strict_types=1);

// ---------------------------
// Bootstrap Laravel
// ---------------------------
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ---------------------------
// Helpers
// ---------------------------
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

$publicDir   = base_path('public/justifications');
$privateDisk = Storage::disk('local');               // storage/app
$privatePath = 'justifications';                    // relative to storage/app

echo "=== Justification migration start ===\n";
echo "Public folder: {$publicDir}\n";
echo "Target storage disk: local/{$privatePath}\n\n";

// -----------------------------------------------------------------
// 1. Verify source folder exists and contains files
// -----------------------------------------------------------------
if (!File::exists($publicDir) || !File::isDirectory($publicDir)) {
    echo "❗ The folder {$publicDir} does not exist or is not a directory.\n";
    exit(1);
}

$files = File::files($publicDir);
if (empty($files)) {
    echo "✅ No files to migrate – the folder is already empty.\n";
    exit(0);
}

// -----------------------------------------------------------------
// 2. Process each file
// -----------------------------------------------------------------
$processed = 0;
foreach ($files as $file) {
    $filename = $file->getFilename();

    // 2a. Move file to storage/app/justifications
    $newRelativePath = $privatePath . '/' . $filename;
    $newFullPath     = storage_path('app/' . $newRelativePath);

    // Ensure destination directory exists
    if (!File::exists(dirname($newFullPath))) {
        File::makeDirectory(dirname($newFullPath), 0755, true);
    }

    // Move the file
    File::move($file->getRealPath(), $newFullPath);
    echo "📁 Moved {$filename} → storage/app/{$newRelativePath}\n";

    // 2b. Update DB rows that reference this file
    // We assume the column stores the *filename* (not the full public URL).
    $affected = DB::table('absences')
        ->where('justification_path', $filename)
        ->orWhere('justification_path', "justifications/{$filename}")
        ->update(['justification_path' => $newRelativePath]);

    echo "   ↳ Updated {$affected} DB row(s)\n";
    $processed++;
}

// -----------------------------------------------------------------
// 3. Clean up the now‑empty public folder
// -----------------------------------------------------------------
if (File::isDirectory($publicDir) && empty(File::files($publicDir))) {
    File::deleteDirectory($publicDir);
    echo "\n🧹 Deleted empty folder: public/justifications\n";
} else {
    echo "\n⚠️  Folder not empty after migration – manual check recommended.\n";
}

echo "\n=== Migration completed: {$processed} file(s) processed ===\n";
