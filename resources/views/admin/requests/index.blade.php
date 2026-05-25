<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
            {{ __("Gestion des Demandes Administratives") }}
        </h2>
    </x-slot>

    <!-- Include SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div class="py-12 bg-[#F8FAFC]" x-data="kanbanBoard()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 bg-gradient-to-r from-upf-blue to-upf-navy rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-black mb-2 italic">Tableau Kanban</h2>
                    <p class="text-blue-100 opacity-80">Glissez-déposez les cartes pour traiter les demandes des étudiants et professeurs.</p>
                </div>
            </div>

            <!-- Kanban Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- Pending Column -->
                <div class="bg-gray-100 rounded-[2rem] p-4 flex flex-col h-full min-h-[500px]">
                    <div class="flex items-center justify-between px-4 py-2 mb-4">
                        <h3 class="font-black text-amber-600 uppercase tracking-widest text-sm">En Attente</h3>
                        <span class="bg-amber-200 text-amber-800 text-xs font-bold px-2 py-1 rounded-full">{{ $requests->where('status', 'pending')->count() }}</span>
                    </div>
                    <div id="col-pending" class="flex-1 space-y-4 min-h-[200px]" data-status="pending">
                        @foreach($requests->where('status', 'pending') as $req)
                            @include('admin.requests.partials.kanban-card', ['req' => $req])
                        @endforeach
                    </div>
                </div>

                <!-- Approved Column -->
                <div class="bg-gray-100 rounded-[2rem] p-4 flex flex-col h-full min-h-[500px]">
                    <div class="flex items-center justify-between px-4 py-2 mb-4">
                        <h3 class="font-black text-emerald-600 uppercase tracking-widest text-sm">Approuvé</h3>
                        <span class="bg-emerald-200 text-emerald-800 text-xs font-bold px-2 py-1 rounded-full">{{ $requests->where('status', 'approved')->count() }}</span>
                    </div>
                    <div id="col-approved" class="flex-1 space-y-4 min-h-[200px]" data-status="approved">
                        @foreach($requests->where('status', 'approved') as $req)
                            @include('admin.requests.partials.kanban-card', ['req' => $req])
                        @endforeach
                    </div>
                </div>

                <!-- Rejected Column -->
                <div class="bg-gray-100 rounded-[2rem] p-4 flex flex-col h-full min-h-[500px]">
                    <div class="flex items-center justify-between px-4 py-2 mb-4">
                        <h3 class="font-black text-upf-magenta uppercase tracking-widest text-sm">Refusé</h3>
                        <span class="bg-pink-200 text-upf-magenta text-xs font-bold px-2 py-1 rounded-full">{{ $requests->where('status', 'rejected')->count() }}</span>
                    </div>
                    <div id="col-rejected" class="flex-1 space-y-4 min-h-[200px]" data-status="rejected">
                        @foreach($requests->where('status', 'rejected') as $req)
                            @include('admin.requests.partials.kanban-card', ['req' => $req])
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <!-- Rejection Modal -->
        <div x-show="isRejectModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 transform transition-all" @click.away="cancelRejection()">
                <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-xl font-black text-center text-gray-900 mb-2">Motif du Refus</h3>
                <p class="text-sm text-gray-500 text-center mb-6">Veuillez indiquer pourquoi cette demande est refusée. L'utilisateur recevra une notification.</p>
                
                <textarea x-model="rejectReason" rows="3" class="w-full border-gray-200 rounded-xl focus:ring-rose-500 focus:border-rose-500 p-4 text-sm font-bold text-gray-900 bg-gray-50 mb-6" placeholder="Raison détaillée..."></textarea>
                
                <div class="flex gap-4">
                    <button @click="cancelRejection()" class="flex-1 py-3 px-4 border-2 border-gray-100 rounded-xl text-gray-500 font-black hover:bg-gray-50 transition-colors">Annuler</button>
                    <button @click="confirmRejection()" class="flex-1 py-3 px-4 bg-rose-500 text-white rounded-xl font-black hover:bg-rose-600 shadow-lg shadow-rose-500/30 transition-all">Confirmer le Refus</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kanbanBoard', () => ({
                isRejectModalOpen: false,
                rejectReason: '',
                currentRequestElement: null,
                currentRequestId: null,
                originalColumn: null,

                init() {
                    const self = this;
                    const options = {
                        group: 'requests',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        onEnd: function (evt) {
                            const itemEl = evt.item;
                            const toCol = evt.to;
                            const fromCol = evt.from;
                            
                            if (toCol === fromCol) return; // Didn't change column

                            const newStatus = toCol.getAttribute('data-status');
                            const requestId = itemEl.getAttribute('data-id');

                            if (newStatus === 'rejected') {
                                // Need to ask for reason
                                self.originalColumn = fromCol;
                                self.currentRequestElement = itemEl;
                                self.currentRequestId = requestId;
                                self.rejectReason = '';
                                self.isRejectModalOpen = true;
                            } else {
                                // Direct update
                                self.updateRequestStatus(requestId, newStatus);
                            }
                        }
                    };

                    new Sortable(document.getElementById('col-pending'), options);
                    new Sortable(document.getElementById('col-approved'), options);
                    new Sortable(document.getElementById('col-rejected'), options);
                },

                updateRequestStatus(id, status, reason = null) {
                    fetch(`/admin/requests/${id}/update-status-ajax`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: status, reason: reason })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Update UI if needed (like the action buttons on the card)
                            // A page reload might be easier to update all counts and action buttons correctly
                            window.location.reload(); 
                        } else {
                            alert('Erreur lors de la mise à jour.');
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Erreur serveur.');
                        window.location.reload();
                    });
                },

                confirmRejection() {
                    if (!this.rejectReason.trim()) {
                        alert("Le motif est obligatoire pour un refus.");
                        return;
                    }
                    this.isRejectModalOpen = false;
                    this.updateRequestStatus(this.currentRequestId, 'rejected', this.rejectReason);
                },

                cancelRejection() {
                    this.isRejectModalOpen = false;
                    // Move the card back to its original column
                    if (this.currentRequestElement && this.originalColumn) {
                        this.originalColumn.appendChild(this.currentRequestElement);
                    }
                }
            }));
        });
    </script>
</x-app-layout>
