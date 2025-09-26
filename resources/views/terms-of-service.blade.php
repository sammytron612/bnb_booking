<x-layouts.app title="Terms of Service - {{ config('app.name') }}">
    <div class="bg-white dark:bg-zinc-800 min-h-screen">
        <div class="container mx-auto px-4 py-12">
            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-800 dark:text-slate-100 mb-4">Terms of Service</h1>
                <p class="text-lg text-slate-600 dark:text-slate-300">
                    Terms and conditions for booking and using our accommodation services
                </p>
                <div class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                    Last updated: {{ date('F j, Y') }}
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-8 space-y-8">
                    {{-- Introduction --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">1. Agreement</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            These Terms of Service ("Terms") constitute a legally binding agreement between you and
                            {{ config('app.name') }} ("we," "us," or "our") regarding your use of our website and
                            accommodation services. By making a booking or using our website, you agree to be bound by these Terms.
                        </p>
                    </section>

                    {{-- Booking Terms --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">2. Booking and Reservations</h2>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">2.1 Booking Process</h3>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2 mb-4">
                            <li>All bookings must be made through our official website</li>
                            <li>Bookings are subject to availability</li>
                            <li>A booking is confirmed only when payment is successfully processed</li>
                            <li>You will receive a booking confirmation email upon successful payment</li>
                            <li>All guests must be 18 years or older to make a booking</li>
                        </ul>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">2.2 Payment Terms</h3>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2 mb-4">
                            <li>Full payment is required at the time of booking</li>
                            <li>All payments are processed securely through Stripe</li>
                            <li>Prices are displayed in GBP and include VAT where applicable</li>
                            <li>We reserve the right to change prices without prior notice</li>
                        </ul>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">2.3 Booking Modifications</h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            Changes to existing bookings are subject to availability and may incur additional charges.
                            Please contact us directly to discuss any modifications to your reservation.
                        </p>
                    </section>

                    {{-- Cancellation Policy --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">3. Cancellation and Refund Policy</h2>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">3.1 Guest Cancellations</h3>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2 mb-4">
                            <li><strong>More than 14 days before arrival:</strong> Full refund minus 5% processing fee</li>
                            <li><strong>7-14 days before arrival:</strong> 50% refund</li>
                            <li><strong>Less than 7 days before arrival:</strong> No refund</li>
                            <li><strong>No-show:</strong> No refund</li>
                        </ul>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">3.2 Host Cancellations</h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                            In the unlikely event that we need to cancel your booking, you will receive a full refund
                            and we will assist in finding alternative accommodation where possible.
                        </p>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">3.3 Force Majeure</h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            Neither party shall be liable for cancellations due to circumstances beyond reasonable control,
                            including but not limited to natural disasters, government restrictions, or public health emergencies.
                        </p>
                    </section>

                    {{-- House Rules --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">4. House Rules and Guest Conduct</h2>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">4.1 General Rules</h3>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2 mb-4">
                            <li>Maximum occupancy limits must be respected</li>
                            <li>Quiet hours: 10:00 PM - 8:00 AM</li>
                            <li>No smoking inside the properties</li>
                            <li>No parties or events</li>
                            <li>Pets are permitted</li>
                            <li>Guests are responsible for any damage to the property</li>
                        </ul>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">4.2 Check-in and Check-out</h3>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2">
                            <li><strong>Check-in:</strong> 3:00 PM onwards</li>
                            <li><strong>Check-out:</strong> 11:00 AM</li>
                            <li>Self-check-in instructions will be provided before arrival</li>
                            <li>Late check-out may be available by arrangement</li>
                        </ul>
                    </section>

                    {{-- Liability --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">5. Liability and Insurance</h2>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">5.1 Guest Responsibility</h3>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2 mb-4">
                            <li>Guests are responsible for their personal belongings</li>
                            <li>We recommend comprehensive travel insurance</li>
                            <li>Guests must report any accidents or damage immediately</li>
                            <li>Guests are liable for any damage caused by negligence or misuse</li>
                        </ul>

                        <h3 class="text-lg font-medium text-slate-700 dark:text-slate-200 mb-2">5.2 Limitation of Liability</h3>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            Our liability is limited to the amount paid for your booking. We are not liable for
                            indirect, consequential, or punitive damages arising from your stay or use of our services.
                        </p>
                    </section>

                    {{-- Privacy and Data --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">6. Privacy and Data Protection</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            Your privacy is important to us. Our collection and use of personal information is governed
                            by our <a href="{{ route('privacy-policy') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">Privacy Policy</a>,
                            which forms part of these Terms.
                        </p>
                    </section>

                    {{-- Reviews and Content --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">7. Reviews and User Content</h2>
                        <ul class="list-disc pl-6 text-slate-600 dark:text-slate-300 space-y-2">
                            <li>Reviews must be honest, accurate, and based on your actual experience</li>
                            <li>Reviews must not contain offensive, defamatory, or inappropriate content</li>
                            <li>We reserve the right to remove reviews that violate our guidelines</li>
                            <li>By submitting a review, you grant us permission to use it for marketing purposes</li>
                        </ul>
                    </section>

                    {{-- Dispute Resolution --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">8. Dispute Resolution</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                            We encourage direct communication to resolve any issues. If a dispute cannot be resolved
                            informally, it shall be governed by the laws of England and Wales, and any legal proceedings
                            shall be subject to the exclusive jurisdiction of the English courts.
                        </p>
                    </section>

                    {{-- Changes to Terms --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">9. Changes to Terms</h2>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            We may update these Terms from time to time. Changes will be posted on our website with
                            an updated effective date. Continued use of our services after changes constitutes acceptance
                            of the new Terms.
                        </p>
                    </section>

                    {{-- Contact Information --}}
                    <section>
                        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-4">10. Contact Information</h2>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                            For questions about these Terms or to discuss any issues, please contact us:
                        </p>
                        <div class="bg-slate-100 dark:bg-slate-600 rounded-lg p-4">
                            <p class="text-slate-700 dark:text-slate-200"><strong>{{ config('app.name') }}</strong></p>
                            <p class="text-slate-600 dark:text-slate-300">Email: {{ config('mail.from.address') }}</p>
                            <p class="text-slate-600 dark:text-slate-300">Phone: {{ env('OWNER_PHONE_NO') }}</p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
