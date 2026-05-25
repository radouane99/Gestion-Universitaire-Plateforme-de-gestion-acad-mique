<section>
    <header>
        <h2 class="text-2xl font-black text-upf-blue italic tracking-tighter">
            {{ __('Profile Mastery') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500 font-medium uppercase tracking-widest text-[10px]">
            {{ __("Manage your academic identity and contact protocols.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-10 space-y-8" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div class="flex items-center gap-10 bg-gray-100/50 p-8 rounded-[2.5rem] border border-dashed border-gray-200 group">
            <div class="relative">
                <div class="w-32 h-32 rounded-[2.5rem] overflow-hidden shadow-2xl bg-white border-4 border-white group-hover:rotate-3 transition-transform duration-500">
                    @if($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-indigo-50 text-upf-blue text-4xl font-black italic">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <!-- Status Badge -->
                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-upf-magenta border-4 border-white rounded-full"></div>
            </div>
            
            <div class="flex-1">
                <x-input-label for="photo" :value="__('Academic Portrait')" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4" />
                <input id="photo" name="photo" type="file" class="hidden" onchange="document.getElementById('photo-name').innerText = this.files[0].name" />
                <label for="photo" class="cursor-pointer inline-flex items-center px-6 py-3 bg-white border border-gray-200 rounded-xl font-bold text-xs text-upf-blue uppercase tracking-widest hover:bg-upf-blue hover:text-white transition-all shadow-sm">
                    Upload New Image
                </label>
                <p id="photo-name" class="mt-2 text-[10px] font-bold text-gray-400 italic"></p>
                <x-input-error class="mt-2" :messages="$errors->get('photo')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
            <!-- Name -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <x-input-label for="name" :value="__('Full Legal Name')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full !border-none !bg-gray-50 !rounded-xl !p-4 font-bold" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <!-- Email -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <x-input-label for="email" :value="__('Academic Email')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full !border-none !bg-gray-50 !rounded-xl !p-4 font-bold" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4">
                        <p class="text-sm text-upf-magenta font-bold">
                            {{ __('Verification Pending.') }}

                            <button form="send-verification" class="underline hover:text-upf-blue">
                                {{ __('Resend Protocol.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been dispatched.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4 pt-6 border-t border-gray-50">
            <x-primary-button class="!bg-gradient-to-r !from-upf-blue !to-upf-navy !px-10 !py-4 !rounded-2xl !font-black">
                {{ __('Commit Changes') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-gray-400 italic"
                >{{ __('Identity updated.') }}</p>
            @endif
        </div>
    </form>
</section>
