@props(['title' => null, 'seoData' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['seoData' => $seoData])
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container sticky class="border-b border-gray-200 bg-gradient-to-r from-slate-50 via-blue-50 to-slate-100 dark:from-slate-800 dark:via-slate-700 dark:to-slate-800 dark:border-slate-600 backdrop-blur-sm shadow-xl py-4 z-50">
            <flux:sidebar.toggle class="lg:hidden text-slate-700 hover:text-blue-600 p-2 rounded-lg hover:bg-slate-100 transition-colors" icon="bars-2" inset="left" />

            <a href="{{ route('home') }}" class="ms-2 me-8 flex items-center space-x-3 rtl:space-x-reverse lg:ms-0 group" wire:navigate>
                <div class="w-14 h-14 bg-slate-700 rounded-xl flex items-center justify-center group-hover:bg-slate-800 transition-colors">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L8 6v16h8V6l-4-4zm0 2.5L14 6.5V20h-4V6.5L12 4.5z"/>
                        <circle cx="12" cy="8" r="1.5" fill="#FBBF24"/>
                        <path d="M4 20h16v2H4z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="text-slate-700 dark:text-slate-200 flex flex-col">
                    <div class="font-bold text-xl leading-tight">Seaham Coastal</div>
                    <div class="text-slate-500 dark:text-slate-400 text-sm font-medium">Retreats</div>
                </div>
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate class="text-slate-700 hover:text-blue-600 dark:text-slate-200 dark:hover:text-blue-400 font-medium px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                    Home
                </flux:navbar.item>
                <flux:navbar.item :href="route('venue.show', ['route' => 'light-house'])" :current="request()->routeIs('venue.show') && request()->route('route') === 'light-house'"  class="text-slate-700 hover:text-blue-600 dark:text-slate-200 dark:hover:text-blue-400 font-medium px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                    The Light House
                </flux:navbar.item>
                <flux:navbar.item :href="route('venue.show', ['route' => 'saras'])" :current="request()->routeIs('venue.show') && request()->route('route') === 'saras'" class="text-slate-700 hover:text-blue-600 dark:text-slate-200 dark:hover:text-blue-400 font-medium px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                    Saras
                </flux:navbar.item>
                <flux:navbar.item href="{{ route('home') }}#about" :current="request()->routeIs('about-seaham')" class="text-slate-700 hover:text-blue-600 dark:text-slate-200 dark:hover:text-blue-400 font-medium px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                    About Seaham
                </flux:navbar.item>
                <flux:navbar.item href="{{ route('home') }}#contact" :current="request()->routeIs('contact')" class="text-slate-700 hover:text-blue-600 dark:text-slate-200 dark:hover:text-blue-400 font-medium px-4 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                    Contact
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 mt-12">
            <div class="bg-gradient-to-r from-slate-700 to-slate-800 p-4 border-b border-slate-600">
                <flux:sidebar.toggle class="text-white hover:text-slate-200 mb-3 p-2 rounded-lg hover:bg-white/10 transition-colors" icon="x-mark" />

                <a href="{{ route('home') }}" class="flex items-center space-x-3 rtl:space-x-reverse" wire:navigate>
                    <div class="w-12 h-12 bg-slate-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L8 6v16h8V6l-4-4zm0 2.5L14 6.5V20h-4V6.5L12 4.5z"/>
                            <circle cx="12" cy="8" r="1.5" fill="#FBBF24"/>
                            <path d="M4 20h16v2H4z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="text-white">
                        <div class="font-bold text-lg leading-tight">Seaham Coastal</div>
                        <div class="text-slate-200 text-xs">Retreats</div>
                    </div>
                </a>
            </div>

            <div class="p-4">
                <flux:navlist variant="outline" class="space-y-2">
                    <flux:navlist.group :heading="__('Navigation')" class="text-gray-600 dark:text-gray-400 font-semibold text-sm uppercase tracking-wide">
                        <flux:navlist.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium py-3 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                            Home
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('venue.show', ['route' => 'light-house'])" :current="request()->routeIs('venue.show') && request()->route('route') === 'light-house'" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium py-3 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                            The Light House
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('venue.show', ['route' => 'saras'])" :current="request()->routeIs('venue.show') && request()->route('route') === 'saras'" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium py-3 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                            Saras
                        </flux:navlist.item>
                        <flux:navlist.item href="{{ route('home') }}#about" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium py-3 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                            About Seaham
                        </flux:navlist.item>
                        <flux:navlist.item href="{{ route('home') }}#contact" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 font-medium py-3 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                            Contact
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            </div>

            <flux:spacer />

        </flux:sidebar>

        {{ $slot }}


        @fluxScripts

    </body>
</html>
