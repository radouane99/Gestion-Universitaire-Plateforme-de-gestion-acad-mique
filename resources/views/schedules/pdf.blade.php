<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 25px; }
        .title { color: #1e3a8a; text-transform: uppercase; letter-spacing: 2px; margin: 0; font-size: 20px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
        th { background-color: #f3f4f6; color: #1f2937; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; }
        .day-header { background-color: #1e3a8a; color: #ffffff; font-weight: bold; padding: 5px 10px; margin-top: 20px; border-radius: 4px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Université UPF</h1>
        <p class="subtitle">{{ $title }}</p>
    </div>

    @foreach($days as $dayNum => $dayName)
        @if($byDay->has($dayNum))
            <div class="day-header">{{ $dayName }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="20%">Horaires</th>
                        <th width="35%">Module</th>
                        @if($user->isProfessor())
                            <th width="25%">Groupe</th>
                        @else
                            <th width="25%">Professeur</th>
                        @endif
                        <th width="20%">Salle</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byDay[$dayNum]->sortBy('start_time') as $s)
                    <tr>
                        <td>{{ date('H:i', strtotime($s->start_time)) }} - {{ date('H:i', strtotime($s->end_time)) }}</td>
                        <td><strong>{{ $s->module->name }}</strong></td>
                        @if($user->isProfessor())
                            <td>{{ $s->group->name }} {{ $s->group->filiere ? '('.$s->group->filiere->name.')' : '' }}</td>
                        @else
                            <td>{{ $s->professor->user->name ?? '—' }}</td>
                        @endif
                        <td>{{ $s->room?->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="footer">
        Document généré informatiquement le {{ now()->format('d/m/Y à H:i') }}.<br>
        Direction des Affaires Académiques - UPF
    </div>
</body>
</html>
