<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            </div>

                            <div>
                                <x-input-label for="role_id" :value="__('Role')" />
                                <select id="role_id" name="role_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="toggleRoleFields(this)">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" data-role="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                            </div>

                            <div id="student_fields" class="hidden">
                                <x-input-label for="group_id" :value="__('Group')" />
                                <select name="group_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->level }})</option>
                                    @endforeach
                                </select>
                                <div class="mt-4">
                                    <x-input-label for="student_number" :value="__('Student Number')" />
                                    <x-text-input id="student_number" class="block mt-1 w-full" type="text" name="student_number" :value="old('student_number')" />
                                </div>
                            </div>

                            <div id="professor_fields" class="hidden">
                                <x-input-label for="department" :value="__('Department')" />
                                <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department')" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ms-4">
                                {{ __('Create User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRoleFields(select) {
            const role = select.options[select.selectedIndex].getAttribute('data-role');
            document.getElementById('student_fields').classList.add('hidden');
            document.getElementById('professor_fields').classList.add('hidden');

            if (role === 'student') {
                document.getElementById('student_fields').classList.remove('hidden');
            } else if (role === 'professor') {
                document.getElementById('professor_fields').classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
