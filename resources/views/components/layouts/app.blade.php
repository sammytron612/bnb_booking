<x-layouts.app.header :title="$title ?? null" :seoData="$seoData ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    
    <x-footer />
</x-layouts.app.header>
