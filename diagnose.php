<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class);
\Illuminate\Support\Facades\DB::connection();

$exam = \App\Models\Exam::first();
if (!$exam) {
    echo "No exam found\n";
    exit;
}
echo "Exam ID: {$exam->id}\n";
echo "Exam group_id: {$exam->group_id}\n";

$students = \App\Models\Student::where('group_id', $exam->group_id)->get();
echo "Students in group: " . $students->count() . "\n";

foreach ($students as $s) {
    echo " - Student ID: {$s->id}, user_id: {$s->user_id}\n";
}
