<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container sticky class="border-b border-blue-200 bg-gradient-to-r from-blue-200 via-blue-300 to-blue-500 dark:border-blue-700 shadow-lg py-8 z-50">
            <flux:sidebar.toggle class="lg:hidden text-black" icon="bars-2" inset="left" />

            <a href="{{ route('home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <div class="text-black flex items-center">
                    <div class="font-bold text-2xl">Seaham Coastal Retreats</div>
                </div>
            </a>

            <flux:navbar class="-mb-px max-lg:hidden !text-black">
                <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate class="!text-black hover:!text-blue-800">
                    Home
                </flux:navbar.item>
                <flux:navbar.item :href="route('light-house')" :current="request()->routeIs('light-house')" wire:navigate class="!text-black hover:!text-blue-800">
                    The Light House
                </flux:navbar.item>
                <flux:navbar.item href="#"  :current="request()->routeIs('saras')" class="!text-black hover:!text-blue-800">
                    Saras
                </flux:navbar.item>
                <flux:navbar.item href="#" :current="request()->routeIs('about-seaham')" class="!text-black hover:!text-blue-800">
                    About Seaham
                </flux:navbar.item>
                <flux:navbar.item href="#" :current="request()->routeIs('contact')" class="!text-black hover:!text-blue-800">
                    Contact
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-blue-200 bg-gradient-to-b from-blue-200 via-blue-300 to-blue-500 dark:border-blue-700">
            <flux:sidebar.toggle class="lg:hidden text-black" icon="x-mark" />

            <a href="{{ route('home') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <div class="text-black">
                    <div class="font-bold text-sm">Seaham Coastal Retreats</div>
                </div>
            </a>

            <flux:navlist variant="outline" class="text-black">
                <flux:navlist.group :heading="__('Properties')">
                    <flux:navlist.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate class="text-black hover:text-blue-800">
                        Home
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('light-house')" :current="request()->routeIs('light-house')" wire:navigate class="text-black hover:text-blue-800">
                        The Light House
                    </flux:navlist.item>
                    <flux:navlist.item href="#" class="text-black hover:text-blue-800">
                        Saras
                    </flux:navlist.item>
                    <flux:navlist.item href="#" class="text-black hover:text-blue-800">
                        About Seaham
                    </flux:navlist.item>
                    <flux:navlist.item href="#" class="text-black hover:text-blue-800">
                        Contact
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
