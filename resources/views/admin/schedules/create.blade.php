<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Schedule Session') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.schedules.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="group_id" :value="__('Group')" />
                                <select name="group_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="module_id" :value="__('Module')" />
                                <select name="module_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($modules as $module)
                                        <option value="{{ $module->id }}">{{ $module->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="professor_id" :value="__('Professor')" />
                                <select name="professor_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($professors as $prof)
                                        <option value="{{ $prof->id }}">{{ $prof->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="room_id" :value="__('Room')" />
                                <select name="room_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->capacity }})</option>
                                    @endforeach
                                </select>
                            </div>

                             <div>
                                <x-input-label for="date" :value="__('Date de la Séance')" />
                                <x-text-input type="date" name="date" class="block mt-1 w-full" value="{{ date('Y-m-d') }}" required />
                             </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="start_time" :value="__('Start Time')" />
                                    <x-text-input type="time" name="start_time" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="end_time" :value="__('End Time')" />
                                    <x-text-input type="time" name="end_time" class="block mt-1 w-full" required />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ms-4">
                                {{ __('Save Session') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
