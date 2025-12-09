<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900">Create Account</h2>
        <p class="text-sm text-slate-500 mt-2">Join us to start generating content</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="user" class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                </div>
                <input id="name" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       autocomplete="name" 
                       placeholder="John Doe" 
                       class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:bg-white transition-all duration-200"
                />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

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
                       autocomplete="username" 
                       placeholder="name@company.com" 
                       class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:bg-white transition-all duration-200"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="lock" class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password" 
                       placeholder="Min. 8 characters" 
                       class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:bg-white transition-all duration-200"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="check-circle-2" class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                </div>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password" 
                       placeholder="Re-enter password" 
                       class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:bg-white transition-all duration-200"
                />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition-all duration-200">
            Create Account
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center">
        <p class="text-sm text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition-colors ml-1">
                Sign in here
            </a>
        </p>
    </div>
</x-guest-layout>