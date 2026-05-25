<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            {{ __('Nouvelle Filière') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 dark:border-slate-800 p-8 lg:p-12">
                
                <form action="{{ route('admin.filieres.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div>
                        <label for="code" class="block text-sm font-black text-upf-blue uppercase tracking-widest mb-2">Code de la Filière (Ex: GI, GC)</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required class="block w-full rounded-2xl border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-upf-magenta focus:ring-upf-magenta font-mono uppercase">
                        @error('code') <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-black text-upf-blue uppercase tracking-widest mb-2">Nom Complet</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="block w-full rounded-2xl border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-upf-magenta focus:ring-upf-magenta font-bold">
                        @error('name') <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-black text-upf-blue uppercase tracking-widest mb-2">Description / Objectifs</label>
                        <textarea name="description" id="description" rows="4" class="block w-full rounded-2xl border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-upf-magenta focus:ring-upf-magenta">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-2 text-sm text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end pt-6">
                        <a href="{{ route('admin.filieres.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-slate-600 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none transition mr-4">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center px-8 py-3 bg-upf-magenta border border-transparent rounded-xl font-black text-white uppercase tracking-widest hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-upf-magenta focus:ring-offset-2 transition shadow-lg">
                            Créer la Filière
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
