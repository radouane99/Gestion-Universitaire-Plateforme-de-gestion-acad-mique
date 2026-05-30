<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-upf-navy via-indigo-950 to-black text-white flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8 font-sans">
        
        <!-- Premium Logo Header -->
        <div class="text-center mb-6 z-10 space-y-2">
            <a href="/" class="inline-block transform hover:scale-105 transition-transform">
                <img src="/images/logo_upf.png" alt="UPF Logo" class="h-20 w-auto filter drop-shadow-[0_0_15px_rgba(255,255,255,0.2)]">
            </a>
            <h1 class="text-2xl sm:text-3xl font-black italic tracking-tighter text-white">{{ __('Université Privée de Fès') }}</h1>
            <p class="text-blue-200/70 text-xs font-black uppercase tracking-widest">{{ __('Portail National de Candidature en Ligne') }}</p>
        </div>

        <!-- Glassmorphism Container -->
        <div x-data="{ 
            step: 1,
            validateStep1() {
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const password_confirm = document.getElementById('password_confirmation').value;
                const cin = document.getElementById('cin').value;
                
                if (!name || !email || !password || !password_confirm || !cin) {
                    alert('{{ __("Veuillez remplir tous les champs de l\'étape 1.") }}');
                    return false;
                }
                if (password !== password_confirm) {
                    alert('{{ __("Les mots de passe ne correspondent pas.") }}');
                    return false;
                }
                return true;
            },
            validateStep2() {
                const birth_date = document.getElementById('birth_date').value;
                const birth_place = document.getElementById('birth_place').value;
                const father_name = document.getElementById('father_name').value;
                const father_cin = document.getElementById('father_cin').value;
                const father_occupation = document.getElementById('father_occupation').value;
                const mother_name = document.getElementById('mother_name').value;
                const mother_cin = document.getElementById('mother_cin').value;
                const mother_occupation = document.getElementById('mother_occupation').value;
                
                if (!birth_date || !birth_place || !father_name || !father_cin || !father_occupation || !mother_name || !mother_cin || !mother_occupation) {
                    alert('{{ __("Veuillez remplir tous les champs de l\'étape 2.") }}');
                    return false;
                }
                return true;
            }
        }" class="w-full max-w-4xl bg-white/10 dark:bg-slate-900/40 backdrop-blur-2xl border border-white/10 rounded-[3rem] p-8 lg:p-12 shadow-2xl relative overflow-hidden z-10 transition-all duration-500">

            <!-- Stepper Progress Header -->
            <div class="relative mb-10 flex justify-between items-center max-w-lg mx-auto">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-white/10 rounded-full z-0">
                    <div class="h-1 bg-gradient-to-r from-upf-magenta to-upf-blue rounded-full transition-all duration-500" 
                         :style="'width: ' + ((step - 1) / 2 * 100) + '%'"></div>
                </div>

                <!-- Step 1 Button -->
                <button type="button" @click="step = 1" class="relative z-10 flex flex-col items-center gap-2 focus:outline-none group">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black transition-all border-2"
                         :class="step >= 1 ? 'bg-gradient-to-r from-upf-magenta to-pink-500 border-transparent text-white shadow-[0_0_15px_rgba(230,0,126,0.4)]' : 'bg-slate-900 border-white/20 text-white/50'">
                        1
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-200" :class="step === 1 ? 'opacity-100' : 'opacity-60 group-hover:opacity-90'">{{ __('Identité') }}</span>
                </button>

                <!-- Step 2 Button -->
                <button type="button" @click="if (validateStep1()) step = 2" class="relative z-10 flex flex-col items-center gap-2 focus:outline-none group">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black transition-all border-2"
                         :class="step >= 2 ? 'bg-gradient-to-r from-upf-magenta to-pink-500 border-transparent text-white shadow-[0_0_15px_rgba(230,0,126,0.4)]' : 'bg-slate-900 border-white/20 text-white/50'">
                        2
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-200" :class="step === 2 ? 'opacity-100' : 'opacity-60 group-hover:opacity-90'">{{ __('Parents') }}</span>
                </button>

                <!-- Step 3 Button -->
                <button type="button" @click="if (validateStep1() && validateStep2()) step = 3" class="relative z-10 flex flex-col items-center gap-2 focus:outline-none group">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black transition-all border-2"
                         :class="step >= 3 ? 'bg-gradient-to-r from-upf-magenta to-pink-500 border-transparent text-white shadow-[0_0_15px_rgba(230,0,126,0.4)]' : 'bg-slate-900 border-white/20 text-white/50'">
                        3
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-200" :class="step === 3 ? 'opacity-100' : 'opacity-60 group-hover:opacity-90'">{{ __('Bac & Filière') }}</span>
                </button>
            </div>

            <!-- Validation Errors -->
            <x-alert-messages />
            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-500/20 border border-rose-500/30 rounded-2xl text-rose-350 text-xs font-bold space-y-1">
                    <p class="font-black">⚠️ {{ __('Certaines erreurs sont survenues :') }}</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Multi-step Form -->
            <form action="{{ route('inscription') }}" method="POST" class="space-y-8 font-bold">
                @csrf

                <!-- ================== STEP 1: ACCOUNT & IDENTITY ================== -->
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6">
                    <div class="border-b border-white/10 pb-4">
                        <h2 class="text-xl font-black italic">{{ __('Étape 1 : Compte & Identité') }}</h2>
                        <p class="text-blue-200/60 text-xs mt-1">{{ __('Remplissez vos identifiants de connexion et données personnelles.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom complet -->
                        <div class="space-y-2">
                            <label for="name" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Nom Complet') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="Ahmed El Fassi">
                        </div>

                        <!-- CIN -->
                        <div class="space-y-2">
                            <label for="cin" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('CIN (Carte d\'identité)') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="cin" id="cin" required value="{{ old('cin') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="AB123456">
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Adresse Email') }} <span class="text-upf-magenta">*</span></label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="candidat@email.com">
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <label for="password" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Mot de passe') }} <span class="text-upf-magenta">*</span></label>
                            <input type="password" name="password" id="password" required
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="••••••••">
                        </div>

                        <!-- Password Confirmation -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Confirmer le Mot de passe') }} <span class="text-upf-magenta">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" @click="if (validateStep1()) step = 2"
                                class="px-8 py-3.5 bg-gradient-to-r from-upf-magenta to-pink-500 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:scale-105 transform transition-all shadow-md">
                            {{ __('Continuer') }} →
                        </button>
                    </div>
                </div>

                <!-- ================== STEP 2: PARENTS & BIRTH ================== -->
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6" style="display: none;">
                    <div class="border-b border-white/10 pb-4">
                        <h2 class="text-xl font-black italic">{{ __('Étape 2 : Parents & Naissance') }}</h2>
                        <p class="text-blue-200/60 text-xs mt-1">{{ __('Saisissez vos données de naissance et les coordonnées de vos parents.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date de naissance -->
                        <div class="space-y-2">
                            <label for="birth_date" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Date de Naissance') }} <span class="text-upf-magenta">*</span></label>
                            <input type="date" name="birth_date" id="birth_date" required value="{{ old('birth_date') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none">
                        </div>

                        <!-- Lieu de naissance -->
                        <div class="space-y-2">
                            <label for="birth_place" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Lieu de Naissance') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="birth_place" id="birth_place" required value="{{ old('birth_place') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="Fès">
                        </div>

                        <!-- Father header -->
                        <div class="col-span-1 md:col-span-2 border-t border-white/5 pt-4">
                            <p class="text-[10px] uppercase font-black text-pink-400 tracking-wider">👨 {{ __('Informations du Père') }}</p>
                        </div>

                        <div class="space-y-2">
                            <label for="father_name" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Nom & Prénom du Père') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="father_name" id="father_name" required value="{{ old('father_name') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="Nom Complet">
                        </div>

                        <div class="space-y-2">
                            <label for="father_cin" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('CIN du Père') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="father_cin" id="father_cin" required value="{{ old('father_cin') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="CD123456">
                        </div>

                        <div class="space-y-2 col-span-1 md:col-span-2">
                            <label for="father_occupation" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Profession du Père') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="father_occupation" id="father_occupation" required value="{{ old('father_occupation') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="Ex: Fonctionnaire, Ingénieur, Retraité, etc.">
                        </div>

                        <!-- Mother header -->
                        <div class="col-span-1 md:col-span-2 border-t border-white/5 pt-4">
                            <p class="text-[10px] uppercase font-black text-pink-400 tracking-wider">👩 {{ __('Informations de la Mère') }}</p>
                        </div>

                        <div class="space-y-2">
                            <label for="mother_name" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Nom & Prénom de la Mère') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="mother_name" id="mother_name" required value="{{ old('mother_name') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="Nom Complet">
                        </div>

                        <div class="space-y-2">
                            <label for="mother_cin" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('CIN de la Mère') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="mother_cin" id="mother_cin" required value="{{ old('mother_cin') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="EF654321">
                        </div>

                        <div class="space-y-2 col-span-1 md:col-span-2">
                            <label for="mother_occupation" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Profession de la Mère') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="mother_occupation" id="mother_occupation" required value="{{ old('mother_occupation') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="Ex: Enseignante, Foyer, Employée, etc.">
                        </div>
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" @click="step = 1"
                                class="px-6 py-3.5 bg-white/10 border border-white/10 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-white/20 transition-all">
                            ← {{ __('Précédent') }}
                        </button>
                        <button type="button" @click="if (validateStep2()) step = 3"
                                class="px-8 py-3.5 bg-gradient-to-r from-upf-magenta to-pink-500 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:scale-105 transform transition-all shadow-md">
                            {{ __('Continuer') }} →
                        </button>
                    </div>
                </div>

                <!-- ================== STEP 3: BAC & FILIERE ================== -->
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6" style="display: none;">
                    <div class="border-b border-white/10 pb-4">
                        <h2 class="text-xl font-black italic">{{ __('Étape 3 : Cursus du Baccalauréat & Choix de Filière') }}</h2>
                        <p class="text-blue-200/60 text-xs mt-1">{{ __('Déclarez vos notes d\'études secondaires et sélectionnez votre filière d\'inscription.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Filière Bac -->
                        <div class="space-y-2">
                            <label for="bac_filiere" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Série du Baccalauréat') }} <span class="text-upf-magenta">*</span></label>
                            <input type="text" name="bac_filiere" id="bac_filiere" required value="{{ old('bac_filiere') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="Ex: Sciences Mathématiques A, Physique-Chimie, etc.">
                        </div>

                        <!-- Note Bac -->
                        <div class="space-y-2">
                            <label for="bac_grade" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Moyenne du Bac') }} <span class="text-upf-magenta">*</span></label>
                            <input type="number" step="0.01" min="10" max="20" name="bac_grade" id="bac_grade" required value="{{ old('bac_grade') }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="15.50">
                        </div>

                        <!-- Mention Bac -->
                        <div class="space-y-2">
                            <label for="bac_mention" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Mention du Bac') }} <span class="text-upf-magenta">*</span></label>
                            <select name="bac_mention" id="bac_mention" required
                                    class="w-full bg-slate-950 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none font-bold">
                                <option value="" disabled selected>{{ __('Sélectionnez la mention') }}</option>
                                <option value="Passable">{{ __('Passable') }}</option>
                                <option value="Assez Bien">{{ __('Assez Bien') }}</option>
                                <option value="Bien">{{ __('Bien') }}</option>
                                <option value="Très Bien">{{ __('Très Bien') }}</option>
                            </select>
                        </div>

                        <!-- Année Bac -->
                        <div class="space-y-2">
                            <label for="bac_year" class="text-[10px] uppercase font-black tracking-widest text-blue-200 block">{{ __('Année d\'Obtention') }} <span class="text-upf-magenta">*</span></label>
                            <input type="number" name="bac_year" id="bac_year" required min="2010" max="{{ date('Y') }}" value="{{ old('bac_year', date('Y')) }}"
                                   class="w-full bg-slate-950/50 border-white/10 focus:border-upf-magenta focus:ring-upf-magenta rounded-xl text-xs text-white p-3.5 transition-all outline-none"
                                   placeholder="2025">
                        </div>

                        <!-- Filière Cible UPF -->
                        <div class="col-span-1 md:col-span-2 space-y-3 pt-4 border-t border-white/5">
                            <label for="filiere_id" class="text-[10px] uppercase font-black tracking-widest text-pink-400 block">{{ __('Filière demandée à l\'UPF') }} <span class="text-upf-magenta">*</span></label>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($filieres as $filiere)
                                    <label class="relative flex items-center p-4 bg-slate-950/40 border border-white/10 rounded-2xl cursor-pointer hover:border-upf-blue transition-all group shadow-sm">
                                        <input type="radio" name="filiere_id" value="{{ $filiere->id }}" class="sr-only peer" required {{ old('filiere_id') == $filiere->id ? 'checked' : '' }}>
                                        <div class="w-5 h-5 border-2 border-white/20 rounded-full mr-3 flex items-center justify-center transition-all peer-checked:border-upf-blue peer-checked:bg-upf-blue">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-xs text-white group-hover:text-blue-400 transition-colors">{{ $filiere->name }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Final Submission Buttons -->
                    <div class="flex justify-between pt-6 border-t border-white/10">
                        <button type="button" @click="step = 2"
                                class="px-6 py-3.5 bg-white/10 border border-white/10 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-white/20 transition-all">
                            ← {{ __('Précédent') }}
                        </button>
                        <button type="submit"
                                class="px-10 py-4 bg-gradient-to-r from-upf-magenta to-indigo-600 text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:scale-105 transform transition-all shadow-lg hover:shadow-indigo-500/20">
                            🚀 {{ __('Soumettre mon Inscription') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-blue-200/50 font-bold z-10">
            {{ __('Vous avez déjà un compte ?') }} 
            <a href="{{ route('login') }}" class="text-pink-400 hover:text-white transition-colors underline font-black">{{ __('Se connecter') }}</a>
        </p>
    </div>
</x-guest-layout>
