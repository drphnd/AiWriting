<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Create Account</h2>
        <p class="text-sm text-gray-500 mt-2">Get started with your free account today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Full Name')" class="mb-1 text-gray-600 font-medium" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="user" class="h-4 w-4 text-gray-400"></i>
                </div>
                <input id="name" class="pl-10 block mt-1 w-full border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="mb-1 text-gray-600 font-medium" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="mail" class="h-4 w-4 text-gray-400"></i>
                </div>
                <input id="email" class="pl-10 block mt-1 w-full border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="mb-1 text-gray-600 font-medium" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="lock" class="h-4 w-4 text-gray-400"></i>
                </div>
                <input id="password" class="pl-10 block mt-1 w-full border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="mb-1 text-gray-600 font-medium" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="check-circle" class="h-4 w-4 text-gray-400"></i>
                </div>
                <input id="password_confirmation" class="pl-10 block mt-1 w-full border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                {{ __('Create Account') }}
            </button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 hover:underline ml-1">
                Log in
            </a>
        </p>
    </div>
</x-guest-layout>