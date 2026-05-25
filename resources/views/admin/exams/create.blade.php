<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.exams.index') }}" class="text-gray-400 hover:text-upf-blue transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Planifier un Examen') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-gray-100">
                <div class="p-8">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.exams.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="filiere_id" :value="__('Filière')" />
                                <select name="filiere_id" id="filiere_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required>
                                    <option value="">Sélectionnez une filière</option>
                                    @foreach($filieres as $filiere)
                                        <option value="{{ $filiere->id }}">{{ $filiere->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="group_id" :value="__('Groupe')" />
                                <select name="group_id" id="group_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required disabled>
                                    <option value="">Sélectionnez d'abord une filière</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="module_id" :value="__('Module')" />
                                <select name="module_id" id="module_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required disabled>
                                    <option value="">Sélectionnez d'abord un groupe</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                            <div>
                                <x-input-label for="exam_session_id" :value="__('Session d\'examen')" />
                                <select name="exam_session_id" id="exam_session_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required>
                                    <option value="">Sélectionnez une session</option>
                                    @foreach($examSessions as $session)
                                        @if($session->start_date && $session->end_date)
                                            <option value="{{ $session->id }}" {{ old('exam_session_id') == $session->id ? 'selected' : '' }}>
                                                {{ $session->name }} ({{ $session->start_date->format('d/m') }} - {{ $session->end_date->format('d/m') }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Type d\'examen')" />
                                <select name="type" id="type" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required>
                                    <option value="CC1" {{ old('type') == 'CC1' ? 'selected' : '' }}>Contrôle Continu 1</option>
                                    <option value="CC2" {{ old('type') == 'CC2' ? 'selected' : '' }}>Contrôle Continu 2</option>
                                    <option value="Final" {{ old('type') == 'Final' ? 'selected' : '' }}>Examen Final</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="room_id" :value="__('Salle (Optionnelle mais recommandée)')" />
                                <select name="room_id" id="room_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm">
                                    <option value="">Sélectionnez une salle</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }} (Capacité: {{ $room->capacity }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date')" required />
                            </div>
                            <div>
                                <x-input-label for="start_time" :value="__('Heure de début')" />
                                <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="old('start_time')" required />
                            </div>
                            <div>
                                <x-input-label for="duration" :value="__('Durée (minutes)')" />
                                <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" :value="old('duration', 90)" required />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="proctors" :value="__('Surveillants assignés')" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($professors as $prof)
                                    <label class="flex items-center space-x-2 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:bg-white transition-colors cursor-pointer">
                                        <input type="checkbox" name="proctors[]" value="{{ $prof->id }}" class="rounded border-gray-300 text-upf-blue shadow-sm focus:ring-upf-blue">
                                        <span class="text-sm font-medium text-gray-700">{{ $prof->user->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Convocation Options --}}
                        <div class="mt-6 p-6 bg-blue-50/50 border border-blue-100 rounded-2xl">
                            <h4 class="font-black text-upf-blue text-sm uppercase tracking-widest mb-4">📋 Options de convocation</h4>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="generate_convocations" value="1" checked
                                        class="rounded border-gray-300 text-upf-blue shadow-sm focus:ring-upf-blue w-4 h-4">
                                    <span class="text-sm font-bold text-gray-700">Générer automatiquement les convocations pour tous les étudiants du groupe</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="send_email" value="1"
                                        class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500 w-4 h-4">
                                    <span class="text-sm font-bold text-gray-700">Envoyer les convocations par <span class="text-emerald-600">email</span> avec le PDF en pièce jointe</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100">
                            <x-primary-button>
                                {{ __('Planifier l\'examen') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filiereSelect = document.getElementById('filiere_id');
            const groupSelect = document.getElementById('group_id');
            const moduleSelect = document.getElementById('module_id');

            filiereSelect.addEventListener('change', function() {
                const filiereId = this.value;
                groupSelect.innerHTML = '<option value="">Chargement...</option>';
                groupSelect.disabled = true;
                moduleSelect.innerHTML = '<option value="">Sélectionnez d\'abord un groupe</option>';
                moduleSelect.disabled = true;

                if (filiereId) {
                    fetch(`/admin/api/filieres/${filiereId}/groups`)
                        .then(response => response.json())
                        .then(data => {
                            groupSelect.innerHTML = '<option value="">Sélectionnez un groupe</option>';
                            data.forEach(group => {
                                groupSelect.innerHTML += `<option value="${group.id}">${group.name}</option>`;
                            });
                            groupSelect.disabled = false;
                        });
                } else {
                    groupSelect.innerHTML = '<option value="">Sélectionnez d\'abord une filière</option>';
                }
            });

            groupSelect.addEventListener('change', function() {
                const groupId = this.value;
                moduleSelect.innerHTML = '<option value="">Chargement...</option>';
                moduleSelect.disabled = true;

                if (groupId) {
                    fetch(`/admin/api/groups/${groupId}/modules`)
                        .then(response => response.json())
                        .then(data => {
                            moduleSelect.innerHTML = '<option value="">Sélectionnez un module</option>';
                            data.forEach(module => {
                                moduleSelect.innerHTML += `<option value="${module.id}">${module.name}</option>`;
                            });
                            moduleSelect.disabled = false;
                        });
                } else {
                    moduleSelect.innerHTML = '<option value="">Sélectionnez d\'abord un groupe</option>';
                }
            });
        });
    </script>
</x-app-layout>
