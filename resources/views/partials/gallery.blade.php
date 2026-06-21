<!-- ============================================================
     GALLERY SECTION  —  gallery-section.html
     Auto slider · 4 visible · no gaps · lightbox on click
     Link in <head>: <link rel="stylesheet" href="gallery-section.css">
     Link before </body>: <script src="gallery-section.js"></script>
     ============================================================ -->

<section id="gallery">

  <!-- ── Slider (full width, no gaps) ── -->
  <div class="gl-viewport" id="glViewport">
    <div class="gl-track" id="glTrack">

      <div class="gl-slide" data-index="0">
        <img src="{{ asset('assets/images/gallery/1.jpg') }}" alt="Embroidered polo shirts" draggable="false">
        <div class="gl-slide-overlay">
          <i class="bi bi-zoom-in"></i>
        </div>
      </div>

      <div class="gl-slide" data-index="1">
        <img src="{{ asset('assets/images/gallery/2.jpg') }}" alt="Corporate branded jackets" draggable="false">
        <div class="gl-slide-overlay">
          <i class="bi bi-zoom-in"></i>
        </div>
      </div>

      <div class="gl-slide" data-index="2">
        <img src="{{ asset('assets/images/gallery/3.jpg') }}" alt="Screen printed sportswear" draggable="false">
        <div class="gl-slide-overlay">
          <i class="bi bi-zoom-in"></i>
        </div>
      </div>

      <div class="gl-slide" data-index="3">
        <img src="{{ asset('assets/images/gallery/4.jpg') }}" alt="Embroidered workwear" draggable="false">
        <div class="gl-slide-overlay">
          <i class="bi bi-zoom-in"></i>
        </div>
      </div>

      <div class="gl-slide" data-index="4">
        <img src="{{ asset('assets/images/gallery/5.jpg') }}" alt="Custom school uniforms" draggable="false">
        <div class="gl-slide-overlay">
          <i class="bi bi-zoom-in"></i>
        </div>
      </div>

      <div class="gl-slide" data-index="5">
        <img src="{{ asset('assets/images/gallery/6.jpg') }}" alt="Branded hospitality uniforms" draggable="false">
        <div class="gl-slide-overlay">
          <i class="bi bi-zoom-in"></i>
        </div>
      </div>

    </div>
  </div>

  <!-- ── Dots ── -->
  <!-- <div class="gl-dots" id="glDots"></div> -->

</section>


<!-- ============================================================
     LIGHTBOX OVERLAY
============================================================ -->
<div class="gl-lightbox" id="glLightbox" aria-hidden="true" role="dialog" aria-label="Image viewer">
  <div class="gl-lb-backdrop" id="glLbBackdrop"></div>

  <button class="gl-lb-close" id="glLbClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>

  <button class="gl-lb-prev" id="glLbPrev" aria-label="Previous image"><i class="bi bi-arrow-left"></i></button>
  <button class="gl-lb-next" id="glLbNext" aria-label="Next image"><i class="bi bi-arrow-right"></i></button>

  <div class="gl-lb-img-wrap">
    <img src="" alt="" class="gl-lb-img" id="glLbImg">
  </div>

  <div class="gl-lb-counter" id="glLbCounter">1 / 6</div>
</div>