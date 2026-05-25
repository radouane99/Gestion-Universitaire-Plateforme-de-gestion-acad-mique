<?php

namespace App\Exports;

use App\Models\Assignment;
use App\Models\AcademicYear;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AssignmentsExport
{
    protected $academicYearId;

    public function __construct($academicYearId = null)
    {
        $this->academicYearId = $academicYearId;
    }

    public function download(string $filename)
    {
        $spreadsheet = $this->build();
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    protected function build(): Spreadsheet
    {
        $query = Assignment::with(['professor.user', 'module', 'group', 'academicYear']);
        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }
        $assignments = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Affectations Professeurs');

        // Headers
        $headers = ['ID', 'Professeur', 'Email Professeur', 'Module (Code)', 'Module (Nom)', 'Groupe', 'Année Universitaire', 'Date Affectation'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Header styles
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Data rows
        foreach ($assignments as $i => $a) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $a->id);
            $sheet->setCellValue("B{$row}", $a->professor->user->name ?? 'N/A');
            $sheet->setCellValue("C{$row}", $a->professor->user->email ?? 'N/A');
            $sheet->setCellValue("D{$row}", $a->module->code ?? 'N/A');
            $sheet->setCellValue("E{$row}", $a->module->name ?? 'N/A');
            $sheet->setCellValue("F{$row}", $a->group->name ?? 'N/A');
            $sheet->setCellValue("G{$row}", $a->academicYear->name ?? 'N/A');
            $sheet->setCellValue("H{$row}", $a->created_at->format('d/m/Y'));

            // Alternating row colors
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$row}:H{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');
            }
        }

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
