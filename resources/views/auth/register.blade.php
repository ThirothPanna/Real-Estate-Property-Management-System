<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Role Selection -->
        <div>
            <x-input-label for="role" :value="__('I am a')" />

            <div class="mt-2 grid grid-cols-2 gap-3">
                <!-- Tenant -->
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="tenant"
                           class="sr-only peer"
                           {{ old('role', 'tenant') === 'tenant' ? 'checked' : '' }}>
                    <div class="border-2 rounded-lg p-4 text-center transition-all
                                border-gray-200 bg-white
                                peer-checked:border-[#3f9c3a] peer-checked:bg-[#f0fdf4]
                                hover:border-[#3f9c3a]">
                        <div class="text-2xl mb-2">🏠</div>
                        <div class="font-semibold text-gray-800 text-[15px]">Tenant</div>
                        <div class="text-xs text-gray-500 mt-1">I rent a property</div>
                    </div>
                </label>

                <!-- Landlord -->
                <label class="cursor-pointer">
                    <input type="radio" name="role" value="landlord"
                           class="sr-only peer"
                           {{ old('role') === 'landlord' ? 'checked' : '' }}>
                    <div class="border-2 rounded-lg p-4 text-center transition-all
                                border-gray-200 bg-white
                                peer-checked:border-[#3f9c3a] peer-checked:bg-[#f0fdf4]
                                hover:border-[#3f9c3a]">
                        <div class="text-2xl mb-2">🔑</div>
                        <div class="font-semibold text-gray-800 text-[15px]">Landlord</div>
                        <div class="text-xs text-gray-500 mt-1">I own a property</div>
                    </div>
                </label>
            </div>

            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>