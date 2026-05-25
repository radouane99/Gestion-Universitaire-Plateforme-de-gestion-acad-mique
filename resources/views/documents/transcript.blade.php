<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relevé de Notes - UPF</title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #001A4D; margin: 0; padding: 0; }
        .page { width: 210mm; min-height: 297mm; padding: 15mm; margin: auto; border: 1px solid #eee; position: relative; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #003399; padding-bottom: 20px; }
        .title { text-align: center; margin-top: 30px; font-size: 24px; font-weight: 900; text-transform: uppercase; }
        .student-info { margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 8px; font-size: 13px; }
        
        .semester-block { margin-top: 20px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .semester-title { background: #003399; color: white; padding: 10px 15px; font-size: 14px; font-weight: bold; display: flex; justify-content: space-between; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #334155; padding: 8px 15px; text-align: left; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        td { padding: 8px 15px; border-bottom: 1px solid #f1f5f9; font-size: 12px; font-weight: 500; }
        .total-row { background: #f8fafc; font-weight: 900; border-top: 2px solid #e2e8f0; }
        
        .final-result { margin-top: 30px; border: 2px solid #003399; padding: 15px; border-radius: 8px; text-align: center; font-size: 16px; font-weight: bold; }
        .footer { position: absolute; bottom: 30px; left: 30px; right: 30px; font-size: 9px; text-align: center; color: #94a3b8; }
        
        @media print {
            .print\:hidden { display: none !important; }
            .page { border: none; padding: 0; margin: 0; width: auto; min-height: auto; }
            body { background: white; }
        }
    </style>
</head>
<body>
    <!-- Print Button Widget -->
    <div class="print:hidden" style="position: fixed; top: 30px; right: 30px; z-index: 9999;">
        <button onclick="window.print()" style="background-color: #003399; color: white; padding: 14px 28px; border: none; border-radius: 16px; font-weight: 800; cursor: pointer; font-size: 14px; box-shadow: 0 10px 25px -5px rgba(0, 51, 153, 0.4); display: flex; align-items: center; gap: 10px; transition: transform 0.2s; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Imprimer le Relevé
        </button>
    </div>

    <div class="page">
        <div class="header">
            <div>
                <div style="font-size: 24px; font-weight: 900; color: #003399;">UPF</div>
                <div style="font-size: 8px; font-weight: bold; text-transform: uppercase;">Université Privée de Fès</div>
            </div>
            <div style="text-align: right; font-size: 10px;">
                Année: {{ $student && $student->academicYear ? $student->academicYear->name : '2024 / 2025' }}<br>
                Session: Principale
            </div>
        </div>

        <h1 class="title">Relevé de Notes Annuel</h1>

        <div class="student-info">
            Nom & Prénom : <b>{{ $request->user->name }}</b><br>
            N° Étudiant : <b>STU-{{ $request->user->id }}{{ $request->user->id + 200 }}</b><br>
            Filière : <b>{{ $student && $student->group && $student->group->filiere ? $student->group->filiere->name : 'Systèmes d\'Information & Génie Logiciel' }}</b>
        </div>

        @forelse($gradesBySemester as $semesterName => $grades)
        <div class="semester-block">
            @php
                $semGradesCount = $grades->whereNotNull('final_grade')->count();
                $semGPA = $semGradesCount > 0 ? $grades->whereNotNull('final_grade')->avg('final_grade') : 0;
            @endphp
            <div class="semester-title">
                <span>{{ $semesterName }}</span>
                <span>Moyenne: {{ number_format($semGPA, 2) }}</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Module ID</th>
                        <th>Intitulé du Module</th>
                        <th style="text-align: center;">Note / 20</th>
                        <th style="text-align: center;">Résultat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grades as $grade)
                    <tr>
                        <td>{{ $grade->module->code ?? 'MOD-' . $grade->module->id }}</td>
                        <td>{{ $grade->module->name }}</td>
                        <td style="text-align: center;">{{ $grade->final_grade ? number_format($grade->final_grade, 2) : '--' }}</td>
                        <td style="text-align: center;">{{ $grade->final_grade >= 10 ? 'Validé' : ($grade->final_grade !== null ? 'Rattrapage' : 'En Cours') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @empty
        <div style="margin-top: 30px; text-align: center; color: #999; font-style: italic;">
            Aucune note enregistrée pour le moment.
        </div>
        @endforelse

        <div class="final-result">
            Résultat de l'Année : MOYENNE GÉNÉRALE = <span style="color: {{ $yearlyGPA >= 10 ? '#059669' : '#e11d48' }};">{{ number_format($yearlyGPA, 2) }} / 20</span>
            <br>
            <span style="font-size: 14px; margin-top: 5px; display: block; color: #64748b;">
                Décision du Jury : {{ $yearlyGPA >= 10 ? 'ADMIS(E) AU NIVEAU SUPÉRIEUR' : ($yearlyGPA > 0 ? 'AJOURNÉ(E)' : 'EN COURS') }}
            </span>
        </div>

        <div style="margin-top: 50px; font-size: 12px; font-style: italic;">
            Note: Ce document est une copie numérique officielle générée par le portail UPF.
        </div>

        <div class="footer">
            UPF - Excellence Hub - Fès, Maroc - contact@upf.ac.ma
        </div>
    </div>
</body>
</html>
