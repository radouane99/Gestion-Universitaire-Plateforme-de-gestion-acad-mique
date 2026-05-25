<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$examsCount = \App\Models\Exam::count();
$convocationsCount = \App\Models\Convocation::count();
$sessions = \App\Models\ExamSession::all();

echo "Total Exams: $examsCount\n";
echo "Total Convocations: $convocationsCount\n";
foreach ($sessions as $s) {
    $eCount = \App\Models\Exam::where('exam_session_id', $s->id)->count();
    echo "Session ID {$s->id} ({$s->name}): $eCount exams\n";
}
