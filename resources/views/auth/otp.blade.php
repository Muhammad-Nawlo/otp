<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <div>
            <x-input-label for="otp" :value="__('Otp')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="number"  name="otp" :value="old('otp')" required autofocus />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('otp.resend') }}">
                    {{ __('Resend OTP') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Send') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
