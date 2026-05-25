<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tight italic">
            {{ __('Journal d\'Activité') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-slate-800">
                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-800">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Utilisateur</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Action</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Description</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">IP</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800">
                                @forelse ($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-upf-blue flex items-center justify-center text-white text-xs font-bold">
                                                    {{ $log->user ? substr($log->user->name, 0, 1) : '?' }}
                                                </div>
                                                <span class="ml-3 text-sm font-bold text-gray-900 dark:text-white">{{ $log->user->name ?? 'Système' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $colors = [
                                                    'created' => 'bg-emerald-100 text-emerald-800',
                                                    'updated' => 'bg-blue-100 text-blue-800',
                                                    'deleted' => 'bg-red-100 text-red-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-orange-100 text-orange-800',
                                                    'replied' => 'bg-purple-100 text-purple-800',
                                                    'login' => 'bg-indigo-100 text-indigo-800',
                                                    'exported' => 'bg-cyan-100 text-cyan-800',
                                                ];
                                                $color = $colors[$log->action] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $color }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-upf-magenta">{{ $log->model_type }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $log->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-mono">{{ $log->ip_address }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            <p class="font-bold uppercase tracking-widest">Aucune activité enregistrée.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $logs->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
