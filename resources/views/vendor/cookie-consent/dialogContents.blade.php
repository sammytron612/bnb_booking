<div class="js-cookie-consent cookie-consent fixed bottom-0 inset-x-0 pb-2 px-2 sm:pb-4 sm:px-4 z-50">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-3 sm:p-4 md:p-6">
            <!-- Mobile Layout: Stack vertically -->
            <div class="block sm:hidden">
                <div class="flex items-start space-x-2 mb-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs leading-relaxed text-gray-700 dark:text-gray-300 cookie-consent__message">
                            {!! trans('cookie-consent::texts.message') !!}
                        </p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button class="js-cookie-consent-decline cookie-consent__decline cursor-pointer flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                        Decline
                    </button>
                    <button class="js-cookie-consent-agree cookie-consent__agree cursor-pointer flex-1 inline-flex items-center justify-center px-3 py-2 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                        {{ trans('cookie-consent::texts.agree') }}
                    </button>
                </div>
            </div>

            <!-- Desktop Layout: Horizontal -->
            <div class="hidden sm:flex items-center justify-between flex-wrap gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 dark:text-gray-300 cookie-consent__message">
                                {!! trans('cookie-consent::texts.message') !!}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0 flex space-x-3">
                    <button class="js-cookie-consent-decline cookie-consent__decline cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                        Decline
                    </button>
                    <button class="js-cookie-consent-agree cookie-consent__agree cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                        {{ trans('cookie-consent::texts.agree') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle decline button
    const declineButton = document.querySelector('.js-cookie-consent-decline');
    if (declineButton) {
        declineButton.addEventListener('click', function() {
            // Set a cookie to remember the decline choice
            document.cookie = 'laravel_cookie_consent=declined; path=/; max-age=' + (365 * 24 * 60 * 60) + '; SameSite=Lax';

            // Hide the banner
            const banner = document.querySelector('.js-cookie-consent');
            if (banner) {
                banner.style.display = 'none';
            }

            // Set global flag for declined cookies
            window.laravelCookieConsent = false;

            // Dispatch custom event
            document.dispatchEvent(new CustomEvent('cookie-consent-declined'));
        });
    }
});
</script>
