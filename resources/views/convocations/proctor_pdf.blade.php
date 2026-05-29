<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Convocation Surveillance — {{ $convocation->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #000; background: #fff; font-size: 12px; line-height: 1.4; }
        .page { padding: 25px 35px; }
        table { border-collapse: collapse; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-principal { background: #1e3a8a; color: #fff; }
        .badge-assistant { background: #374151; color: #fff; }
    </style>
</head>
<body>
@php
    $professor    = $convocation->professor;
    $currentExam  = $convocation->exam;
    $session      = $currentExam->examSession;
    $academicYearName = $session?->academicYear?->name ?? '2025-2026';
    $sessionName  = $session?->name ?? 'Session d\'examens';
@endphp

<div class="page">

    <!-- HEADER -->
    <table style="width: 100%; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 20px;">
        <tr>
            <td style="width: 70%;" valign="top">
                <div style="font-size: 22px; font-weight: bold; letter-spacing: 0.5px; color: #000;">UNIVERSITÉ PRIVÉE DE FÈS</div>
                <div style="font-size: 16px; font-weight: bold; margin-top: 5px; color: #000;">الجامعة الخاصة لفاس</div>
                <div style="font-size: 10px; color: #555; margin-top: 4px;">Service de Scolarité et des Affaires Estudiantines</div>
            </td>
            <td style="width: 30%;" align="right" valign="top">
                <div style="margin-bottom: 4px;">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(65)->generate(url('/verify/proctor/' . $convocation->reference)) !!}
                </div>
                <div style="font-size: 8px; color: #555; font-weight: bold; text-transform: uppercase;">Réf : {{ $convocation->reference }}</div>
            </td>
        </tr>
    </table>

    <!-- TITLE -->
    <div style="text-align: center; margin-bottom: 25px;">
        <div style="font-size: 22px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Convocation de Surveillance d'Examens</div>
        <div style="font-size: 17px; font-weight: bold; margin-top: 5px;">{{ $academicYearName }} — Session {{ $sessionName }}</div>
        <div style="font-size: 11px; color: #444; margin-top: 4px;">Document officiel — à conserver</div>
    </div>

    <!-- PROFESSOR INFO -->
    <table style="width: 100%; margin-bottom: 20px; font-size: 13px; border-collapse: collapse;">
        <tr>
            <td style="width: 22%; font-weight: bold; padding: 4px 0;">Nom & Prénom</td>
            <td style="padding: 4px 0; text-transform: uppercase; font-weight: bold;">: {{ $professor->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 4px 0;">Département</td>
            <td style="padding: 4px 0;">: {{ $professor->department ?? 'Non spécifié' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 4px 0;">Email</td>
            <td style="padding: 4px 0; font-size: 11px; color: #333;">: {{ $professor->user->email ?? '' }}</td>
        </tr>
    </table>

    <!-- INTRO TEXT -->
    <div style="font-size: 12.5px; margin-bottom: 15px; font-style: italic; border-left: 3px solid #000; padding-left: 8px;">
        Vous êtes prié(e) d'assurer la surveillance des épreuves d'examen aux dates et horaires indiqués ci-dessous.
        Veuillez vous présenter <strong>15 minutes avant le début de chaque épreuve</strong>.
    </div>

    <!-- SURVEILLANCE TABLE -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 11px;">
        <thead>
            <tr style="background-color: #1e3a8a; color: #fff;">
                <th style="border: 1px solid #000; padding: 8px 5px; text-align: center; width: 12%;">Date</th>
                <th style="border: 1px solid #000; padding: 8px 5px; text-align: center; width: 14%;">Horaire</th>
                <th style="border: 1px solid #000; padding: 8px 6px; text-align: left; width: 28%;">Module</th>
                <th style="border: 1px solid #000; padding: 8px 6px; text-align: left; width: 16%;">Groupe</th>
                <th style="border: 1px solid #000; padding: 8px 5px; text-align: center; width: 12%;">Salle</th>
                <th style="border: 1px solid #000; padding: 8px 5px; text-align: center; width: 10%;">Places</th>
                <th style="border: 1px solid #000; padding: 8px 5px; text-align: center; width: 8%;">Rôle</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allConvocations as $c)
                <tr style="{{ $loop->even ? 'background-color: #f8fafc;' : '' }}">
                    <td style="border: 1px solid #aaa; padding: 7px 5px; text-align: center; font-weight: bold;">
                        {{ \Carbon\Carbon::parse($c->exam->date)->format('d/m/Y') }}
                    </td>
                    <td style="border: 1px solid #aaa; padding: 7px 5px; text-align: center;">
                        {{ date('H:i', strtotime($c->exam->start_time)) }} – {{ $c->exam->end_time }}
                    </td>
                    <td style="border: 1px solid #aaa; padding: 7px 8px; font-weight: bold;">
                        {{ $c->exam->module->name ?? '—' }}
                    </td>
                    <td style="border: 1px solid #aaa; padding: 7px 8px; font-size: 10px;">
                        {{ $c->exam->group->name ?? '—' }}
                    </td>
                    <td style="border: 1px solid #aaa; padding: 7px 5px; text-align: center; font-weight: bold;">
                        {{ $c->exam->room->name ?? 'TBD' }}
                    </td>
                    <td style="border: 1px solid #aaa; padding: 7px 5px; text-align: center;">
                        {{ $c->exam->room->capacity ?? '—' }}
                    </td>
                    <td style="border: 1px solid #aaa; padding: 7px 5px; text-align: center;">
                        @if($c->role === 'principal')
                            <span style="font-size: 8px; font-weight: bold; color: #1e3a8a; text-transform: uppercase;">Principal</span>
                        @else
                            <span style="font-size: 8px; font-weight: bold; color: #374151; text-transform: uppercase;">Assistant</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- INSTRUCTIONS -->
    <div style="margin-bottom: 25px;">
        <div style="font-size: 12px; font-weight: bold; border-bottom: 1.5px solid #000; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase;">
            Instructions de Surveillance
        </div>
        <div style="font-size: 9px; line-height: 1.5; color: #111; font-family: sans-serif;">
            - Le surveillant <strong>principal</strong> est responsable de la distribution des sujets, de l'émargement des étudiants et du rassemblement des copies.<br>
            - Le surveillant <strong>assistant</strong> veille au maintien de l'ordre et à la bonne marche de l'épreuve.<br>
            - Aucun étudiant ne peut entrer en salle après 20 minutes de retard (à partir de la distribution des sujets).<br>
            - Aucun étudiant ne peut quitter définitivement la salle avant 30 minutes après la distribution.<br>
            - Tout incident (fraude, retard excessif, problème technique) doit faire l'objet d'un rapport écrit remis à la scolarité sous 48h.<br>
            - Les téléphones portables et appareils électroniques personnels ne sont pas autorisés dans la salle pendant l'épreuve.<br>
            - En cas d'urgence ou d'absence imprévue, contacter immédiatement la Scolarité au numéro d'urgence.
        </div>
    </div>

    <!-- SIGNATURE & STAMP -->
    <table style="width: 100%; margin-top: 15px;">
        <tr>
            <td style="width: 50%; valign: bottom;">
                <div style="font-size: 11px; font-weight: bold; margin-bottom: 5px;">Signature du Professeur</div>
                <div style="font-size: 8px; color: #666; margin-bottom: 40px;">(Réception confirmée)</div>
                <div style="border-top: 1px solid #000; width: 150px;"></div>
                <div style="font-size: 8px; color: #555; margin-top: 3px; font-style: italic;">{{ $professor->user->name ?? '' }}</div>
            </td>
            <td style="width: 50%;" align="right" valign="top">
                <div style="display: inline-block; text-align: center;">
                    <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px;">Chargée de la Scolarité et des Affaires Estudiantines</div>
                    <!-- Circular Stamp -->
                    <div style="position: relative; width: 125px; height: 125px; margin: 10px auto; border: 2px double #1d4ed8; border-radius: 50%; color: #1d4ed8; font-family: 'DejaVu Sans', Arial, sans-serif; box-sizing: border-box; padding: 2px;">
                        <div style="width: 100%; height: 100%; border: 1px solid #1d4ed8; border-radius: 50%; position: relative;">
                            <div style="position: absolute; width: 100%; text-align: center; top: 12px; font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px;">UNIVERSITE PRIVEE DE FES</div>
                            <div style="position: absolute; width: 100%; text-align: center; top: 40px; font-size: 18px; font-weight: bold; letter-spacing: 1.5px;">★ UPF ★</div>
                            <div style="position: absolute; width: 100%; text-align: center; top: 66px; font-size: 7px; font-weight: bold;">FADOUA KHALOUQ</div>
                            <div style="position: absolute; width: 100%; text-align: center; bottom: 12px; font-size: 8.5px; font-weight: bold;">الجامعة الخاصة لفاس</div>
                        </div>
                    </div>
                    <div style="font-size: 11px; font-weight: bold; color: #1d4ed8; margin-top: 5px;">FADOUA KHALOUQ</div>
                </div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
