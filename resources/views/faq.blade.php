<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-upf-blue dark:text-white leading-tight tracking-tight italic">
            {{ __('Questions Fréquentes (FAQ)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Header -->
            <div class="text-center mb-12">
                <h3 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter italic">Besoin d'aide ?</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-3 text-lg">Trouvez rapidement les réponses aux questions les plus courantes.</p>
            </div>

            <!-- FAQ Accordion -->
            <div class="space-y-4" x-data="{ openItem: null }">

                <!-- Item 1 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-all">
                    <button @click="openItem = openItem === 1 ? null : 1" class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="font-bold text-gray-900 dark:text-white">Comment accéder à mon espace étudiant ?</span>
                        <svg :class="{ 'rotate-180': openItem === 1 }" class="w-5 h-5 text-upf-blue transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openItem === 1" x-collapse>
                        <div class="px-6 pb-6 text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-slate-700 pt-4">
                            Cliquez sur le bouton <strong>"Espace Académique"</strong> en haut à droite de la page d'accueil, puis connectez-vous avec votre email et mot de passe fournis par l'administration. Si vous n'avez pas encore de compte, veuillez contacter le secrétariat.
                        </div>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-all">
                    <button @click="openItem = openItem === 2 ? null : 2" class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="font-bold text-gray-900 dark:text-white">Comment demander une attestation de scolarité ?</span>
                        <svg :class="{ 'rotate-180': openItem === 2 }" class="w-5 h-5 text-upf-blue transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openItem === 2" x-collapse>
                        <div class="px-6 pb-6 text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-slate-700 pt-4">
                            Depuis votre tableau de bord étudiant, cliquez sur <strong>"Gérer les Demandes"</strong>. Sélectionnez le type de document souhaité (Attestation de scolarité ou Relevé de Notes) et soumettez votre demande. L'administration la traitera sous 24 à 48 heures.
                        </div>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-all">
                    <button @click="openItem = openItem === 3 ? null : 3" class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="font-bold text-gray-900 dark:text-white">Comment justifier une absence ?</span>
                        <svg :class="{ 'rotate-180': openItem === 3 }" class="w-5 h-5 text-upf-blue transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openItem === 3" x-collapse>
                        <div class="px-6 pb-6 text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-slate-700 pt-4">
                            Vous devez fournir un justificatif (certificat médical, convocation, etc.) au secrétariat dans un délai de <strong>48 heures</strong> suivant votre absence. Le professeur concerné mettra à jour votre statut dans le système.
                        </div>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-all">
                    <button @click="openItem = openItem === 4 ? null : 4" class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="font-bold text-gray-900 dark:text-white">Comment consulter mes notes et mon relevé ?</span>
                        <svg :class="{ 'rotate-180': openItem === 4 }" class="w-5 h-5 text-upf-blue transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openItem === 4" x-collapse>
                        <div class="px-6 pb-6 text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-slate-700 pt-4">
                            Rendez-vous dans votre portail étudiant, section <strong>"Notes"</strong>. Vous y trouverez vos résultats par module, votre moyenne générale (GPA) et une barre de progression visuelle pour chaque matière.
                        </div>
                    </div>
                </div>

                <!-- Item 5 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-all">
                    <button @click="openItem = openItem === 5 ? null : 5" class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="font-bold text-gray-900 dark:text-white">Comment contacter l'administration ?</span>
                        <svg :class="{ 'rotate-180': openItem === 5 }" class="w-5 h-5 text-upf-blue transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openItem === 5" x-collapse>
                        <div class="px-6 pb-6 text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-slate-700 pt-4">
                            Vous pouvez utiliser le formulaire de la page <strong>"Contact"</strong> accessible depuis le menu de navigation. Votre message sera transmis directement à l'administration qui vous répondra par email dans les meilleurs délais.
                        </div>
                    </div>
                </div>

                <!-- Item 6 -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-all">
                    <button @click="openItem = openItem === 6 ? null : 6" class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <span class="font-bold text-gray-900 dark:text-white">Comment changer la langue de l'interface ?</span>
                        <svg :class="{ 'rotate-180': openItem === 6 }" class="w-5 h-5 text-upf-blue transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openItem === 6" x-collapse>
                        <div class="px-6 pb-6 text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-slate-700 pt-4">
                            Cliquez sur le bouton de langue (FR, EN, AR) dans la barre de navigation en haut de page. Vous pouvez basculer entre le <strong>Français</strong>, l'<strong>Anglais</strong> et l'<strong>Arabe</strong> à tout moment. L'interface s'adaptera automatiquement, y compris la direction du texte pour l'arabe (RTL).
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
