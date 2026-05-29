<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Procès-Verbal Semestriel</title>
    <style>
        @page {
            margin: 15px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 7px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 5px;
        }
        .header-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .header-meta {
            text-align: right;
            font-size: 8px;
            color: #64748b;
            font-weight: bold;
        }
        .pv-table {
            width: 100%;
            border-collapse: collapse;
            border: 0.5px solid #94a3b8;
        }
        .pv-table th {
            border: 0.5px solid #94a3b8;
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 4px 2px;
            font-size: 6px;
        }
        .pv-table td {
            border: 0.5px solid #cbd5e1;
            padding: 3px 2px;
            text-align: left;
            vertical-align: middle;
        }
        .text-center {
            text-align: center !important;
        }
        .font-mono {
            font-family: Courier, monospace;
            font-size: 6.5px;
        }
        .bg-v {
            background-color: #d1fae5 !important;
            color: #065f46;
        }
        .bg-var {
            background-color: #f3e8ff !important;
            color: #5b21b6;
        }
        .bg-r {
            background-color: #fef3c7 !important;
            color: #92400e;
        }
        .bg-nv {
            background-color: #fee2e2 !important;
            color: #991b1b;
        }
        .bg-abs {
            background-color: #f1f5f9 !important;
            color: #64748b;
        }
        .bg-discipline {
            background-color: #fca5a5 !important;
            color: #7f1d1d;
            font-weight: bold;
        }
        .footer {
            margin-top: 15px;
            width: 100%;
            font-size: 7px;
            color: #64748b;
        }
        .signatures-table {
            width: 100%;
            margin-top: 25px;
        }
        .signatures-table td {
            border: none;
            width: 33.33%;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            height: 50px;
            vertical-align: top;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="border:none;">
                <div class="header-title">UNIVERSITÉ DE PORTFOLIO (UPF)</div>
                <div style="font-size:8px; font-weight:bold; color: #db2777; margin-top:2px;">
                    FACULTÉ DES SCIENCES & DE L'ÉDUCATION
                </div>
            </td>
            <td style="border:none;" class="header-meta">
                PROCÈS-VERBAL SEMESTRIEL — SEMESTRE {{ $semester->name }}<br>
                Année Académique : {{ $academicYear->name }} | Filière : {{ $filiere->name }}
            </td>
        </tr>
    </table>

    {{-- Main PV Grid --}}
    <table class="pv-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2%;">N°</th>
                <th rowspan="2" style="width: 6%;">CIN</th>
                <th rowspan="2" style="width: 8%;">CNE</th>
                <th rowspan="2" style="width: 12%; text-align: left; padding-left: 4px;">Nom & Prénom</th>
                
                @foreach($modules as $module)
                    <th colspan="3" style="width: {{ 66 / $modules->count() }}%;">
                        {{ $module->code }}
                    </th>
                @endforeach

                <th colspan="2" style="width: 8%; background-color: #be185d;">
                    S{{ $semester->name }}
                </th>
                <th rowspan="2" style="width: 8%;">Observations</th>
            </tr>
            <tr>
                @foreach($modules as $module)
                    <th style="font-size: 5px; font-weight: normal;">Moy.</th>
                    <th style="font-size: 5px; font-weight: normal;">Déc.</th>
                    <th style="font-size: 5px; font-weight: normal;">Dt. Val./Ex.</th>
                @endforeach
                <th style="background-color: #db2777; font-size: 5.5px;">Moy.</th>
                <th style="background-color: #db2777; font-size: 5.5px;">Déc.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $idx => $student)
                @php
                    $data = $pvData[$student->id];
                    $rowClass = $data['is_disciplinary'] ? 'bg-discipline' : '';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center {{ $rowClass }} font-mono">{{ $idx + 1 }}</td>
                    <td class="text-center {{ $rowClass }} font-mono">{{ $student->cin ?? 'N/A' }}</td>
                    <td class="text-center {{ $rowClass }} font-mono">{{ $student->student_number ?? 'N/A' }}</td>
                    <td class="{{ $rowClass }}" style="font-weight: bold; padding-left: 4px; text-transform: uppercase;">
                        {{ $student->user->name ?? 'N/A' }}
                    </td>

                    @foreach($modules as $module)
                        @php
                            $modData = $data['modules'][$module->id] ?? null;
                            $decision = $modData['decision'] ?? '';
                            $bgClass = '';
                            
                            if ($decision === 'V') {
                                $bgClass = 'bg-v';
                            } elseif ($decision === 'VAR') {
                                $bgClass = 'bg-var';
                            } elseif ($decision === 'R') {
                                $bgClass = 'bg-r';
                            } elseif ($decision === 'NV') {
                                $bgClass = 'bg-nv';
                            } elseif ($decision === 'ABS') {
                                $bgClass = 'bg-abs';
                            }
                        @endphp
                        <td class="text-center font-mono {{ $bgClass }}">{{ $modData['final_grade'] !== null ? number_format($modData['final_grade'], 2, ',', ' ') : '-' }}</td>
                        <td class="text-center {{ $bgClass }}" style="font-weight: bold;">{{ $decision ?? '-' }}</td>
                        <td class="text-center font-mono {{ $bgClass }}">{{ $modData['val_date'] ?? '-' }}</td>
                    @endforeach

                    @php
                        $semData = $data['semesters'][$semester->id] ?? null;
                        $semAvg = $semData['average'] ?? null;
                        $semDec = $semData['decision'] ?? '';
                        $semBg = ($semDec === 'V') ? 'bg-v' : (($semDec === 'NV') ? 'bg-nv' : '');
                    @endphp
                    <td class="text-center font-mono {{ $semBg }}" style="font-weight: bold;">
                        {{ $semAvg !== null ? number_format($semAvg, 2, ',', ' ') : '-' }}
                    </td>
                    <td class="text-center {{ $semBg }}" style="font-weight: bold;">
                        {{ $semDec ?? '-' }}
                    </td>

                    <td class="text-center {{ $rowClass }}" style="font-size: 5px; font-weight: bold;">
                        {{ $data['observations'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer Info --}}
    <table class="footer">
        <tr>
            <td style="border: none;">Date d'édition : {{ now()->format('d/m/Y H:i:s') }}</td>
            <td style="border: none; text-align: right;">UPF Gestion Académique — Plateforme Intégrée</td>
        </tr>
    </table>

    {{-- Signatures --}}
    <table class="signatures-table">
        <tr>
            <td>Le Jury d'Examen</td>
            <td>Le Directeur de l'Établissement</td>
            <td>Le Président de l'Université</td>
        </tr>
    </table>

</body>
</html>
