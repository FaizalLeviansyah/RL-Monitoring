<x-guest-layout>

    <div class="flex flex-col items-center mb-8 relative group">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full blur-xl opacity-20 group-hover:opacity-40 transition duration-500"></div>
        <div class="relative p-4 bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl shadow-blue-500/10 mb-4 border border-white transform group-hover:scale-105 transition duration-500">
            <img src="{{ asset('images/Logo_PT_ASM.jpg') }}" alt="Logo" class="h-16 w-auto mix-blend-multiply">
        </div>
        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Login Access</h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Secure Procurement Gateway</p>
    </div>

    <div class="glass-panel rounded-3xl shadow-[0_20px_50px_rgba(8,_112,_184,_0.1)] p-8 relative overflow-hidden transition-all hover:shadow-[0_20px_50px_rgba(8,_112,_184,_0.2)]">

        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-600 shadow-[0_0_15px_rgba(6,182,212,0.5)]"></div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6 mt-4">
            @csrf

            <div class="group">
                <x-input-label for="email" :value="__('Email Address')" class="text-slate-600 font-bold text-xs uppercase tracking-wider mb-1" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                    </div>
                    <input id="email" class="block w-full pl-10 pr-4 py-3 bg-white/50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent focus:shadow-[0_0_15px_rgba(34,211,238,0.3)] transition-all duration-300" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="executive@amarin.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="group">
                <x-input-label for="password" :value="__('Password')" class="text-slate-600 font-bold text-xs uppercase tracking-wider mb-1" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <input id="password" class="block w-full pl-10 pr-4 py-3 bg-white/50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent focus:shadow-[0_0_15px_rgba(34,211,238,0.3)] transition-all duration-300" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 cursor-pointer" name="remember">
                    <span class="ms-2 text-slate-500 group-hover:text-blue-600 transition-colors">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-slate-500 hover:text-blue-600 font-medium transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full relative overflow-hidden group py-3.5 px-4 rounded-xl shadow-lg shadow-blue-500/30 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-blue-500/50">
                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600"></div>
                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-[100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                <span class="relative text-white font-bold tracking-wide flex justify-center items-center">
                    SIGN IN TO DASHBOARD
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
            </button>
        </form>
    </div>

    <div class="mt-8 text-center">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">&copy; {{ date('Y') }} PT Amarin Ship Management</p>
    </div>
</x-guest-layout>
