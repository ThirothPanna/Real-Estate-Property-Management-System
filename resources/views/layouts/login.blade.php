<x-guest-layout>
    <!-- Main Card -->
    <div class="w-full sm:max-w-[440px]">
        
        <!-- Titles -->
        <h1 class="text-[26px] font-bold text-gray-800 mb-2">Sign in</h1>
        <p class="text-[15px] text-gray-600 mb-8">Sign in to your rental management software.</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Social Login Buttons -->
        <div class="space-y-4 mb-8">
            <!-- Google -->
            <button type="button" class="w-full flex items-center justify-center gap-3 px-4 py-3.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                <span class="text-gray-700 font-medium text-[15px]">Continue with Google</span>
            </button>
            <!-- Apple -->
            <button type="button" class="w-full flex items-center justify-center gap-3 px-4 py-3.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.04 2.26-.79 3.56-.7 1.5.11 2.66.63 3.42 1.76-3.17 1.9-2.5 5.75.55 6.96-.71 1.83-1.66 3.54-2.61 4.15zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                </svg>
                <span class="text-gray-700 font-medium text-[15px]">Continue with Apple</span>
            </button>
            <!-- Facebook -->
            <button type="button" class="w-full flex items-center justify-center gap-3 px-4 py-3.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span class="text-gray-700 font-medium text-[15px]">Continue with Facebook</span>
            </button>
        </div>

        <!-- Divider -->
        <div class="relative mb-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-[13px]">
                <span class="px-3 bg-white text-gray-500">Or</span>
            </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-5">
                <label for="email" class="block text-[13px] font-medium text-gray-700 mb-2">Email <span class="text-gray-500">*</span></label>
                <input id="email" 
                       class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-3.5 px-4 text-[15px] placeholder-gray-400" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       placeholder="Enter your email" 
                       required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-5">
                <label for="password" class="block text-[13px] font-medium text-gray-700 mb-2">Password <span class="text-gray-500">*</span></label>
                <div class="relative">
                    <input id="password" 
                           class="block w-full rounded-lg border-gray-200 shadow-sm focus:border-[#3f9c3a] focus:ring-[#3f9c3a] py-3.5 px-4 pr-12 text-[15px] placeholder-gray-400" 
                           type="password" 
                           name="password" 
                           placeholder="Enter your password" 
                           required autocomplete="current-password" />
                    
                    <!-- Eye Icon (Crossed out like in image) -->
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                        <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mt-5 mb-8">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <!-- Custom Green Checkbox -->
                    <div class="relative flex items-center">
                        <input id="remember_me" type="checkbox" class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-gray-300 bg-white checked:border-[#3f9c3a] checked:bg-[#3f9c3a] transition-all" name="remember">
                        <svg class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="ml-2.5 text-[14px] text-gray-700">Keep signed in for 60 days</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-[14px] font-bold text-[#3f9c3a] hover:text-[#34852f]" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-lg shadow-sm text-[15px] font-bold text-white bg-[#3f9c3a] hover:bg-[#34852f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3f9c3a] transition duration-200">
                Sign in
            </button>
            
            <!-- Recaptcha Text -->
            <p class="mt-6 text-[13px] text-gray-500 leading-relaxed">
                This site is protected by reCAPTCHA and the Google 
                <a href="#" class="text-[#3f9c3a] hover:underline font-medium">Privacy Policy</a> and 
                <a href="#" class="text-[#3f9c3a] hover:underline font-medium">Terms of Service</a> apply.
            </p>
        </form>
    </div>

    <!-- Password Toggle Script (swaps between eye and crossed eye) -->
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                // Change to regular eye icon
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            } else {
                input.type = 'password';
                // Change back to crossed eye icon
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            }
        }
    </script>
</x-guest-layout>