<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email / Username')" />
            <x-text-input id="email" class="block mt-1 w-full px-4 py-3 rounded-lg border-gray-200 focus:border-[#0b1c3c] focus:ring-[#0b1c3c]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="admin@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                 <x-input-label for="password" :value="__('Password')" />
                 @if (Route::has('password.request'))
                    <a class="text-sm text-red-600 hover:text-red-800 font-medium transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1 w-full px-4 py-3 rounded-lg border-gray-200 focus:border-[#0b1c3c] focus:ring-[#0b1c3c]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-[#0b1c3c] shadow-sm focus:ring-[#0b1c3c] transition ease-in-out duration-150" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:text-gray-100 transition-colors">{{ __('Keep me logged in') }}</span>
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#0b1c3c] hover:bg-[#1a2e55] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0b1c3c] transition-colors duration-200">
                {{ __('Secure Login') }}
            </button>
        </div>
    </form>
</x-guest-layout>
