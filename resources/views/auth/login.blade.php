<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AI Writing Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center overflow-hidden bg-slate-50 relative">

    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 z-0"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-slate-50 to-purple-50 z-0"></div>

    <div class="w-full max-w-md bg-white/80 backdrop-blur-xl border border-white/20 shadow-xl rounded-2xl p-8 relative z-10 mx-4 ring-1 ring-slate-900/5">
        
        <div class="mb-8 text-center">
            <div class="mx-auto w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-indigo-200">
                <i data-lucide="sparkles" class="w-6 h-6 text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome Back</h2>
            <p class="text-sm text-slate-500 mt-2">Sign in to access your writing assistant</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-medium border border-emerald-100 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
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
                           class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:bg-white transition-all duration-200"
                    />
                </div>
                @error('email')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Password</label>
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
                           class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 hover:bg-white transition-all duration-200"
                    />
                </div>
                @error('password')
                    <p class="mt-2 text-xs text-red-500 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="block">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4" name="remember">
                    <span class="ms-2 text-sm text-slate-600 select-none">{{ __('Keep me logged in') }}</span>
                </label>
            </div>

            <button type="submit" class="w-full flex justify-center items-center py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-200 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                Sign In
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition-colors ml-1">
                    Create free account
                </a>
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>