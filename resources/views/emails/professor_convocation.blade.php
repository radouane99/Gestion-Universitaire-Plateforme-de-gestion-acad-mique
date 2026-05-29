@component('mail::message')
# 🎓 Convocation de Surveillance d'Examens

Bonjour **{{ $convocation->professor->user->name }}**,

Vous êtes convoqué(e) pour assurer la **surveillance des examens** lors de la prochaine session. Veuillez trouver ci-joint votre convocation officielle de surveillance.

---

@component('mail::panel')
**Session :** {{ $convocation->exam->examSession->name ?? 'Session d\'examens' }}
**Examen :** {{ $convocation->exam->module->name }}
**Date :** {{ \Carbon\Carbon::parse($convocation->exam->date)->isoFormat('dddd D MMMM YYYY') }}
**Horaire :** {{ date('H:i', strtotime($convocation->exam->start_time)) }} — {{ $convocation->exam->end_time }}
**Salle :** {{ $convocation->exam->room->name ?? 'À confirmer' }}
**Rôle :** {{ $convocation->role === 'principal' ? 'Surveillant Principal' : 'Surveillant Assistant' }}
**Référence :** `{{ $convocation->reference }}`
@endcomponent

---

Veuillez vous présenter **15 minutes avant** le début de l'épreuve. La convocation PDF est jointe à cet email pour impression.

@component('mail::button', ['url' => route('professor.proctor_convocations.index'), 'color' => 'primary'])
Voir mes convocations en ligne
@endcomponent

Merci de votre implication et de votre disponibilité.

Cordialement,  
**La Scolarité — Université Privée de Fès**

---
*Réf : {{ $convocation->reference }} — Document officiel, ne pas répondre à cet email.*
@endcomponent
