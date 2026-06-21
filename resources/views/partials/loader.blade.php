    <!-- ============================================================
     PAGE LOADER
     ============================================================ -->
    <div id="page-loader" role="status" aria-label="Loading Threads Embroidery & Print">

        <!-- Ambient glow blobs -->
        <div class="loader-blob loader-blob--a" aria-hidden="true"></div>
        <div class="loader-blob loader-blob--b" aria-hidden="true"></div>

        <!-- Central stage -->
        <div class="loader-stage">
            <!-- Logo -->
            <div class="loader-logo-wrap">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Threads Embroidery & Print" class="loader-logo">
            </div>

            <!-- Tagline words — stitch in one by one -->
            <div class="loader-tagline" aria-hidden="true">
                <span class="loader-word" style="--i:0">Stitch.</span>
                <span class="loader-word" style="--i:1">Print.</span>
                <span class="loader-word loader-word--accent" style="--i:2">Perfect.</span>
            </div>

            <!-- Thread progress bar -->
            <div class="loader-bar-wrap" aria-hidden="true">
                <div class="loader-bar-track">
                    <div class="loader-bar-fill" id="loaderBarFill"></div>
                    <!-- Moving thread knot -->
                    <div class="loader-bar-knot" id="loaderBarKnot"></div>
                </div>
                <div class="loader-bar-labels">
                    <span class="loader-bar-label" id="loaderPct">0%</span>
                    <span class="loader-bar-sublabel">Crafting your experience…</span>
                </div>
            </div>

        </div><!-- /loader-stage -->

        <!-- Decorative corner stitches -->
        <svg class="loader-corner loader-corner--tl" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path d="M4 56 L4 4 L56 4" stroke="rgba(238,96,35,0.25)" stroke-width="1.5" stroke-dasharray="5 4"
                stroke-linecap="round" />
        </svg>
        <svg class="loader-corner loader-corner--tr" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path d="M56 56 L56 4 L4 4" stroke="rgba(238,96,35,0.25)" stroke-width="1.5" stroke-dasharray="5 4"
                stroke-linecap="round" />
        </svg>
        <svg class="loader-corner loader-corner--bl" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path d="M4 4 L4 56 L56 56" stroke="rgba(238,96,35,0.25)" stroke-width="1.5" stroke-dasharray="5 4"
                stroke-linecap="round" />
        </svg>
        <svg class="loader-corner loader-corner--br" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true">
            <path d="M56 4 L56 56 L4 56" stroke="rgba(238,96,35,0.25)" stroke-width="1.5" stroke-dasharray="5 4"
                stroke-linecap="round" />
        </svg>

    </div><!-- /page-loader -->
