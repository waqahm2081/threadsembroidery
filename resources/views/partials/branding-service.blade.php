@once
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/branding-service.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/js/branding-service.js') }}"></script>
@endpush
@endonce

<section id="branding-service" aria-labelledby="bs-heading">
    <div class="bs-glow" aria-hidden="true"></div>

    <div class="bs-container">

        <!-- Left: Content -->
        <div class="bs-content" data-bs-reveal="left">
            <div class="bs-eyebrow">
                <span class="bs-eyebrow-dot"></span>
                Own Garments Service
            </div>

            <h2 class="bs-heading" id="bs-heading">
                Already Have Garments?<br>
                <span class="bs-accent">We'll Brand Them For You.</span>
            </h2>

            <p class="bs-lead">
                Send us garments you've already purchased and our in-house team will apply professional embroidery or DTF print — chest, back, sleeves and more — to your exact specifications.
            </p>

            <div class="bs-features">
                <div class="bs-feature">
                    <span class="bs-feature-icon"><i class="bi bi-box-seam"></i></span>
                    <div>
                        <strong>Your Garments, Our Craft</strong>
                        <span>Ideal for workwear, event kits and items you've sourced elsewhere.</span>
                    </div>
                </div>
                <div class="bs-feature">
                    <span class="bs-feature-icon"><i class="bi bi-brush"></i></span>
                    <div>
                        <strong>Embroidery &amp; DTF Print</strong>
                        <span>Left chest, centre chest, large back, sleeves — tick all that apply.</span>
                    </div>
                </div>
                <div class="bs-feature">
                    <span class="bs-feature-icon"><i class="bi bi-calendar-check"></i></span>
                    <div>
                        <strong>10–14 Working Days</strong>
                        <span>Clear turnaround from order confirmation to completed branding.</span>
                    </div>
                </div>
                <div class="bs-feature">
                    <span class="bs-feature-icon"><i class="bi bi-stack"></i></span>
                    <div>
                        <strong>Minimum 5 Garments</strong>
                        <span>Perfect for teams, clubs and small batch branding runs.</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('branding-enquiry') }}" class="bs-cta-btn">
                Start Your Enquiry
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- Right: Visual -->
        <div class="bs-visual" data-bs-reveal="right">
            <div class="bs-visual-frame">
                <img src="{{ asset('assets/images/about-2.jpeg') }}" alt="Professional embroidery being applied to a garment" loading="lazy" />
                <div class="bs-visual-overlay" aria-hidden="true"></div>
            </div>

            <div class="bs-float-card bs-float-card--tl">
                <i class="bi bi-calendar3"></i>
                <div>
                    <strong>10–14 Days</strong>
                    <span>Working turnaround</span>
                </div>
            </div>

            <div class="bs-float-card bs-float-card--br">
                <i class="bi bi-patch-check-fill"></i>
                <div>
                    <strong>In-House Quality</strong>
                    <span>Embroidery &amp; print</span>
                </div>
            </div>

            <div class="bs-ring" aria-hidden="true"></div>
            <div class="bs-dot-grid" aria-hidden="true"></div>
        </div>

    </div>
</section>
