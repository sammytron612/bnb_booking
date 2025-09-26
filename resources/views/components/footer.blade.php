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
                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.328-1.297L6.46 14.453c.587.587 1.297.88 2.119.88 1.659 0 3.006-1.346 3.006-3.005S10.238 9.32 8.58 9.32c-.822 0-1.532.293-2.119.88L5.122 8.96c.88-.807 2.031-1.297 3.328-1.297 2.722 0 4.944 2.221 4.944 4.943s-2.222 4.944-4.945 4.944zm7.389-3.296h-.807v.807h-.807v-.807h-.807v-.807h.807v-.807h.807v.807h.807v.807zm2.764-2.662c0 .314-.132.594-.366.798-.234.204-.527.306-.854.306s-.62-.102-.854-.306c-.234-.204-.366-.484-.366-.798s.132-.594.366-.798c.234-.204.527-.306.854-.306s.62.102.854.306c.234.204.366.484.366.798z"/>
                    </svg>
                </a>
                @endif

                {{-- TripAdvisor --}}
                @if(config('app.tripadvisor_url'))
                <a href="{{ config('app.tripadvisor_url') }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="w-12 h-12 bg-slate-600 rounded-lg flex items-center justify-center hover:bg-green-600 transition-all duration-200 transform hover:scale-105 group">
                    <svg class="w-6 h-6 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-1-13c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 4c-.552 0-1-.448-1-1s.448-1 1-1 1 .448 1 1-.448 1-1 1zm2-4c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 4c-.552 0-1-.448-1-1s.448-1 1-1 1 .448 1 1-.448 1-1 1z"/>
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
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.328-1.297L6.46 14.453c.587.587 1.297.88 2.119.88 1.659 0 3.006-1.346 3.006-3.005S10.238 9.32 8.58 9.32c-.822 0-1.532.293-2.119.88L5.122 8.96c.88-.807 2.031-1.297 3.328-1.297 2.722 0 4.944 2.221 4.944 4.943s-2.222 4.944-4.945 4.944zm7.389-3.296h-.807v.807h-.807v-.807h-.807v-.807h.807v-.807h.807v.807h.807v.807zm2.764-2.662c0 .314-.132.594-.366.798-.234.204-.527.306-.854.306s-.62-.102-.854-.306c-.234-.204-.366-.484-.366-.798s.132-.594.366-.798c.234-.204.527-.306.854-.306s.62.102.854.306c.234.204.366.484.366.798z"/>
                            </svg>
                        </a>
                        @endif

                        {{-- TripAdvisor --}}
                        @if(config('app.tripadvisor_url'))
                        <a href="{{ config('app.tripadvisor_url') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-10 h-10 bg-slate-600 rounded-lg flex items-center justify-center hover:bg-green-600 transition-all duration-200 transform hover:scale-105 group">
                            <svg class="w-5 h-5 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-1-13c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 4c-.552 0-1-.448-1-1s.448-1 1-1 1 .448 1 1-.448 1-1 1zm2-4c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 4c-.552 0-1-.448-1-1s.448-1 1-1 1 .448 1 1-.448 1-1 1z"/>
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
