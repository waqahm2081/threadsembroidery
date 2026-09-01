{{-- Guest login/register CTA on shop cart & checkout --}}
@if(!auth()->check())
@php $redirect = urlencode(request()->fullUrl()); @endphp
<div class="guest-account-cta" role="region" aria-label="Account benefits">
    <div class="guest-account-cta-inner">
        <div class="guest-account-cta-icon" aria-hidden="true">
            <i class="bi bi-person-badge-fill"></i>
        </div>
        <div class="guest-account-cta-content">
            <p class="guest-account-cta-title">Login or register for order history &amp; tracking</p>
            <p class="guest-account-cta-note">Guest checkout is still available.</p>
        </div>
        <div class="guest-account-cta-actions">
            <a href="{{ route('login') }}?redirect={{ $redirect }}" class="guest-account-cta-btn guest-account-cta-btn--login">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
            <a href="{{ route('login') }}?redirect={{ $redirect }}#signup" class="guest-account-cta-btn guest-account-cta-btn--register">
                <i class="bi bi-person-plus"></i> Register
            </a>
        </div>
    </div>
</div>
@endif
