<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convocation — {{ $convocation->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #000; background: #fff; font-size: 12px; line-height: 1.4; }
        .page { padding: 25px 35px; }
    </style>
</head>
<body>
@php
    $student = $convocation->student;
    $currentExam = $convocation->exam;
    $session = $currentExam->examSession;

    // Fetch all convocations of this student for this session to list all their exams
    $allConvocations = \App\Models\Convocation::where('student_id', $student->id)
        ->whereHas('exam', function ($query) use ($session) {
            $query->where('exam_session_id', $session->id);
        })
        ->with(['exam.module', 'exam.room'])
        ->get()
        ->sortBy(function ($c) {
            return $c->exam->date . ' ' . $c->exam->start_time;
        });

    $academicYearName = $session->academicYear->name ?? '2025-2026';
    $sessionName = $session->name; // e.g. "Normale Automne"

    $levelText = match(intval($student->group->level ?? 0)) {
        1 => 'Première année',
        2 => 'Deuxième année',
        3 => 'Troisième année',
        4 => 'Quatrième année',
        5 => 'Cinquième année',
        default => ($student->group->level ? $student->group->level . 'ème année' : 'Troisième année')
    };
@endphp

<div class="page">

    <!-- HEADER -->
    <table style="width: 100%; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 20px; border-collapse: collapse;">
        <tr>
            <td style="width: 65px; vertical-align: middle;">
                <img src="{{ public_path('images/logo_upf.png') }}" style="height: 50px; display: block;" alt="UPF Logo">
            </td>
            <td style="padding-left: 12px; vertical-align: middle;">
                <div style="font-size: 20px; font-weight: bold; letter-spacing: 0.5px; color: #000;">UNIVERSITÉ PRIVÉE DE FÈS</div>
                <div style="font-size: 14px; font-weight: bold; margin-top: 3px; color: #000;">الجامعة الخاصة لفاس</div>
            </td>
            <td style="width: 30%;" align="right" valign="top">
                <div style="margin-bottom: 4px;">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(65)->generate(route('admin.convocations.verify', $convocation->reference)) !!}
                </div>
                <div style="font-size: 8px; color: #555; font-weight: bold; text-transform: uppercase;">Référence : {{ $convocation->reference }}</div>
            </td>
        </tr>
    </table>

    <!-- CONVOCATION TITLE -->
    <div style="text-align: center; margin-bottom: 25px;">
        <div style="font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Convocation Examen</div>
        <div style="font-size: 19px; font-weight: bold; margin-top: 5px;">{{ $academicYearName }} Session {{ $sessionName }}</div>
    </div>

    <!-- STUDENT INFO TABLE -->
    <table style="width: 100%; margin-bottom: 20px; font-size: 13.5px; border-collapse: collapse;">
        <tr>
            <td style="width: 16%; font-weight: bold; padding: 4px 0;">Matricule</td>
            <td style="width: 84%; padding: 4px 0;">: {{ $student->student_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 4px 0;">Nom & prénom</td>
            <td style="padding: 4px 0; text-transform: uppercase;">: {{ $student->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 4px 0;">Filière</td>
            <td style="padding: 4px 0;">: {{ $student->group->filiere->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 4px 0;">Niveau</td>
            <td style="padding: 4px 0;">: {{ $levelText }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Option</strong> &nbsp;&nbsp; -</td>
        </tr>
    </table>

    <!-- TEXT NOTICE -->
    <div style="font-size: 13.5px; font-weight: normal; margin-bottom: 15px;">
        Vous êtes priés(e) de vous présenter aux dates et heures suivantes pour les épreuves ci-dessous.
    </div>

    <!-- EXAMS LIST TABLE -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 11.5px;">
        <thead>
            <tr style="background-color: #fafafa; border-top: 1.5px solid #000; border-bottom: 1.5px solid #000;">
                <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-weight: bold; width: 12%;">Date</th>
                <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-weight: bold; width: 15%;">Horaire</th>
                <th style="border: 1px solid #000; padding: 8px 6px; text-align: left; font-weight: bold; width: 36%;">Matière</th>
                <th style="border: 1px solid #000; padding: 8px 6px; text-align: left; font-weight: bold; width: 22%;">Enseignant</th>
                <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-weight: bold; width: 10%;">Salle</th>
                <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-weight: bold; width: 5%;">Place</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allConvocations as $c)
                @php
                    $sched = \App\Models\Schedule::where('group_id', $c->exam->group_id)
                        ->where('module_id', $c->exam->module_id)
                        ->with('professor.user')
                        ->first();
                    $profName = $sched && $sched->professor && $sched->professor->user
                        ? $sched->professor->user->name
                        : 'Non spécifié';
                    
                    // Use actual seat number from the convocation record
                    $seatNumberStr = $c->seat_number ? str_replace('Place ', '', $c->seat_number) : ((($student->id * 17) + ($c->exam->id * 11)) % 55 + 1);
                @endphp
                <tr>
                    <td style="border: 1px solid #000; padding: 7px 4px; text-align: center;">
                        {{ \Carbon\Carbon::parse($c->exam->date)->format('d/m/Y') }}
                    </td>
                    <td style="border: 1px solid #000; padding: 7px 4px; text-align: center;">
                        {{ date('H:i', strtotime($c->exam->start_time)) }} - {{ $c->exam->end_time }}
                    </td>
                    <td style="border: 1px solid #000; padding: 7px 8px; font-weight: bold;">
                        {{ $c->exam->module->name }}
                    </td>
                    <td style="border: 1px solid #000; padding: 7px 8px; text-transform: uppercase; font-size: 10.5px;">
                        {{ $profName }}
                    </td>
                    <td style="border: 1px solid #000; padding: 7px 4px; text-align: center; font-weight: bold;">
                        {{ $c->exam->room->name ?? 'TBD' }}
                    </td>
                    <td style="border: 1px solid #000; padding: 7px 4px; text-align: center;">
                        {{ $seatNumberStr }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- RULES REGULATION -->
    <div style="margin-bottom: 25px;">
        <div style="font-size: 13.5px; font-weight: bold; border-bottom: 1.5px solid #000; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase;">
            Règlement des examens
        </div>
        <div style="font-size: 9px; line-height: 1.45; color: #111; text-align: justify; font-family: sans-serif;">
            L'usage des téléphones portables, tablettes ou autres appareils électroniques est interdit en salle d'examen. Même lorsque l'usage des calculatrices est autorisé, les portables ne peuvent être utilisés à cet effet.<br>
            - L'usage des PC portables est interdit sauf autorisation explicite de l'enseignant responsable de l'épreuve.<br>
            - Chaque étudiant est tenu de se munir de tous les articles de bureau nécessaires pour l'épreuve (stylos, crayons, gomme, marqueurs, blanco, règle, etc.). L'échange de tels articles entre étudiants est interdit.<br>
            - Tout étudiant en retard de plus de 20 minutes après la distribution des sujets des contrôles ou des examens ne peut être admis dans la salle d'examen.<br>
            - S'il est accepté à l'examen, l'étudiant retardataire rendra sa copie à la fin du temps réglementaire et ne pourra bénéficier d'aucune rallonge.<br>
            - Quel que soit le cas, tout étudiant en retard de plus de 30 minutes après la distribution des sujets des contrôles n'est pas accepté.<br>
            - Aucun étudiant participant à un examen ne pourra quitter définitivement la salle (même s'il doit rendre une copie blanche) que 30 minutes au moins après la distribution des sujets et sur autorisation de l'enseignant responsable de l'épreuve. Celui-ci l'informe qu'il ne pourra plus être réadmis dans la salle d'examen et qu'il doit remettre sa copie.<br>
            - Il est strictement interdit de quitter temporairement la salle d'examen pendant le déroulement de l'épreuve.<br>
            - Dans le cas où les documents ne sont pas autorisés, les étudiants déposent tous leurs documents et cartables sur le bureau des surveillants, avant la distribution des sujets d'examen.<br>
            - Tout document trouvé à proximité des étudiants pendant le déroulement d'une épreuve sans documents entraîne l'établissement d'un rapport de fraude.<br>
            - Tout type de communication entre étudiants pendant le déroulement d'une épreuve est prohibé.<br>
            - Toute fraude dûment constatée lors du déroulement des épreuves ou à posteriori, ainsi que tout manquement grave à la discipline donne lieu à l'attribution d'un zéro au contrôle et à l'établissement d'un rapport précisant la sanction proposée, compte tenu de la gravité de la fraude. Le rapport doit parvenir à la Direction dans un délai de 48 heures suivant le constat.<br>
            - Toute copie non rendue à l'heure fixée par les surveillants n'est pas acceptée et est affectée d'un zéro.
        </div>
    </div>

    <!-- SIGNATURE & STAMP -->
    <table style="width: 100%; margin-top: 15px;">
        <tr>
            <td style="width: 45%; valign: bottom;">
                <div style="font-size: 8.5px; color: #555; font-family: sans-serif;">Université Privée de Fès — Scolarité</div>
            </td>
            <td style="width: 55%;" align="right" valign="top">
                <div style="display: inline-block; text-align: center;">
                    <div style="font-size: 11.5px; font-weight: bold; margin-bottom: 10px;">Chargée de la Scolarité et des Affaires Estudiantines</div>
                    
                    {{-- Circular Stamp --}}
                    <div style="position: relative; width: 125px; height: 125px; margin: 10px auto; border: 2px double #1d4ed8; border-radius: 50%; color: #1d4ed8; font-family: 'DejaVu Sans', Arial, sans-serif; box-sizing: border-box; padding: 2px;">
                        <div style="width: 100%; height: 100%; border: 1px solid #1d4ed8; border-radius: 50%; position: relative;">
                            <!-- Top Text -->
                            <div style="position: absolute; width: 100%; text-align: center; top: 12px; font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px;">UNIVERSITE PRIVEE DE FES</div>
                            
                            <!-- Middle UPF -->
                            <div style="position: absolute; width: 100%; text-align: center; top: 40px; font-size: 18px; font-weight: bold; letter-spacing: 1.5px;">★ UPF ★</div>
                            
                            <!-- Middle Signature Name -->
                            <div style="position: absolute; width: 100%; text-align: center; top: 66px; font-size: 7px; font-weight: bold;">FADOUA KHALOUQ</div>
                            
                            <!-- Bottom Arabic Text -->
                            <div style="position: absolute; width: 100%; text-align: center; bottom: 12px; font-size: 8.5px; font-weight: bold;">الجامعة الخاصة لفاس</div>
                        </div>
                    </div>
                    
                    <div style="font-size: 11.5px; font-weight: bold; color: #1d4ed8; margin-top: 5px;">FADOUA KHALOUQ</div>
                </div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
