@props([
  // Allow passing custom images; fallback to storage ones
  'images' => [
    ['/storage/coast.webp', 'Coast'],
    ['/storage/seaglass.jpg', 'Seaglass'],
    ['/storage/tommy.jpg', 'Tommy']
  ],
  'interval' => 3000,
  // display mode: cover (crop to fill) or contain (letterbox)
  'mode' => 'cover',
  // height utility (Tailwind) e.g. h-[70vh] or h-screen
  'height' => 'h-[75vh] md:h-[85vh]'
])

@php $fitClass = $mode === 'contain' ? 'object-contain' : 'object-cover'; @endphp

<div class="relative w-full {{ $height }} overflow-hidden bg-black group" data-slider
  data-interval="{{ (int)$interval }}"
  data-fit="{{ $fitClass }}"
  style="height:75vh;max-height:100vh;">
  <div class="absolute inset-0" data-slider-track>
    @foreach($images as $i => $img)
      <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-black opacity-0 transition-opacity duration-700 ease-in-out" data-slide="{{ $i }}">
        <img src="{{ $img[0] }}" alt="{{ $img[1] ?? 'Slide '.($i+1) }}" class="w-full h-full {{ $fitClass }} select-none" draggable="false" />
        @if(!empty($img[1]))
          <div class="absolute bottom-6 left-6 text-white text-xl font-medium drop-shadow">{{ $img[1] }}</div>
        @endif
      </div>
    @endforeach
  </div>

  <!-- Prev / Next -->
  <button type="button" data-prev aria-label="Previous slide"
          class="opacity-0 group-hover:opacity-100 transition-opacity absolute left-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white rounded-full w-10 h-10 flex items-center justify-center focus:outline-none z-10">‹</button>
  <button type="button" data-next aria-label="Next slide"
          class="opacity-0 group-hover:opacity-100 transition-opacity absolute right-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white rounded-full w-10 h-10 flex items-center justify-center focus:outline-none z-10">›</button>

  <!-- Indicators -->
  <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10" data-indicators></div>
  <!-- Fallback placeholder (hidden after init) -->
  <div data-fallback class="absolute inset-0 flex items-center justify-center text-white text-lg bg-black/40">Loading slider…</div>
  <!-- Debug overlay (remove when satisfied) -->
  <div class="absolute top-2 right-2 text-xs bg-black/60 text-white px-2 py-1 rounded" data-debug>loading…</div>
</div>
