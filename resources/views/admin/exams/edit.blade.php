<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.exams.index') }}" class="text-gray-400 hover:text-upf-blue transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier un Examen') }}
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

                    <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="filiere_id" :value="__('Filière')" />
                                <select name="filiere_id" id="filiere_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required>
                                    @foreach($filieres as $filiere)
                                        <option value="{{ $filiere->id }}" {{ (old('filiere_id') ?? $exam->group->filiere_id) == $filiere->id ? 'selected' : '' }}>{{ $filiere->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="group_id" :value="__('Groupe')" />
                                <select name="group_id" id="group_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ old('group_id', $exam->group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="module_id" :value="__('Module')" />
                                <select name="module_id" id="module_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required>
                                    @foreach($modules as $module)
                                        <option value="{{ $module->id }}" {{ old('module_id', $exam->module_id) == $module->id ? 'selected' : '' }}>{{ $module->name }}</option>
                                    @endforeach
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
                                            <option value="{{ $session->id }}" {{ old('exam_session_id', $exam->exam_session_id) == $session->id ? 'selected' : '' }}>
                                                {{ $session->name }} ({{ $session->start_date->format('d/m') }} - {{ $session->end_date->format('d/m') }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Type d\'examen')" />
                                <select name="type" id="type" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm" required>
                                    <option value="CC1" {{ old('type', $exam->type) == 'CC1' ? 'selected' : '' }}>Contrôle Continu 1</option>
                                    <option value="CC2" {{ old('type', $exam->type) == 'CC2' ? 'selected' : '' }}>Contrôle Continu 2</option>
                                    <option value="Final" {{ old('type', $exam->type) == 'Final' ? 'selected' : '' }}>Examen Final</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="room_id" :value="__('Salle (Optionnelle mais recommandée)')" />
                                <select name="room_id" id="room_id" class="mt-1 block w-full border-gray-300 focus:border-upf-blue focus:ring-upf-blue rounded-xl shadow-sm">
                                    <option value="">Sélectionnez une salle</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id', $exam->room_id) == $room->id ? 'selected' : '' }}>{{ $room->name }} (Capacité: {{ $room->capacity }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', $exam->date)" required />
                            </div>
                            <div>
                                <x-input-label for="start_time" :value="__('Heure de début')" />
                                <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="old('start_time', \Carbon\Carbon::parse($exam->start_time)->format('H:i'))" required />
                            </div>
                            <div>
                                <x-input-label for="duration" :value="__('Durée (minutes)')" />
                                <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" :value="old('duration', $exam->duration)" required />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="proctors" :value="__('Surveillants assignés')" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @php
                                    $assignedProctors = $exam->proctors->pluck('id')->toArray();
                                @endphp
                                @foreach($professors as $prof)
                                    <label class="flex items-center space-x-2 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:bg-white transition-colors cursor-pointer">
                                        <input type="checkbox" name="proctors[]" value="{{ $prof->id }}" 
                                            {{ (is_array(old('proctors')) && in_array($prof->id, old('proctors'))) || (!old('proctors') && in_array($prof->id, $assignedProctors)) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-upf-blue shadow-sm focus:ring-upf-blue">
                                        <span class="text-sm font-medium text-gray-700">{{ $prof->user->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100">
                            <x-primary-button>
                                {{ __('Mettre à jour l\'examen') }}
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
                moduleSelect.innerHTML = '<option value="">Sélectionnez d\'abord un groupe</option>';

                if (filiereId) {
                    fetch(`/admin/api/filieres/${filiereId}/groups`)
                        .then(response => response.json())
                        .then(data => {
                            groupSelect.innerHTML = '<option value="">Sélectionnez un groupe</option>';
                            data.forEach(group => {
                                groupSelect.innerHTML += `<option value="${group.id}">${group.name}</option>`;
                            });
                        });
                } else {
                    groupSelect.innerHTML = '<option value="">Sélectionnez d\'abord une filière</option>';
                }
            });

            groupSelect.addEventListener('change', function() {
                const groupId = this.value;
                moduleSelect.innerHTML = '<option value="">Chargement...</option>';

                if (groupId) {
                    fetch(`/admin/api/groups/${groupId}/modules`)
                        .then(response => response.json())
                        .then(data => {
                            moduleSelect.innerHTML = '<option value="">Sélectionnez un module</option>';
                            data.forEach(module => {
                                moduleSelect.innerHTML += `<option value="${module.id}">${module.name}</option>`;
                            });
                        });
                } else {
                    moduleSelect.innerHTML = '<option value="">Sélectionnez d\'abord un groupe</option>';
                }
            });
        });
    </script>
</x-app-layout>
