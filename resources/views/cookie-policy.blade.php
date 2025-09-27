<x-layouts.app.header>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-8">
                <h1 class="text-3xl font-bold text-white">Cookie Policy</h1>
                <p class="text-amber-100 mt-2">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <div class="px-6 py-8 prose prose-lg max-w-none">
                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. What Are Cookies?</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Cookies are small text files that are stored on your computer or mobile device when you visit our website.
                        They help us provide you with a better browsing experience by remembering your preferences and enabling
                        certain functionality on our site.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. How We Use Cookies</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Seaham Coastal Retreats uses cookies to:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Remember your booking preferences and form data</li>
                        <li>Keep you logged in to your account (if applicable)</li>
                        <li>Analyze website traffic and user behavior</li>
                        <li>Improve our website performance and user experience</li>
                        <li>Provide personalized content and recommendations</li>
                        <li>Ensure website security and prevent fraud</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Types of Cookies We Use</h2>

                    <div class="space-y-6">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <h3 class="text-lg font-semibold text-blue-800 mb-2">Essential Cookies</h3>
                            <p class="text-blue-700 text-sm mb-2">
                                <strong>Purpose:</strong> These cookies are necessary for the website to function properly.
                            </p>
                            <p class="text-blue-700 text-sm">
                                <strong>Examples:</strong> Session management, security tokens, CSRF protection, booking form data
                            </p>
                        </div>

                        <div class="bg-green-50 border-l-4 border-green-400 p-4">
                            <h3 class="text-lg font-semibold text-green-800 mb-2">Functional Cookies</h3>
                            <p class="text-green-700 text-sm mb-2">
                                <strong>Purpose:</strong> These cookies enhance website functionality and personalization.
                            </p>
                            <p class="text-green-700 text-sm">
                                <strong>Examples:</strong> Language preferences, user settings, remembering login status
                            </p>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Analytics Cookies</h3>
                            <p class="text-yellow-700 text-sm mb-2">
                                <strong>Purpose:</strong> These cookies help us understand how visitors use our website.
                            </p>
                            <p class="text-yellow-700 text-sm">
                                <strong>Examples:</strong> Google Analytics, page views, user journey tracking, performance metrics
                            </p>
                        </div>

                        <div class="bg-purple-50 border-l-4 border-purple-400 p-4">
                            <h3 class="text-lg font-semibold text-purple-800 mb-2">Marketing Cookies</h3>
                            <p class="text-purple-700 text-sm mb-2">
                                <strong>Purpose:</strong> These cookies may be used to show you relevant advertisements.
                            </p>
                            <p class="text-purple-700 text-sm">
                                <strong>Examples:</strong> Social media pixels, advertising networks, remarketing tags
                            </p>
                        </div>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Specific Cookies We Use</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">Cookie Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">Purpose</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">Duration</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-4 py-4 text-sm text-gray-900 border-r border-gray-300">laravel_session</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">Maintains user session and form data</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">2 hours</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">Essential</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-900 border-r border-gray-300">XSRF-TOKEN</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">Security protection against CSRF attacks</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">2 hours</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">Essential</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-4 text-sm text-gray-900 border-r border-gray-300">_ga</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">Google Analytics - Distinguishes users</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">2 years</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">Analytics</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-900 border-r border-gray-300">_ga_*</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">Google Analytics - Session and campaign data</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">2 years</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">Analytics</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-4 text-sm text-gray-900 border-r border-gray-300">booking_preferences</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">Remembers booking form preferences</td>
                                    <td class="px-4 py-4 text-sm text-gray-700 border-r border-gray-300">30 days</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">Functional</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Third-Party Cookies</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Our website may also use third-party cookies from:
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Google Analytics</p>
                                <p class="text-sm text-gray-600">Helps us understand website usage and improve user experience</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Stripe</p>
                                <p class="text-sm text-gray-600">Secure payment processing for booking transactions</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Social Media Platforms</p>
                                <p class="text-sm text-gray-600">Enable social media sharing and integration features</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Managing Your Cookie Preferences</h2>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">Browser Settings</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        You can control and/or delete cookies as you wish. You can delete all cookies that are already on your
                        computer and you can set most browsers to prevent them from being placed.
                    </p>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="font-medium text-gray-800 mb-2">Popular Browser Cookie Settings:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><strong>Chrome:</strong> Settings → Privacy and security → Cookies and other site data</li>
                            <li><strong>Firefox:</strong> Options → Privacy & Security → Cookies and Site Data</li>
                            <li><strong>Safari:</strong> Preferences → Privacy → Cookies and website data</li>
                            <li><strong>Edge:</strong> Settings → Cookies and site permissions → Cookies and site data</li>
                        </ul>
                    </div>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">Opt-Out Links</h3>
                    <div class="space-y-2">
                        <p class="text-gray-700">
                            <strong>Google Analytics:</strong>
                            <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener noreferrer"
                               class="text-blue-600 hover:text-blue-800 underline">
                                Google Analytics Opt-out Browser Add-on
                            </a>
                        </p>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Impact of Disabling Cookies</h2>
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800">Please Note</h3>
                                <p class="mt-1 text-sm text-amber-700">
                                    If you disable cookies, some features of our website may not function properly, including:
                                </p>
                                <ul class="mt-2 text-sm text-amber-700 list-disc list-inside space-y-1">
                                    <li>Booking form functionality and data retention</li>
                                    <li>User authentication and session management</li>
                                    <li>Personalized content and preferences</li>
                                    <li>Website analytics and performance improvements</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Updates to This Cookie Policy</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We may update this Cookie Policy from time to time to reflect changes in our practices or for other
                        operational, legal, or regulatory reasons. We will notify you of any changes by posting the new Cookie
                        Policy on this page and updating the "Last updated" date.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Contact Us</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        If you have any questions about our use of cookies or this Cookie Policy, please contact us:
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="font-medium text-gray-800">Seaham Coastal Retreats</p>
                        <p class="text-gray-700">Email: <a href="mailto:{{ env('OWNER_EMAIL') }}" class="text-blue-600 hover:text-blue-800">{{ env('OWNER_EMAIL') }}</a></p>
                        <p class="text-gray-700">Phone: <a href="tel:{{ env('OWNER_PHONE_NO') }}" class="text-blue-600 hover:text-blue-800">{{ env('OWNER_PHONE_NO') }}</a></p>
                        <p class="text-gray-700">Address: Seaham, County Durham, United Kingdom</p>
                    </div>
                </section>

                <div class="border-t pt-6 mt-8">
                    <p class="text-sm text-gray-600">
                        By continuing to use our website, you consent to our use of cookies as described in this Cookie Policy.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app.header>
