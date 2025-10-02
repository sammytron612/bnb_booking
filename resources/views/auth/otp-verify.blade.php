<x-layouts.app>
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verify Your Identity
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                We've sent a 6-digit verification code to
                <span class="font-medium text-blue-600">{{ $email }}</span>
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('otp.verify') }}" method="POST">
            @csrf

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div>
                <label for="otp_code" class="sr-only">OTP Code</label>
                <div class="relative">
                    <input id="otp_code"
                           name="otp_code"
                           type="text"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           autocomplete="off"
                           required
                           class="appearance-none rounded-lg relative block w-full px-3 py-4 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 text-center text-2xl font-mono tracking-widest"
                           placeholder="000000"
                           value="{{ old('otp_code') }}">
                </div>
                <p class="mt-2 text-sm text-gray-500 text-center">
                    Enter the 6-digit code sent to your email
                </p>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                    </span>
                    Verify Code
                </button>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Security Notice
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>This code expires in 10 minutes</li>
                                <li>Never share this code with anyone</li>
                                <li>Each code can only be used once</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Resend Code Section - Outside main form -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Didn't receive the code?
            </p>

            @if($canResend)
                <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="font-medium text-blue-600 hover:text-blue-500 transition duration-150 ease-in-out">
                        Resend Code
                    </button>
                </form>
            @else
                <span class="text-gray-400">
                    Please wait 30 seconds before requesting a new code
                </span>
            @endif
        </div>        <div class="text-center">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-sm text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
                Sign out instead
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp_code');

    // Auto-focus the input
    otpInput.focus();

    // Format input as user types
    otpInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 6) {
            value = value.slice(0, 6);
        }
        e.target.value = value;

        // Auto-submit when 6 digits are entered
        if (value.length === 6) {
            setTimeout(() => {
                e.target.closest('form').submit();
            }, 500);
        }
    });

    // Handle paste
    otpInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const numbers = paste.replace(/[^0-9]/g, '').slice(0, 6);
        e.target.value = numbers;

        if (numbers.length === 6) {
            setTimeout(() => {
                e.target.closest('form').submit();
            }, 500);
        }
    });
});
</script>
</x-layouts.app>
