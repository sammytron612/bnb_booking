<x-layouts.app title="Privacy Policy - {{ config('app.name') }}">
    <div class="bg-white dark:bg-zinc-800 min-h-screen">
        <div class="container mx-auto px-4 py-12">
            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-800 dark:text-slate-100 mb-4">Privacy Policy</h1>
                <p class="text-lg text-slate-600 dark:text-slate-300">
                    How we collect, use, and protect your personal information
                </p>
                <div class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                    Last updated: {{ date('F j, Y') }}
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-8 space-y-8">
                    {{-- Introduction --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">1. Introduction</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            {{ config('app.name') }} ("we," "our," or "us") is committed to protecting your privacy.
                            This Privacy Policy explains how we collect, use, disclose, and safeguard your information
                            when you visit our website or use our booking services. By using our services, you consent
                            to the collection and use of information in accordance with this policy.
                        </p>
                    </section>

                    {{-- Information We Collect --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">2. Information We Collect</h2>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">2.1 Personal Information</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            We may collect personal information that you provide directly to us, including:
                        </p>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2 mb-4">
                            <li>Name and contact information (email, phone number, address)</li>
                            <li>Booking and reservation details</li>
                            <li>Payment confirmation details (we do not store credit card information - all payment data is processed and stored securely by Stripe)</li>
                            <li>Communication preferences</li>
                            <li>Review and feedback content</li>
                        </ul>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">2.2 Automatically Collected Information</h3>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2">
                            <li>Browser type and version</li>
                            <li>Device information and IP address</li>
                            <li>Pages visited and time spent on our website</li>
                            <li>Referring website information</li>
                            <li>Cookies and similar tracking technologies</li>
                        </ul>
                    </section>

                    {{-- How We Use Information --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">3. How We Use Your Information</h2>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            We use the information we collect for the following purposes:
                        </p>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2">
                            <li>Processing bookings and reservations</li>
                            <li>Payment processing and fraud prevention</li>
                            <li>Sending booking confirmations and updates</li>
                            <li>Customer support and communication</li>
                            <li>Improving our website and services</li>
                            <li>Marketing communications (with your consent)</li>
                            <li>Legal compliance and safety</li>
                        </ul>
                    </section>

                    {{-- Information Sharing --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">4. Information Sharing and Disclosure</h2>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            We do not sell, trade, or rent your personal information. We may share information in the following circumstances:
                        </p>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2">
                            <li><strong>Service Providers:</strong> Trusted third parties who help us operate our business (e.g., Stripe for payments)</li>
                            <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
                            <li><strong>Business Transfers:</strong> In connection with mergers or acquisitions</li>
                            <li><strong>Emergency Situations:</strong> To protect the safety of our users or others</li>
                        </ul>
                    </section>

                    {{-- Data Security --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">5. Data Security</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            We implement appropriate technical and organizational security measures to protect your personal
                            information against unauthorized access, alteration, disclosure, or destruction. This includes:
                        </p>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2 mt-4">
                            <li>SSL encryption for data transmission</li>
                            <li>Secure payment processing through Stripe</li>
                            <li>Regular security assessments and updates</li>
                            <li>Access controls and employee training</li>
                            <li>Content Security Policy (CSP) implementation</li>
                        </ul>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mt-6">
                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">Payment Data Security</h3>
                                    <p class="text-blue-700 dark:text-blue-300 leading-relaxed">
                                        <strong>We do not store credit card information.</strong> All payment processing is handled
                                        directly by Stripe, a PCI DSS Level 1 compliant payment processor. Your credit card details
                                        are encrypted and stored securely by Stripe, never on our servers. We only receive confirmation
                                        of successful payments and booking reference numbers.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Your Rights --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">6. Your Rights</h2>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            You have the following rights regarding your personal information:
                        </p>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2">
                            <li><strong>Access:</strong> Request copies of your personal data</li>
                            <li><strong>Rectification:</strong> Request correction of inaccurate data</li>
                            <li><strong>Erasure:</strong> Request deletion of your data (subject to legal requirements)</li>
                            <li><strong>Portability:</strong> Request transfer of your data in a structured format</li>
                            <li><strong>Objection:</strong> Object to processing for marketing purposes</li>
                            <li><strong>Restriction:</strong> Request limitation of processing in certain circumstances</li>
                        </ul>
                    </section>

                    {{-- Cookies --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">7. Cookies and Tracking</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            We use cookies and similar technologies to enhance your experience on our website.
                            For detailed information about our cookie usage, please see our
                            <a href="{{ route('cookie-policy') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Cookie Policy</a>.
                        </p>
                    </section>

                    {{-- Contact Information --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">8. Contact Us</h2>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            If you have any questions about this Privacy Policy or wish to exercise your rights, please contact us:
                        </p>
                        <div class="bg-slate-100 dark:bg-slate-600 rounded-lg p-4">
                            <p class="text-slate-700 dark:text-slate-200"><strong>{{ config('app.name') }}</strong></p>
                            <p class="text-slate-600 dark:text-slate-300">Email: {{ config('mail.from.address') }}</p>
                            <p class="text-slate-600 dark:text-slate-300">Phone: {{ config('app.owner_phone_no') }}</p>
                        </div>
                    </section>

                    {{-- Changes to Policy --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">9. Changes to This Policy</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            We may update this Privacy Policy from time to time. We will notify you of any changes
                            by posting the new Privacy Policy on this page and updating the "Last updated" date.
                            We encourage you to review this Privacy Policy periodically for any changes.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
