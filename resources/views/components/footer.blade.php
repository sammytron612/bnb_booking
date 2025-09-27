<!-- Footer -->
<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-xl font-bold mb-4">Seaham Coastal Retreats</h3>
                <p class="text-gray-300 mb-4">
                    Experience the beauty of Seaham's coastline with our carefully selected holiday accommodations.
                    From cozy cottages to stunning sea views, we offer the perfect escape for your coastal getaway.
                </p>
                <div class="text-gray-400 text-sm">
                    <p>Seaham, County Durham</p>
                    <p>Northeast England</p>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Properties</h4>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="{{ route('venue.show', ['route' => 'light-house']) }}" class="hover:text-white transition-colors" wire:navigate>The Light House</a></li>
                    <li><a href="{{ route('venue.show', ['route' => 'saras']) }}" class="hover:text-white transition-colors" wire:navigate>Sara's</a></li>
                    <li><a href="{{ route('home') }}#about" class="hover:text-white transition-colors">About Seaham</a></li>
                    <li><a href="{{ route('home') }}#contact" class="hover:text-white transition-colors">Contact Us</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Get in Touch</h4>
                <div class="space-y-2 text-gray-300">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <a href="mailto:{{ config('app.owner_email') }}" class="hover:text-white transition-colors">{{ config('app.owner_email') }}</a>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z" clip-rule="evenodd"/>
                        </svg>
                        <a href="tel:{{ config('app.owner_phone_no') }}" class="hover:text-white transition-colors">{{ config('app.owner_phone_no') }}</a>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Seaham, County Durham</span>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="mt-6">
                    <h5 class="text-sm font-semibold mb-3 text-gray-300">Follow Us</h5>
                    <div class="flex space-x-4">
                        <!-- Facebook -->
                        <a href="{{ config('app.facebook_url') }}" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-blue-500 transition-colors duration-300 transform hover:scale-110" 
                           aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        
                        <!-- Instagram -->
                        <a href="{{ config('app.instagram_url') }}" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-pink-500 transition-colors duration-300 transform hover:scale-110" 
                           aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        
                        <!-- TripAdvisor -->
                        <a href="{{ config('app.tripadvisor_url') }}" target="_blank" rel="noopener noreferrer" 
                           class="text-gray-400 hover:text-green-500 transition-colors duration-300 transform hover:scale-110" 
                           aria-label="TripAdvisor">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.006 4.295c-2.67 0-5.338.784-7.645 2.353H0l1.329 1.348a7.682 7.682 0 0 0-.821 3.508c0 4.26 3.445 7.705 7.705 7.705 2.129 0 4.053-.865 5.435-2.258l.358.358.358-.358c1.382 1.393 3.306 2.258 5.435 2.258 4.26 0 7.705-3.445 7.705-7.705a7.682 7.682 0 0 0-.821-3.508L24 6.648h-4.361c-2.307-1.569-4.975-2.353-7.633-2.353zm0 1.2c2.37 0 4.6.65 6.532 1.831H5.474c1.932-1.181 4.162-1.831 6.532-1.831zM8.213 7.125c3.098 0 5.61 2.512 5.61 5.61s-2.512 5.61-5.61 5.61-5.61-2.512-5.61-5.61 2.512-5.61 5.61-5.61zm7.574 0c3.098 0 5.61 2.512 5.61 5.61s-2.512 5.61-5.61 5.61-5.61-2.512-5.61-5.61 2.512-5.61 5.61-5.61zM8.213 9.167c-.539 0-1.076.206-1.487.617-.823.823-.823 2.151 0 2.974.823.823 2.151.823 2.974 0 .823-.823.823-2.151 0-2.974-.411-.411-.948-.617-1.487-.617zm7.574 0c-.539 0-1.076.206-1.487.617-.823.823-.823 2.151 0 2.974.823.823 2.151.823 2.974 0 .823-.823.823-2.151 0-2.974-.411-.411-.948-.617-1.487-.617z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row items-center justify-between">
            <div class="text-gray-400 text-sm mb-4 md:mb-0">
                <p>&copy; {{ date('Y') }} Seaham Coastal Retreats. All rights reserved.</p>
            </div>
            <div class="flex items-center space-x-6 text-gray-400 text-sm">
                <a href="{{ route('privacy-policy') }}" class="hover:text-white transition-colors" wire:navigate>Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}" class="hover:text-white transition-colors" wire:navigate>Terms of Service</a>
                <a href="{{ route('cookie-policy') }}" class="hover:text-white transition-colors" wire:navigate>Cookie Policy</a>
                <a href="{{ route('home') }}#contact" class="hover:text-white transition-colors">Contact</a>
            </div>
        </div>
    </div>
</footer>
