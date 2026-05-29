<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <style>
        table {
            border-collapse: collapse;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
        }
        th {
            border: 1px solid #cbd5e1;
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            height: 35px;
        }
        td {
            border: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: middle;
            height: 28px;
            padding: 4px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            text-align: center;
            height: 40px;
        }
        .header-subtitle {
            font-size: 12px;
            font-weight: bold;
            color: #475569;
            text-align: center;
            height: 30px;
        }
        .bg-v {
            background-color: #d1fae5;
            color: #065f46;
        }
        .bg-var {
            background-color: #f3e8ff;
            color: #5b21b6;
        }
        .bg-r {
            background-color: #fef3c7;
            color: #92400e;
        }
        .bg-nv {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .bg-ac {
            background-color: #dbeafe;
            color: #1e3a8a;
        }
        .bg-abs {
            background-color: #f1f5f9;
            color: #64748b;
        }
        .bg-discipline {
            background-color: #fca5a5;
            color: #7f1d1d;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="{{ 4 + ($modules->count() * 3) + ($semesters->count() * 2) + 3 }}" class="header-title">
                UNIVERSITÉ DE PORTFOLIO (UPF) — PROCÈS-VERBAL ANNUEL (GLOBAL)
            </td>
        </tr>
        <tr>
            <td colspan="{{ 4 + ($modules->count() * 3) + ($semesters->count() * 2) + 3 }}" class="header-subtitle">
                Filière : {{ $filiere->name }} | Niveau : {{ $selectedLevel }}ème Année | Année Académique : {{ $academicYear->name }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ 4 + ($modules->count() * 3) + ($semesters->count() * 2) + 3 }}" height="15"></td>
        </tr>
        
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #1e3a8a;">N°</th>
                <th rowspan="2" style="background-color: #1e3a8a;">CIN</th>
                <th rowspan="2" style="background-color: #1e3a8a;">CNE / MASSAR</th>
                <th rowspan="2" style="background-color: #1e3a8a; min-width: 180px;">Nom & Prénom</th>
                
                @foreach($modules as $module)
                    <th colspan="3" style="background-color: #0369a1;">
                        {{ $module->code }} : {{ $module->name }} (Coef: {{ $module->coefficient ?? 1 }})
                    </th>
                @endforeach

                @foreach($semesters as $sem)
                    <th colspan="2" style="background-color: #7c3aed;">
                        Semestre {{ $sem->name }}
                    </th>
                @endforeach

                <th colspan="2" style="background-color: #db2777;">
                    Bilan Annuel
                </th>
                <th rowspan="2" style="background-color: #475569;">Observations</th>
            </tr>
            <tr>
                @foreach($modules as $module)
                    <th style="background-color: #0284c7;">Moyenne</th>
                    <th style="background-color: #0284c7;">Décision</th>
                    <th style="background-color: #0284c7;">Date Val./Ex.</th>
                @endforeach

                @foreach($semesters as $sem)
                    <th style="background-color: #6d28d9;">Moyenne</th>
                    <th style="background-color: #6d28d9;">Décision</th>
                @endforeach

                <th style="background-color: #be185d;">Moyenne</th>
                <th style="background-color: #be185d;">Décision</th>
            </tr>
        </thead>
        
        <tbody>
            @foreach($students as $idx => $student)
                @php
                    $data = $pvData[$student->id];
                    $rowClass = $data['is_disciplinary'] ? 'bg-discipline' : '';
                @endphp
                <tr>
                    <td class="text-center {{ $rowClass }}">{{ $idx + 1 }}</td>
                    <td class="text-center {{ $rowClass }}">{{ $student->cin ?? 'N/A' }}</td>
                    <td class="text-center {{ $rowClass }}">{{ $student->student_number ?? 'N/A' }}</td>
                    <td class="{{ $rowClass }}">{{ $student->user->name ?? 'N/A' }}</td>

                    {{-- Modules --}}
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
                        <td class="text-center {{ $bgClass }}">{{ $modData['final_grade'] !== null ? number_format($modData['final_grade'], 2, ',', ' ') : '-' }}</td>
                        <td class="text-center {{ $bgClass }}">{{ $decision ?? '-' }}</td>
                        <td class="text-center {{ $bgClass }}">{{ $modData['val_date'] ?? '-' }}</td>
                    @endforeach

                    {{-- Semesters --}}
                    @foreach($semesters as $sem)
                        @php
                            $semData = $data['semesters'][$sem->id] ?? null;
                            $semAvg = $semData['average'] ?? null;
                            $semDec = $semData['decision'] ?? '';
                            $semBg = ($semDec === 'V') ? 'bg-v' : (($semDec === 'NV') ? 'bg-nv' : '');
                        @endphp
                        <td class="text-center {{ $semBg }}" style="font-weight: bold;">
                            {{ $semAvg !== null ? number_format($semAvg, 2, ',', ' ') : '-' }}
                        </td>
                        <td class="text-center {{ $semBg }}" style="font-weight: bold;">
                            {{ $semDec ?? '-' }}
                        </td>
                    @endforeach

                    {{-- Annual Bilan --}}
                    @php
                        $annualAvg = $data['annual_average'];
                        $annualDec = $data['annual_decision'];
                        $annualBg = ($annualDec === 'Admis' || $annualDec === 'Diplômé') 
                            ? 'bg-v' 
                            : (($annualDec === 'Admis avec Crédit') 
                                ? 'bg-ac' 
                                : (($annualDec === 'Ajourné') 
                                    ? 'bg-nv' 
                                    : ''));
                    @endphp
                    <td class="text-center {{ $annualBg }}" style="font-weight: bold; font-size: 12px;">
                        {{ $annualAvg !== null ? number_format($annualAvg, 2, ',', ' ') : '-' }}
                    </td>
                    <td class="text-center {{ $annualBg }}" style="font-weight: bold; font-size: 12px;">
                        {{ $annualDec ?? '-' }}
                    </td>

                    <td class="text-center {{ $rowClass }}">
                        {{ $data['observations'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
