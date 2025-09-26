{{-- Footer Component for Seaham Coastal Retreats --}}
<footer class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 text-white border-t border-slate-600 mt-auto">
    <div class="container mx-auto px-4 py-12">
        {{-- Mobile-First Social Media Section --}}
        <div class="sm:hidden mb-8 text-center">
            <h4 class="text-lg font-semibold text-slate-100 mb-4">Connect With Us</h4>
            <div class="flex justify-center space-x-4">
                {{-- Facebook --}}
                @if(config('app.facebook_url'))
                <a href="{{ config('app.facebook_url') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="w-12 h-12 bg-slate-600 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-all duration-200 transform hover:scale-105 group">
                    <svg class="w-6 h-6 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                @endif

                {{-- Instagram --}}
                @if(config('app.instagram_url'))
                <a href="{{ config('app.instagram_url') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="w-12 h-12 bg-slate-600 rounded-lg flex items-center justify-center hover:bg-gradient-to-r hover:from-purple-500 hover:to-pink-500 transition-all duration-200 transform hover:scale-105 group">
                    <svg class="w-6 h-6 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                @endif

                {{-- TripAdvisor --}}
                @if(config('app.tripadvisor_url'))
                <a href="{{ config('app.tripadvisor_url') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="w-12 h-12 bg-slate-600 rounded-lg flex items-center justify-center hover:bg-green-600 transition-all duration-200 transform hover:scale-105 group">
                    <svg class="w-6 h-6 group-hover:text-white" fill="currentColor" viewBox="0 -96 512.2 512.2">
                        <path d="M128.2 127.9C92.7 127.9 64 156.6 64 192c0 35.4 28.7 64.1 64.1 64.1 35.4 0 64.1-28.7 64.1-64.1.1-35.4-28.6-64.1-64-64.1zm0 110c-25.3 0-45.9-20.5-45.9-45.9s20.5-45.9 45.9-45.9S174 166.7 174 192s-20.5 45.9-45.8 45.9z"/>
                        <circle cx="128.4" cy="191.9" r="31.9"/>
                        <path d="M384.2 127.9c-35.4 0-64.1 28.7-64.1 64.1 0 35.4 28.7 64.1 64.1 64.1 35.4 0 64.1-28.7 64.1-64.1 0-35.4-28.7-64.1-64.1-64.1zm0 110c-25.3 0-45.9-20.5-45.9-45.9s20.5-45.9 45.9-45.9S430 166.7 430 192s-20.5 45.9-45.8 45.9z"/>
                        <circle cx="384.4" cy="191.9" r="31.9"/>
                        <path d="M474.4 101.2l37.7-37.4h-76.4C392.9 29 321.8 0 255.9 0c-66 0-136.5 29-179.3 63.8H0l37.7 37.4C14.4 124.4 0 156.5 0 192c0 70.8 57.4 128.2 128.2 128.2 32.5 0 62.2-12.1 84.8-32.1l43.4 31.9 42.9-31.2-.5-1.2c22.7 20.2 52.5 32.5 85.3 32.5 70.8 0 128.2-57.4 128.2-128.2-.1-35.4-14.6-67.5-37.9-90.7zM368 64.8c-60.7 7.6-108.3 57.6-111.9 119.5-3.7-62-51.4-112.1-112.3-119.5 30.6-22 69.6-32.8 112.1-32.8S337.4 42.8 368 64.8zM128.2 288.2C75 288.2 32 245.1 32 192s43.1-96.2 96.2-96.2 96.2 43.1 96.2 96.2c-.1 53.1-43.1 96.2-96.2 96.2zm256 0c-53.1 0-96.2-43.1-96.2-96.2s43.1-96.2 96.2-96.2 96.2 43.1 96.2 96.2c-.1 53.1-43.1 96.2-96.2 96.2z"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Company Info --}}
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-slate-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L8 6v16h8V6l-4-4zm0 2.5L14 6.5V20h-4V6.5L12 4.5z"/>
                            <circle cx="12" cy="8" r="1.5" fill="#FBBF24"/>
                            <path d="M4 20h16v2H4z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">{{ config('app.name') }}</h3>
                        <p class="text-slate-300 text-sm">Premium Coastal Accommodations</p>
                    </div>
                </div>
                <p class="text-slate-300 leading-relaxed">
                    Experience the beauty of Seaham's coastline with our luxury retreats.
                    Perfect for couples, families, and those seeking a peaceful getaway.
                </p>
            </div>

            {{-- Contact Information --}}
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-slate-100 border-b border-slate-600 pb-2">
                    Contact Information
                </h4>
                <div class="space-y-3">
                    {{-- Email --}}
                    @if(config('mail.from.address'))
                    <div class="flex items-center space-x-3 group">
                        <div class="w-8 h-8 bg-slate-600 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <a href="mailto:{{ config('mail.from.address') }}"
                           class="text-slate-300 hover:text-blue-400 transition-colors">
                            {{ config('mail.from.address') }}
                        </a>
                    </div>
                    @endif

                    {{-- Phone --}}
                    @if(config('app.owner_phone_no'))
                    <div class="flex items-center space-x-3 group">
                        <div class="w-8 h-8 bg-slate-600 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <a href="tel:{{ str_replace(' ', '', config('app.owner_phone_no')) }}"
                           class="text-slate-300 hover:text-blue-400 transition-colors">
                            {{ config('app.owner_phone_no') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Social Media & Links (Hidden on Mobile) --}}
            <div class="space-y-4 hidden sm:block">
                <h4 class="text-lg font-semibold text-slate-100 border-b border-slate-600 pb-2">
                    Connect With Us
                </h4>
                <div class="space-y-4">
                    {{-- Social Media Links --}}
                    <div class="flex space-x-3">
                        {{-- Facebook --}}
                        @if(config('app.facebook_url'))
                        <a href="{{ config('app.facebook_url') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-slate-600 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-all duration-200 transform hover:scale-105 group">
                            <svg class="w-5 h-5 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        @endif

                        {{-- Instagram --}}
                        @if(config('app.instagram_url'))
                        <a href="{{ config('app.instagram_url') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-slate-600 rounded-lg flex items-center justify-center hover:bg-gradient-to-r hover:from-purple-500 hover:to-pink-500 transition-all duration-200 transform hover:scale-105 group">
                            <svg class="w-5 h-5 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        @endif

                        {{-- TripAdvisor --}}
                        @if(config('app.tripadvisor_url'))
                        <a href="{{ config('app.tripadvisor_url') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-slate-600 rounded-lg flex items-center justify-center hover:bg-green-600 transition-all duration-200 transform hover:scale-105 group">
                            <svg class="w-5 h-5 group-hover:text-white" fill="currentColor" viewBox="0 -96 512.2 512.2">
                                <path d="M128.2 127.9C92.7 127.9 64 156.6 64 192c0 35.4 28.7 64.1 64.1 64.1 35.4 0 64.1-28.7 64.1-64.1.1-35.4-28.6-64.1-64-64.1zm0 110c-25.3 0-45.9-20.5-45.9-45.9s20.5-45.9 45.9-45.9S174 166.7 174 192s-20.5 45.9-45.8 45.9z"/>
                                <circle cx="128.4" cy="191.9" r="31.9"/>
                                <path d="M384.2 127.9c-35.4 0-64.1 28.7-64.1 64.1 0 35.4 28.7 64.1 64.1 64.1 35.4 0 64.1-28.7 64.1-64.1 0-35.4-28.7-64.1-64.1-64.1zm0 110c-25.3 0-45.9-20.5-45.9-45.9s20.5-45.9 45.9-45.9S430 166.7 430 192s-20.5 45.9-45.8 45.9z"/>
                                <circle cx="384.4" cy="191.9" r="31.9"/>
                                <path d="M474.4 101.2l37.7-37.4h-76.4C392.9 29 321.8 0 255.9 0c-66 0-136.5 29-179.3 63.8H0l37.7 37.4C14.4 124.4 0 156.5 0 192c0 70.8 57.4 128.2 128.2 128.2 32.5 0 62.2-12.1 84.8-32.1l43.4 31.9 42.9-31.2-.5-1.2c22.7 20.2 52.5 32.5 85.3 32.5 70.8 0 128.2-57.4 128.2-128.2-.1-35.4-14.6-67.5-37.9-90.7zM368 64.8c-60.7 7.6-108.3 57.6-111.9 119.5-3.7-62-51.4-112.1-112.3-119.5 30.6-22 69.6-32.8 112.1-32.8S337.4 42.8 368 64.8zM128.2 288.2C75 288.2 32 245.1 32 192s43.1-96.2 96.2-96.2 96.2 43.1 96.2 96.2c-.1 53.1-43.1 96.2-96.2 96.2zm256 0c-53.1 0-96.2-43.1-96.2-96.2s43.1-96.2 96.2-96.2 96.2 43.1 96.2 96.2c-.1 53.1-43.1 96.2-96.2 96.2z"/>
                            </svg>
                        </a>
                        @endif
                    </div>

                    {{-- Quick Links --}}
                    <div class="space-y-2">
                        <h5 class="text-sm font-medium text-slate-200 uppercase tracking-wide">Quick Links</h5>
                        <div class="space-y-1">
                            <a href="{{ route('home') }}"
                               class="block text-slate-300 hover:text-blue-400 transition-colors text-sm"
                               wire:navigate>
                                Home
                            </a>
                            <a href="{{ route('venue.show', ['route' => 'light-house']) }}"
                               class="block text-slate-300 hover:text-blue-400 transition-colors text-sm">
                                The Light House
                            </a>
                            <a href="{{ route('venue.show', ['route' => 'saras']) }}"
                               class="block text-slate-300 hover:text-blue-400 transition-colors text-sm">
                                Saras
                            </a>
                            <a href="{{ route('home') }}#contact"
                               class="block text-slate-300 hover:text-blue-400 transition-colors text-sm">
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-slate-600 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-slate-400 text-sm">
                    © {{ date('Y') }} KLW DESIGN. All rights reserved.
                </div>
                <div class="flex items-center space-x-4 text-slate-400 text-sm">
                    <a href="{{ route('privacy-policy') }}" class="hover:text-blue-400 transition-colors" wire:navigate>Privacy Policy</a>
                    <span>•</span>
                    <a href="{{ route('terms-of-service') }}" class="hover:text-blue-400 transition-colors" wire:navigate>Terms of Service</a>
                    <span>•</span>
                    <a href="{{ route('cookie-policy') }}" class="hover:text-blue-400 transition-colors" wire:navigate>Cookie Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>
