@props([
  // Allow passing custom images; fallback to storage ones
  'images' => [
    ['/storage/tommy.webp', 'Tommy'],
    ['/storage/coast.webp', 'Coast'],
    ['/storage/seaglass.jpg', 'Seaglass']
  ],
  'interval' => 2500,
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
        <img src="{{ $img[0] }}" alt="{{ $img[1] ?? 'Slide '.($i+1) }}" class="w-full h-full {{ $fitClass }} select-none" draggable="false"
             @if($i === 0) loading="eager" @else loading="lazy" @endif
             decoding="async" />
        @if(!empty($img[1]))
          <div class="absolute bottom-6 left-6 text-white text-xl font-medium drop-shadow">{{ $img[1] }}</div>
        @endif
      </div>
    @endforeach
  </div>
</div>
