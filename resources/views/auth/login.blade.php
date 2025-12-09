<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900">Welcome Back</h2>
        <p class="text-sm text-slate-500 mt-2">Sign in to access your writing assistant</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="mail" class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username" 
                       placeholder="name@company.com" 
                       class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:bg-white transition-all duration-200"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="lock" class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password" 
                       placeholder="••••••••" 
                       class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:bg-white transition-all duration-200"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Keep me logged in') }}</span>
            </label>
        </div>

        <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition-all duration-200">
            Sign In
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center">
        <p class="text-sm text-slate-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition-colors ml-1">
                Create free account
            </a>
        </p>
    </div>
</x-guest-layout>