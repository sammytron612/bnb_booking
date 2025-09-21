<!-- Modal for Image Gallery (reuse existing modal from image-placeholder component) -->

    <!-- Modal -->
    <div
        id="modal-{{ $galleryId }}"
        class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4"
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
                @if($images && $images->count() > 0)
                <img
                    id="modal-image-{{ $galleryId }}"
                    src="{{ $images->first()->location }}"
                    alt="{{ $images->first()->desc }}"
                    class="max-w-full max-h-full object-contain"
                >
                @endif
            </div>

            <!-- Image Counter -->
            <div class="absolute bottom-20 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-60 text-white px-3 py-1 rounded-lg text-sm">
                <span id="modal-counter-{{ $galleryId }}">1 / {{ $images ? $images->count() : 0 }}</span>
            </div>

            <!-- Thumbnail Strip -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 bg-black bg-opacity-60 p-2 rounded-lg max-w-full overflow-x-auto">
                @if($images)
                    @foreach($images as $index => $image)
                        <img
                            src="{{ $image->location }}"
                            alt="{{ $image->desc }}"
                            class="w-16 h-16 object-cover rounded cursor-pointer opacity-60 hover:opacity-100 transition-opacity {{ $index === 0 ? 'ring-2 ring-white opacity-100' : '' }}"
                            data-modal-thumb="{{ $galleryId }}"
                            data-thumb-index="{{ $index }}"
                        >
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Hidden data for JavaScript -->
        @if($images)
        <script type="application/json" data-gallery-images="{{ $galleryId }}">
            {!! json_encode($images->map(function($image) {
                return [
                    'src' => $image->location,
                    'alt' => $image->desc
                ];
            })) !!}
        </script>
        @endif

        <script>
            // Venue Image Modal Gallery - Initialize on DOM ready
            document.addEventListener('DOMContentLoaded', function() {
                const galleryId = '{{ $galleryId }}';
                const modal = document.querySelector(`[data-modal="${galleryId}"]`);
                const modalImage = document.getElementById(`modal-image-${galleryId}`);
                const modalCounter = document.getElementById(`modal-counter-${galleryId}`);
                const prevBtn = document.querySelector(`[data-modal-prev="${galleryId}"]`);
                const nextBtn = document.querySelector(`[data-modal-next="${galleryId}"]`);
                const closeBtn = document.querySelector(`[data-modal-close="${galleryId}"]`);
                const thumbnails = document.querySelectorAll(`[data-modal-thumb="${galleryId}"]`);

                // Debug: Check if elements are found
                console.log('Modal initialization:', {
                    galleryId: galleryId,
                    modal: modal,
                    modalImage: modalImage,
                    modalCounter: modalCounter,
                    triggers: document.querySelectorAll(`[data-modal-trigger="${galleryId}"]`).length
                });

                // Get images data
                const imagesDataScript = document.querySelector(`[data-gallery-images="${galleryId}"]`);
                const images = imagesDataScript ? JSON.parse(imagesDataScript.textContent) : [];

                console.log('Images loaded:', images.length);

                let currentIndex = 0;                // Function to update modal content
                function updateModal(index) {
                    if (index < 0) index = images.length - 1;
                    if (index >= images.length) index = 0;

                    currentIndex = index;

                    if (modalImage && images[index]) {
                        modalImage.src = images[index].src;
                        modalImage.alt = images[index].alt;
                    }

                    if (modalCounter) {
                        modalCounter.textContent = `${index + 1} / ${images.length}`;
                    }

                    // Update thumbnail highlighting
                    thumbnails.forEach((thumb, i) => {
                        if (i === index) {
                            thumb.classList.add('ring-2', 'ring-white', 'opacity-100');
                            thumb.classList.remove('opacity-60');
                        } else {
                            thumb.classList.remove('ring-2', 'ring-white', 'opacity-100');
                            thumb.classList.add('opacity-60');
                        }
                    });
                }

                // Open modal when any image is clicked
                document.addEventListener('click', function(e) {
                    const trigger = e.target.closest(`[data-modal-trigger="${galleryId}"]`);
                    if (trigger) {
                        console.log('Image clicked - opening modal');
                        e.preventDefault();
                        const imageIndex = parseInt(trigger.getAttribute('data-image-index')) || 0;
                        currentIndex = imageIndex;
                        updateModal(currentIndex);

                        if (modal) {
                            modal.classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                            console.log('Modal opened at image index:', currentIndex);
                        } else {
                            console.error('Modal element not found for galleryId:', galleryId);
                        }
                    }
                });                // Close modal
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    });
                }

                // Previous image
                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        updateModal(currentIndex - 1);
                    });
                }

                // Next image
                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        updateModal(currentIndex + 1);
                    });
                }

                // Thumbnail clicks
                thumbnails.forEach((thumb, index) => {
                    thumb.addEventListener('click', function() {
                        updateModal(index);
                    });
                });

                // Keyboard navigation
                document.addEventListener('keydown', function(e) {
                    if (!modal.classList.contains('hidden')) {
                        if (e.key === 'ArrowLeft') {
                            updateModal(currentIndex - 1);
                        } else if (e.key === 'ArrowRight') {
                            updateModal(currentIndex + 1);
                        } else if (e.key === 'Escape') {
                            modal.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    }
                });

                // Close modal when clicking outside
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                });
            });
        </script>
    </div>
