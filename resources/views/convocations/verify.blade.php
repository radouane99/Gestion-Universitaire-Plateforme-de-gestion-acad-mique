<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Convocation - {{ $convocation->reference }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-upf-blue p-6 text-center">
            <h1 class="text-2xl font-black text-white tracking-tight">Université UPF</h1>
            <p class="text-blue-200 text-sm font-medium mt-1">Scanner de Présence</p>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3">
                    <span class="text-2xl">✅</span>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="text-center mb-6">
                <span class="inline-block bg-gray-100 text-gray-500 font-black px-3 py-1 rounded-full text-xs tracking-widest mb-2">RÉFÉRENCE</span>
                <p class="text-xl font-bold text-gray-900">{{ $convocation->reference }}</p>
            </div>

            <!-- Student info -->
            <div class="bg-gray-50 rounded-2xl p-5 mb-6 border border-gray-100">
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xl">
                        {{ substr($convocation->student->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-black text-gray-900">{{ $convocation->student->user->name }}</p>
                        <p class="text-sm text-gray-500 font-medium">{{ $convocation->student->student_number ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Examen</span>
                        <span class="font-bold text-gray-900 text-right">{{ $convocation->exam->module->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Date</span>
                        <span class="font-bold text-gray-900 text-right">{{ \Carbon\Carbon::parse($convocation->exam->date)->format('d/m/Y') }} à {{ date('H:i', strtotime($convocation->exam->start_time)) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Salle</span>
                        <span class="font-bold text-gray-900 text-right">{{ $convocation->exam->room->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Status & Action -->
            <div class="text-center">
                @if($convocation->is_present)
                    <div class="inline-flex items-center justify-center gap-2 bg-emerald-100 text-emerald-700 px-6 py-3 rounded-xl font-black text-lg w-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        PRÉSENCE VALIDÉE
                    </div>
                @else
                    <form action="{{ route('admin.convocations.mark_present', $convocation->reference) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-upf-magenta hover:bg-pink-700 text-white font-black py-4 px-6 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] active:scale-95 text-lg flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            MARQUER PRÉSENT(E)
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
