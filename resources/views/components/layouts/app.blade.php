<x-layouts.app.header :title="$title ?? null" :seoData="$seoData ?? null">
    <flux:main id="main-content">
        {{ $slot }}
    </flux:main>
</x-layouts.app.header>
