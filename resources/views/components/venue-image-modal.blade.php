<!-- Modal for Image Gallery (reuse existing modal from image-placeholder component) -->
    @php
        $modalImages = [
            ['src' => '/storage/lh1.avif', 'alt' => 'The Light House - Stunning Ocean View'],
            ['src' => '/storage/lh2.avif', 'alt' => 'The Light House - Spacious Living Room'],
            ['src' => '/storage/lh3.avif', 'alt' => 'The Light House - Modern Kitchen'],
            ['src' => '/storage/lh4.avif', 'alt' => 'The Light House - Comfortable Bedroom'],
            ['src' => '/storage/lh5.avif', 'alt' => 'The Light House - Luxury Bathroom'],
            ['src' => '/storage/lh6.avif', 'alt' => 'The Light House - Elegant Dining Area'],
            ['src' => '/storage/lh7.avif', 'alt' => 'The Light House - Private Balcony'],
            ['src' => '/storage/lh8.avif', 'alt' => 'The Light House - Beautiful Exterior']
        ];
        $galleryId = 'lighthouselh-gallery';
    @endphp

    <!-- Modal -->
    <div
        id="modal-{{ $galleryId }}"
        class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4"
        data-modal="{{ $galleryId }}"
    >
        <div class="relative max-w-6xl max-h-full w-full h-full flex items-center justify-center">
            <!-- Close Button -->
            <button
                class="absolute top-4 right-4 text-white hover:text-gray-300 z-20"
                data-modal-close="{{ $galleryId }}"
            >
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>

            <!-- Previous Button -->
            <button
                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-20"
                data-modal-prev="{{ $galleryId }}"
            >
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>

            <!-- Next Button -->
            <button
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-20"
                data-modal-next="{{ $galleryId }}"
            >
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>

            <!-- Main Modal Image -->
            <div class="relative w-full h-full flex items-center justify-center pb-24">
                <img
                    id="modal-image-{{ $galleryId }}"
                    src="{{ $modalImages[0]['src'] }}"
                    alt="{{ $modalImages[0]['alt'] }}"
                    class="max-w-full max-h-full object-contain"
                >
            </div>

            <!-- Image Counter -->
            <div class="absolute bottom-20 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-60 text-white px-3 py-1 rounded-lg text-sm">
                <span id="modal-counter-{{ $galleryId }}">1 / {{ count($modalImages) }}</span>
            </div>

            <!-- Thumbnail Strip -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 bg-black bg-opacity-60 p-2 rounded-lg max-w-full overflow-x-auto">
                @foreach($modalImages as $index => $image)
                    <img
                        src="{{ $image['src'] }}"
                        alt="{{ $image['alt'] }}"
                        class="w-16 h-16 object-cover rounded cursor-pointer opacity-60 hover:opacity-100 transition-opacity {{ $index === 0 ? 'ring-2 ring-white opacity-100' : '' }}"
                        data-modal-thumb="{{ $galleryId }}"
                        data-thumb-index="{{ $index }}"
                    >
                @endforeach
            </div>
        </div>

        <!-- Hidden data for JavaScript -->
        <script type="application/json" data-gallery-images="{{ $galleryId }}">
            {!! json_encode($modalImages) !!}
        </script>

        <script>

            // Image modal
            const imageModal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImg');
            const closeImageBtn = document.getElementById('closeImageBtn');
            document.querySelectorAll('.enlargeable').forEach(img => {
                img.addEventListener('click', () => {
                    modalImg.src = img.getAttribute('data-img') || img.src;
                    imageModal.classList.remove('hidden');
                });
            });
            closeImageBtn.addEventListener('click', () => {
                imageModal.classList.add('hidden');
                modalImg.src = '';
            });
            window.addEventListener('click', (e) => {
                if (e.target === imageModal) {
                    imageModal.classList.add('hidden');
                    modalImg.src = '';
                }
            });
        </script>
    </div>
