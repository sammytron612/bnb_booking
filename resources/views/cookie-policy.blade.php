<x-layouts.app title="Cookie Policy - {{ config('app.name') }}">
    <div class="bg-white dark:bg-zinc-800 min-h-screen">
        <div class="container mx-auto px-4 py-12">
            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-800 dark:text-slate-100 mb-4">Cookie Policy</h1>
                <p class="text-lg text-slate-600 dark:text-slate-300">
                    How we use cookies and similar technologies on our website
                </p>
                <div class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                    Last updated: {{ date('F j, Y') }}
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-8 space-y-8">
                    {{-- What are Cookies --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">1. What are Cookies?</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                            Cookies are small text files that are placed on your computer or mobile device when you visit our website.
                            They are widely used to make websites work more efficiently and to provide information to website owners.
                        </p>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            Similar technologies include web beacons, pixels, and local storage, which we may also use to
                            enhance your experience on our website.
                        </p>
                    </section>

                    {{-- How We Use Cookies --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">2. How We Use Cookies</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                            We use cookies for several purposes to improve your experience on our website:
                        </p>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2">
                            <li><strong>Essential Functionality:</strong> To enable core website features like booking forms and payment processing</li>
                            <li><strong>User Preferences:</strong> To remember your settings and preferences</li>
                            <li><strong>Security:</strong> To protect against fraud and maintain secure sessions</li>
                            <li><strong>Performance:</strong> To analyze how our website is used and improve its performance</li>
                            <li><strong>Analytics:</strong> To understand visitor behavior and optimize our content</li>
                        </ul>
                    </section>

                    {{-- Types of Cookies --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">3. Types of Cookies We Use</h2>

                        <div class="space-y-6">
                            {{-- Strictly Necessary Cookies --}}
                            <div class="bg-slate-100 dark:bg-slate-600 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-3">3.1 Strictly Necessary Cookies</h3>
                                <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                                    These cookies are essential for the website to function properly. They cannot be switched off.
                                </p>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="border-b border-slate-300 dark:border-slate-500">
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">Cookie Name</th>
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">Purpose</th>
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">Duration</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-slate-600 dark:text-slate-300 text-sm">
                                            <tr class="border-b border-slate-200 dark:border-slate-600">
                                                <td class="py-2">laravel_session</td>
                                                <td class="py-2">Maintains user session and authentication</td>
                                                <td class="py-2">2 hours</td>
                                            </tr>
                                            <tr class="border-b border-slate-200 dark:border-slate-600">
                                                <td class="py-2">XSRF-TOKEN</td>
                                                <td class="py-2">Security protection against CSRF attacks</td>
                                                <td class="py-2">2 hours</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2">csrf_token</td>
                                                <td class="py-2">Form security validation</td>
                                                <td class="py-2">Session</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Performance Cookies --}}
                            <div class="bg-slate-100 dark:bg-slate-600 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-3">3.2 Performance and Analytics Cookies</h3>
                                <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                                    These cookies help us understand how visitors interact with our website.
                                </p>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="border-b border-slate-300 dark:border-slate-500">
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">Service</th>
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">Purpose</th>
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">Duration</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-slate-600 dark:text-slate-300 text-sm">
                                            <tr class="border-b border-slate-200 dark:border-slate-600">
                                                <td class="py-2">Google Analytics</td>
                                                <td class="py-2">Website traffic and user behavior analysis</td>
                                                <td class="py-2">Up to 2 years</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2">Google Maps</td>
                                                <td class="py-2">Location services and map functionality</td>
                                                <td class="py-2">Session</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Third-Party Cookies --}}
                            <div class="bg-slate-100 dark:bg-slate-600 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-3">3.3 Third-Party Service Cookies</h3>
                                <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                                    We use trusted third-party services that may set their own cookies.
                                </p>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="border-b border-slate-300 dark:border-slate-500">
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">Service</th>
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">Purpose</th>
                                                <th class="text-left py-2 text-slate-700 dark:text-slate-200">More Information</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-slate-600 dark:text-slate-300 text-sm">
                                            <tr class="border-b border-slate-200 dark:border-slate-600">
                                                <td class="py-2">Stripe</td>
                                                <td class="py-2">Secure payment processing and fraud prevention</td>
                                                <td class="py-2">
                                                    <a href="https://stripe.com/privacy" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Stripe Privacy Policy</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-2">Bunny Fonts</td>
                                                <td class="py-2">Font loading and display</td>
                                                <td class="py-2">
                                                    <a href="https://fonts.bunny.net/privacy" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Bunny Fonts Privacy</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Managing Cookies --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">4. Managing Your Cookie Preferences</h2>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">4.1 Browser Settings</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            Most browsers allow you to control cookies through their settings. You can:
                        </p>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2 mb-4">
                            <li>Block all cookies</li>
                            <li>Block third-party cookies</li>
                            <li>Delete existing cookies</li>
                            <li>Set preferences for specific websites</li>
                        </ul>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">4.2 Browser-Specific Instructions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-slate-100 dark:bg-slate-600 rounded p-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-200 mb-2">Google Chrome</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-300">Settings → Privacy and Security → Cookies</p>
                            </div>
                            <div class="bg-slate-100 dark:bg-slate-600 rounded p-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-200 mb-2">Mozilla Firefox</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-300">Options → Privacy & Security → Cookies</p>
                            </div>
                            <div class="bg-slate-100 dark:bg-slate-600 rounded p-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-200 mb-2">Safari</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-300">Preferences → Privacy → Cookies and website data</p>
                            </div>
                            <div class="bg-slate-100 dark:bg-slate-600 rounded p-4">
                                <h4 class="font-medium text-slate-700 dark:text-slate-200 mb-2">Microsoft Edge</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-300">Settings → Cookies and site permissions</p>
                            </div>
                        </div>
                    </section>

                    {{-- Impact of Blocking Cookies --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">5. Impact of Blocking Cookies</h2>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-200 mb-2">Please Note</h3>
                                    <p class="text-yellow-700 dark:text-yellow-300 leading-relaxed">
                                        If you disable cookies, some features of our website may not function properly.
                                        This includes the booking system, payment processing, and personalized content.
                                        Essential cookies are required for basic functionality and security.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Changes to Cookie Policy --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">6. Changes to This Cookie Policy</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            We may update this Cookie Policy from time to time to reflect changes in technology,
                            legislation, or our practices. We will post any changes on this page and update the
                            "Last updated" date. We encourage you to review this policy periodically.
                        </p>
                    </section>

                    {{-- Contact Information --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">7. Contact Us</h2>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            If you have any questions about our use of cookies or this Cookie Policy, please contact us:
                        </p>
                        <div class="bg-slate-100 dark:bg-slate-600 rounded-lg p-4">
                            <p class="text-slate-700 dark:text-slate-200"><strong>{{ config('app.name') }}</strong></p>
                            <p class="text-slate-600 dark:text-slate-300">Email: {{ config('mail.from.address') }}</p>
                            <p class="text-slate-600 dark:text-slate-300">Phone: {{ config('app.owner_phone_no') }}</p>
                        </div>
                    </section>

                    {{-- Additional Resources --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">8. Additional Resources</h2>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            For more information about cookies and privacy online, you may find these resources helpful:
                        </p>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2">
                            <li><a href="https://ico.org.uk/for-the-public/online/cookies/" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Information Commissioner's Office (ICO) - Cookies</a></li>
                            <li><a href="https://allaboutcookies.org/" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">All About Cookies</a></li>
                            <li><a href="https://youronlinechoices.eu/" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Your Online Choices</a></li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
