<x-layouts.app.header>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-slate-600 to-slate-800 px-6 py-8">
                <h1 class="text-3xl font-bold text-white">Terms of Service</h1>
                <p class="text-slate-100 mt-2">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <div class="px-6 py-8 prose prose-lg max-w-none">
                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Agreement to Terms</h2>
                    <p class="text-gray-700 leading-relaxed">
                        These Terms of Service ("Terms") govern your use of Seaham Coastal Retreats' website and booking services.
                        By making a booking or using our services, you agree to be bound by these Terms. If you do not agree to these Terms,
                        please do not use our services.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibent text-gray-900 mb-4">2. Our Services</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Seaham Coastal Retreats provides holiday rental accommodation services in Seaham, County Durham. Our services include:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Online booking platform for our rental properties</li>
                        <li>Property management and guest services</li>
                        <li>Customer support and assistance</li>
                        <li>Information about local attractions and amenities</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Booking Terms</h2>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">Booking Process</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                        <li>All bookings are subject to availability</li>
                        <li>Booking confirmation will be sent via email</li>
                        <li>Full payment is required at the time of booking</li>
                        <li>You must be 18 years or older to make a booking</li>
                    </ul>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">Pricing and Payment</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                        <li>All prices are in British Pounds (GBP) and include VAT where applicable</li>
                        <li>Prices may vary based on season, length of stay, and demand</li>
                        <li>Payment is processed securely through Stripe, which is PCI DSS Level 1 compliant</li>
                        <li>We only collect and store your name, email address, and phone number</li>
                        <li>No credit card information is stored on our servers - all payment data is securely handled by Stripe</li>
                        <li>Additional charges may apply for extra services or damages</li>
                    </ul>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">Check-in and Check-out</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Standard check-in time: 3:00 PM</li>
                        <li>Standard check-out time: 11:00 AM</li>
                        <li>Early check-in or late check-out may be available upon request</li>
                        <li>Check-in instructions will be provided before arrival</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Cancellation Policy</h2>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Flexible Cancellation:</strong> Cancel up to 24 hours before check-in for a full refund
                                </p>
                            </div>
                        </div>
                    </div>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Cancellations made less than 24 hours before check-in: 50% refund</li>
                        <li>No-shows or same-day cancellations: No refund</li>
                        <li>Refunds will be processed within 5-10 business days</li>
                        <li>Exceptional circumstances will be considered on a case-by-case basis</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Guest Responsibilities</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">As our guest, you agree to:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Provide accurate information when booking</li>
                        <li>Treat the property with respect and care</li>
                        <li>Follow all house rules and local regulations</li>
                        <li>Not exceed the maximum occupancy limit</li>
                        <li>Not use the property for illegal activities</li>
                        <li>Be considerate of neighbors and the local community</li>
                        <li>Report any damage or issues immediately</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. House Rules</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-green-800 mb-3">✓ Allowed</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-1 text-sm">
                                <li>Smoking in designated outdoor areas only</li>
                                <li>Well-behaved pets are welcome</li>
                                <li>Reasonable noise levels</li>
                                <li>Use of all provided amenities</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-red-800 mb-3">✗ Not Allowed</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-1 text-sm">
                                <li>Smoking inside the property</li>
                                <li>Parties or large gatherings</li>
                                <li>Excessive noise after 10 PM</li>
                                <li>Subletting or unauthorized guests</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Liability and Insurance</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Guests are responsible for any damage caused during their stay</li>
                        <li>We recommend travel insurance to cover personal belongings</li>
                        <li>Our liability is limited to the amount paid for your booking</li>
                        <li>We are not responsible for personal injury or loss of belongings</li>
                        <li>Emergency contact information will be provided upon check-in</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Force Majeure</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We are not liable for any failure to perform our obligations due to circumstances beyond our reasonable control,
                        including but not limited to natural disasters, government restrictions, or public health emergencies.
                        In such cases, we will work with guests to find alternative solutions or provide appropriate refunds.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Privacy and Data Protection</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Your privacy is important to us. We are committed to protecting your personal information and handling it responsibly.
                    </p>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">Data We Collect</h3>
                    <p class="text-gray-700 leading-relaxed mb-3">
                        We only collect the essential information needed to process your booking:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                        <li>Your name (for booking identification and communication)</li>
                        <li>Email address (for booking confirmations and communication)</li>
                        <li>Phone number (for check-in coordination and emergency contact)</li>
                    </ul>

                    <h3 class="text-xl font-medium text-gray-800 mb-3">Payment Security</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 mb-4">
                        <li>No credit card information is stored on our servers</li>
                        <li>All payment processing is handled by Stripe, which is PCI DSS Level 1 compliant</li>
                        <li>Stripe maintains the highest level of payment security certification</li>
                        <li>Your financial information is encrypted and protected at all times</li>
                    </ul>

                    <p class="text-gray-700 leading-relaxed">
                        For complete details about how we collect, use, and protect your personal information, please review our
                        <a href="{{ route('privacy-policy') }}" class="text-blue-600 hover:text-blue-800 underline">Privacy Policy</a>.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semiboned text-gray-900 mb-4">10. Dispute Resolution</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        We strive to resolve any issues promptly and fairly. If you have a complaint:
                    </p>
                    <ol class="list-decimal list-inside text-gray-700 space-y-2">
                        <li>Contact us immediately to discuss the issue</li>
                        <li>We will investigate and respond within 48 hours</li>
                        <li>If unresolved, disputes will be subject to UK law and jurisdiction</li>
                    </ol>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Modifications to Terms</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We reserve the right to modify these Terms at any time. Changes will be posted on this page with an updated
                        "Last updated" date. Continued use of our services after changes constitutes acceptance of the new Terms.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Contact Information</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        For questions about these Terms of Service, please contact us:
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="font-medium text-gray-800">Seaham Coastal Retreats</p>
                        <p class="text-gray-700">Email: <a href="mailto:{{ config('app.owner_email') }}" class="text-blue-600 hover:text-blue-800">{{ config('app.owner_email') }}</a></p>
                        <p class="text-gray-700">Phone: <a href="tel:{{ config('app.owner_phone_no') }}" class="text-blue-600 hover:text-blue-800">{{ config('app.owner_phone_no') }}</a></p>
                        <p class="text-gray-700">Address: Seaham, County Durham, United Kingdom</p>
                    </div>
                </section>

                <div class="border-t pt-6 mt-8">
                    <p class="text-sm text-gray-600">
                        By using our services, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app.header>
