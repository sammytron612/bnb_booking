(function(){
  function initGalleryModals(){
    console.log('[Gallery] initGalleryModals called');

    // Find all gallery triggers that haven't been initialized
    const triggers = document.querySelectorAll('[data-modal-trigger]:not([data-gallery-ready])');

    triggers.forEach(trigger => {
      trigger.setAttribute('data-gallery-ready', '1');
      const galleryId = trigger.dataset.modalTrigger;
      const modal = document.querySelector(`[data-modal="${galleryId}"]`);

      if (!modal) {
        console.warn('[Gallery] Modal not found for gallery:', galleryId);
        return;
      }

      // Get images data
      const imagesScript = modal.querySelector(`[data-gallery-images="${galleryId}"]`);
      let images = [];

      if (imagesScript) {
        try {
          images = JSON.parse(imagesScript.textContent);
        } catch (e) {
          console.error('[Gallery] Failed to parse images data:', e);
          return;
        }
      }

      let currentIndex = 0;
      const modalImage = modal.querySelector(`#modal-image-${galleryId}`);
      const modalCounter = modal.querySelector(`#modal-counter-${galleryId}`);
      const thumbnails = modal.querySelectorAll(`[data-modal-thumb="${galleryId}"]`);

      function updateModal() {
        if (images[currentIndex] && modalImage) {
          modalImage.src = images[currentIndex].src;
          modalImage.alt = images[currentIndex].alt;
        }

        if (modalCounter) {
          modalCounter.textContent = `${currentIndex + 1} / ${images.length}`;
        }

        // Update thumbnail highlights
        thumbnails.forEach((thumb, index) => {
          thumb.classList.toggle('ring-2', index === currentIndex);
          thumb.classList.toggle('ring-white', index === currentIndex);
          thumb.classList.toggle('opacity-100', index === currentIndex);
          thumb.classList.toggle('opacity-60', index !== currentIndex);
        });
      }

      function openModal(startIndex = 0) {
        currentIndex = startIndex;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        updateModal();
      }

      function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
      }

      function nextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        updateModal();
      }

      function prevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateModal();
      }

      // Event listeners
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        const startIndex = parseInt(trigger.dataset.imageIndex) || 0;
        openModal(startIndex);
      });

      // Modal controls
      const closeBtn = modal.querySelector(`[data-modal-close="${galleryId}"]`);
      const nextBtn = modal.querySelector(`[data-modal-next="${galleryId}"]`);
      const prevBtn = modal.querySelector(`[data-modal-prev="${galleryId}"]`);

      if (closeBtn) closeBtn.addEventListener('click', closeModal);
      if (nextBtn) nextBtn.addEventListener('click', nextImage);
      if (prevBtn) prevBtn.addEventListener('click', prevImage);

      // Thumbnail clicks
      thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
          currentIndex = index;
          updateModal();
        });
      });

      // Keyboard navigation
      document.addEventListener('keydown', (e) => {
        if (modal.classList.contains('hidden')) return;

        if (e.key === 'Escape') closeModal();
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'ArrowRight') nextImage();
      });

      // Click outside to close
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });

      console.log('[Gallery] Modal initialized for:', galleryId, 'with', images.length, 'images');
    });
  }

  function initSliders(){
    console.log('[Slider] initSliders called');
    const roots = document.querySelectorAll('[data-slider]:not([data-ready])');
    if(!roots.length){ console.log('[Slider] no new slider roots found'); }
    roots.forEach(root => {
      root.setAttribute('data-ready','1');
      const track = root.querySelector('[data-slider-track]');
      if(!track){ console.warn('[Slider] missing track'); return; }
      const slides = Array.from(track.querySelectorAll('[data-slide]'));
      if(!slides.length){ console.warn('[Slider] no slides'); return; }
      const prevBtn = root.querySelector('[data-prev]');
      const nextBtn = root.querySelector('[data-next]');
      const indicatorsWrap = root.querySelector('[data-indicators]');
      if(!indicatorsWrap){ console.warn('[Slider] missing indicators container'); }
      const debugEl = root.querySelector('[data-debug]');
      const fallbackEl = root.querySelector('[data-fallback]');
      const interval = parseInt(root.dataset.interval || '3000',10);
      let current = 0; let timer = null;
      slides.forEach((_, idx) => {
        const dot = document.createElement('button');
        dot.type='button';
        dot.className='h-3 rounded-full transition-all duration-300 focus:outline-none bg-white/50 w-3';
        dot.setAttribute('aria-label','Go to slide '+(idx+1));
        dot.addEventListener('click',()=>go(idx));
        indicatorsWrap && indicatorsWrap.appendChild(dot);
      });
      function update(){
        slides.forEach((slide, i) => {
          if (i === current) {
            slide.style.opacity = '1';
            slide.style.zIndex = '2';
          } else {
            slide.style.opacity = '0';
            slide.style.zIndex = '1';
          }
        });
        indicatorsWrap && indicatorsWrap.querySelectorAll('button').forEach((b,i)=>{
          if(i===current){ b.classList.remove('bg-white/50','w-3'); b.classList.add('bg-white','w-6'); }
          else { b.classList.add('bg-white/50','w-3'); b.classList.remove('bg-white','w-6'); }
        });
        if(debugEl) debugEl.textContent=`slide ${current+1}/${slides.length}`;
        if(fallbackEl) fallbackEl.style.display='none';
      }
      function next(){ current=(current+1)%slides.length; update(); }
      function prev(){ current=(current-1+slides.length)%slides.length; update(); }
      function go(i){ current=i; update(); }
      function start(){ stop(); timer=setInterval(next, interval); }
      function stop(){ if(timer){ clearInterval(timer); timer=null; } }
      prevBtn && prevBtn.addEventListener('click', prev);
      nextBtn && nextBtn.addEventListener('click', next);
      root.addEventListener('mouseenter', stop);
      root.addEventListener('mouseleave', start);
      document.addEventListener('visibilitychange',()=>document.hidden?stop():start());
      window.addEventListener('resize', update);
      try { update(); start(); console.log('[Slider] Initialized', slides.length); }
      catch(e){ console.error('[Slider] init error',e); if(debugEl) debugEl.textContent='error'; }
    });
  }
  // expose
  window._sliderInit = initSliders;
  window._galleryInit = initGalleryModals;

  if(document.readyState==='loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initSliders();
      initGalleryModals();
    });
  } else {
    initSliders();
    initGalleryModals();
  }

  document.addEventListener('livewire:navigated', () => {
    initSliders();
    initGalleryModals();
  });

  const mo = new MutationObserver(muts=>{
    if(muts.some(m=>Array.from(m.addedNodes).some(n=>n.nodeType===1 && n.matches && (n.matches('[data-slider]') || n.matches('[data-modal-trigger]'))))){
      initSliders();
      initGalleryModals();
    }
  });
  mo.observe(document.documentElement,{childList:true,subtree:true});

  // Retry after 1s if still not initialized
  setTimeout(()=>{
    if(document.querySelector('[data-slider]') && !document.querySelector('[data-slider][data-ready]')){
      console.log('[Slider] retry init after delay');
      initSliders();
    }
    if(document.querySelector('[data-modal-trigger]') && !document.querySelector('[data-modal-trigger][data-gallery-ready]')){
      console.log('[Gallery] retry init after delay');
      initGalleryModals();
    }
  },1000);
})();


