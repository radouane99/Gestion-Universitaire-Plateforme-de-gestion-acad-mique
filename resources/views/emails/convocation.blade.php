@component('mail::message')
# 📋 Convocation d'Examen

Bonjour **{{ $convocation->student->user->name }}**,

Vous êtes convoqué(e) à l'examen suivant :

@component('mail::panel')
**Module :** {{ $convocation->exam->module->name }}
**Type :** {{ $convocation->exam->type }}
**Date :** {{ \Carbon\Carbon::parse($convocation->exam->date)->isoFormat('dddd D MMMM YYYY') }}
**Horaire :** {{ date('H:i', strtotime($convocation->exam->start_time)) }} — {{ $convocation->exam->end_time }}
**Durée :** {{ $convocation->exam->duration }} minutes
**Salle :** {{ $convocation->exam->room->name ?? 'À confirmer' }}
**Référence :** `{{ $convocation->reference }}`
@endcomponent

Veuillez trouver votre **convocation officielle** en pièce jointe (PDF).

> ⚠️ Pensez à vous munir de votre carte étudiante et d'une pièce d'identité le jour de l'examen.

@component('mail::button', ['url' => config('app.url') . '/student/convocations', 'color' => 'primary'])
Accéder à mes convocations
@endcomponent

Bonne chance pour votre examen !

Cordialement,
**Direction des Affaires Académiques**
{{ config('app.name') }}
@endcomponent
