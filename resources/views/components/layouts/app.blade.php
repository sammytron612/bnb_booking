<x-layouts.app.header :title="$title ?? null" :seoData="$seoData ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    <x-slot:footer>
        <x-footer />
    </x-slot:footer>
</x-layouts.app.header>
