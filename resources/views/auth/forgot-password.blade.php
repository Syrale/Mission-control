<x-guest-layout>
    <div class="mb-6 text-center">
        <span class="text-4xl">📡</span>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            Signal Lost?
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Enter your comms frequency (email) and we will send a recovery link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Send Recovery Signal') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>