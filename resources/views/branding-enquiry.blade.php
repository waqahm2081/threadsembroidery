@extends('layouts.app')

@section('title', 'Branding Enquiry — Threads Embroidery &amp; Print Ltd')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/branding-enquiry.css') }}">
@endpush

@section('content')

<!-- ============================================================
     PAGE HERO
============================================================ -->
<section id="be-hero">
    <div class="be-hero-container">
        <div class="be-hero-eyebrow">
            <span class="be-hero-eyebrow-dot"></span>
            Own Garments Service
        </div>
        <h1 class="be-hero-title">Branding <span class="be-hero-accent">Enquiry</span></h1>
        <p class="be-hero-sub">Already have garments? Send us your items and we'll apply professional embroidery or print — turnaround in 10 to 14 working days.</p>
        <nav class="be-hero-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span class="be-hero-sep"><i class="bi bi-chevron-right"></i></span>
            <span>Branding Enquiry</span>
        </nav>
    </div>
</section>


<!-- ============================================================
     ENQUIRY FORM
============================================================ -->
<section id="be-body">
    <div class="be-container">

        <!-- Progress sidebar (desktop) -->
        <aside class="be-progress-aside" aria-hidden="true">
            <div class="be-progress-card">
                <h3 class="be-progress-heading">Your Progress</h3>
                <ol class="be-progress-list" id="beProgressList">
                    <li class="be-progress-item active" data-step="1">
                        <span class="be-progress-num">1</span>
                        <div class="be-progress-text">
                            <strong>Welcome</strong>
                            <span>Minimum order</span>
                        </div>
                    </li>
                    <li class="be-progress-item" data-step="2">
                        <span class="be-progress-num">2</span>
                        <div class="be-progress-text">
                            <strong>Your Details</strong>
                            <span>Contact information</span>
                        </div>
                    </li>
                    <li class="be-progress-item" data-step="3">
                        <span class="be-progress-num">3</span>
                        <div class="be-progress-text">
                            <strong>Garments</strong>
                            <span>Items &amp; colours</span>
                        </div>
                    </li>
                    <li class="be-progress-item" data-step="4">
                        <span class="be-progress-num">4</span>
                        <div class="be-progress-text">
                            <strong>Branding</strong>
                            <span>Placements &amp; artwork</span>
                        </div>
                    </li>
                </ol>
            </div>

            <div class="be-aside-note">
                <i class="bi bi-info-circle-fill"></i>
                <p>Turnaround is <strong>10 to 14 working days</strong> once your order is confirmed. Complete this form in full and we'll get back to you with a quote.</p>
            </div>
        </aside>

        <!-- Form card -->
        <div class="be-form-col">
            <div class="be-form-card">

                <!-- Mobile progress bar -->
                <div class="be-mobile-progress" aria-label="Form progress">
                    <div class="be-mobile-progress-bar">
                        <div class="be-mobile-progress-fill" id="beMobileProgressFill"></div>
                    </div>
                    <span class="be-mobile-progress-label" id="beMobileProgressLabel">Step 1 of 4</span>
                </div>

                <form class="be-form" id="brandingEnquiryForm" novalidate>

                    <!-- ── STEP 1: Welcome ── -->
                    <div class="be-step active" data-step="1">
                        <div class="be-step-header">
                            <h2 class="be-step-title">Order Enquiry Form</h2>
                            <p class="be-step-intro">Thank you so much for considering us for your branded clothing or workwear! We cannot wait to review your enquiry and get back to you.</p>
                        </div>

                        <div class="be-minimum-notice">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <p>We have a <strong>MINIMUM ORDER QUANTITY of 5 Garments.</strong></p>
                        </div>

                        <div class="be-turnaround-notice">
                            <i class="bi bi-calendar-check"></i>
                            <p>Standard turnaround is <strong>10 to 14 working days</strong> from order confirmation.</p>
                        </div>

                        <div class="be-field">
                            <label class="be-choice-box be-confirm-box" id="beConfirmBox">
                                <input type="checkbox" name="min_order_confirm" id="beMinOrderConfirm" class="be-choice-input" />
                                <span class="be-choice-indicator">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                                <span class="be-choice-label">I confirm my order will meet the minimum of 5 garments.</span>
                            </label>
                            <span class="be-error" id="beConfirmError">Please confirm you meet the minimum order quantity.</span>
                        </div>
                    </div>


                    <!-- ── STEP 2: Contact Details ── -->
                    <div class="be-step" data-step="2">
                        <div class="be-step-header">
                            <p class="be-step-intro">The more information you provide, the quicker we can prepare an accurate quote. Most quotes are sent without needing to contact you for additional details if this form is completed in full.</p>
                        </div>

                        <div class="be-field">
                            <label class="be-label" for="beFullName">Hi! What's your Full Name? <span class="be-required">*</span></label>
                            <div class="be-input-wrap">
                                <i class="bi bi-person be-input-icon"></i>
                                <input type="text" id="beFullName" name="full_name" class="be-input" placeholder="John Smith" required />
                            </div>
                            <span class="be-error">Please enter your full name.</span>
                        </div>

                        <div class="be-field">
                            <label class="be-label" for="beBusinessName">What's your business name? <span class="be-required">*</span></label>
                            <div class="be-input-wrap">
                                <i class="bi bi-building be-input-icon"></i>
                                <input type="text" id="beBusinessName" name="business_name" class="be-input" placeholder="Your Company Ltd" required />
                            </div>
                            <span class="be-error">Please enter your business name.</span>
                        </div>

                        <div class="be-form-row">
                            <div class="be-field">
                                <label class="be-label" for="beEmail">Email Address <span class="be-required">*</span></label>
                                <div class="be-input-wrap">
                                    <i class="bi bi-envelope be-input-icon"></i>
                                    <input type="email" id="beEmail" name="email" class="be-input" placeholder="john@company.co.uk" required />
                                </div>
                                <span class="be-error">Please enter a valid email address.</span>
                            </div>

                            <div class="be-field">
                                <label class="be-label" for="beWhatsapp">WhatsApp Number <span class="be-required">*</span></label>
                                <div class="be-input-wrap">
                                    <i class="bi bi-whatsapp be-input-icon"></i>
                                    <input type="tel" id="beWhatsapp" name="whatsapp" class="be-input" placeholder="+44 7700 900000" required />
                                </div>
                                <span class="be-error">Please enter your WhatsApp number.</span>
                            </div>
                        </div>
                    </div>


                    <!-- ── STEP 3: Garment Details ── -->
                    <div class="be-step" data-step="3">
                        <div class="be-field">
                            <label class="be-label" for="beGarments">Garments Required <span class="be-required">*</span></label>
                            <p class="be-hint">If you're unsure of sizes, just write "mixed sizes" or "size guide needed". This section must be filled in accurately for us to get a price quote over.</p>
                            <div class="be-textarea-wrap">
                                <textarea id="beGarments" name="garments" class="be-textarea" rows="5"
                                    placeholder="Example: 10 x T-Shirts (mixed sizes), 2 x Softshell Jackets, 5 x Hoodies (3 Medium, 2 Large), 4 x Hi-Vis Vests"
                                    required></textarea>
                            </div>
                            <span class="be-error">Please describe the garments you need branded.</span>
                        </div>

                        <div class="be-field">
                            <label class="be-label" for="beColours">Garment Colours <span class="be-required">*</span></label>
                            <div class="be-input-wrap">
                                <i class="bi bi-palette be-input-icon"></i>
                                <input type="text" id="beColours" name="colours" class="be-input" placeholder="e.g. Navy, Black, White" required />
                            </div>
                            <span class="be-error">Please enter the garment colours.</span>
                        </div>
                    </div>


                    <!-- ── STEP 4: Branding & Artwork ── -->
                    <div class="be-step" data-step="4">
                        <div class="be-field">
                            <label class="be-label">What placements would you like? <span class="be-required">*</span></label>
                            <p class="be-hint">Tick all that apply</p>
                            <div class="be-choice-grid be-choice-grid--multi" id="bePlacements">
                                @foreach(['Left Chest', 'Centre Chest', 'Right Chest', 'Large Back', 'Sleeve'] as $placement)
                                    <label class="be-choice-box">
                                        <input type="checkbox" name="placements[]" value="{{ $placement }}" class="be-choice-input" />
                                        <span class="be-choice-indicator"><i class="bi bi-check-lg"></i></span>
                                        <span class="be-choice-label">{{ $placement }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <span class="be-error" id="bePlacementsError">Please select at least one placement.</span>
                        </div>

                        <div class="be-field">
                            <label class="be-label">How would you like your branding applied? <span class="be-required">*</span></label>
                            <div class="be-choice-grid be-choice-grid--method" id="beBrandingMethod">
                                @foreach([
                                    'embroidery' => 'Embroidery',
                                    'dtf' => 'DTF Print',
                                    'both' => 'Both',
                                    'not_sure' => 'Not Sure (Recommended for me)',
                                ] as $value => $label)
                                    <label class="be-choice-box">
                                        <input type="radio" name="branding_method" value="{{ $value }}" class="be-choice-input" />
                                        <span class="be-choice-indicator"><i class="bi bi-check-lg"></i></span>
                                        <span class="be-choice-label">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <span class="be-error" id="beMethodError">Please select a branding method.</span>
                        </div>

                        <div class="be-field">
                            <label class="be-label">Please upload your logo/design files <span class="be-required">*</span></label>
                            <div class="be-upload-zone" id="beUploadZone">
                                <input type="file" id="beDesignFiles" name="design_files[]" class="be-upload-input" accept=".png,.jpg,.jpeg,.svg,.pdf,.ai,.eps,.psd" multiple />
                                <div class="be-upload-content">
                                    <i class="bi bi-cloud-arrow-up be-upload-icon"></i>
                                    <p class="be-upload-text">Click to choose files or drag here</p>
                                    <span class="be-upload-limit">Size limit: 10 MB per file</span>
                                </div>
                            </div>
                            <ul class="be-file-list" id="beFileList"></ul>
                            <span class="be-error" id="beUploadError">Please upload at least one design file.</span>
                        </div>
                    </div>


                    <!-- Success state -->
                    <div class="be-success" id="beSuccess" style="display:none">
                        <div class="be-success-icon"><i class="bi bi-check-circle-fill"></i></div>
                        <h3 class="be-success-title">Enquiry Submitted!</h3>
                        <p class="be-success-text">Thank you for your enquiry. Our team will review your details and get back to you with a quote. Once confirmed, your branding will be completed within 10 to 14 working days.</p>
                        <a href="{{ route('home') }}" class="be-success-btn">Back to Home</a>
                    </div>


                    <!-- Navigation -->
                    <div class="be-nav" id="beNav">
                        <button type="button" class="be-btn be-btn--back" id="beBackBtn" style="display:none">
                            <i class="bi bi-arrow-left"></i> Back
                        </button>
                        <button type="button" class="be-btn be-btn--next" id="beNextBtn">
                            Next <i class="bi bi-arrow-right"></i>
                        </button>
                        <button type="submit" class="be-btn be-btn--submit" id="beSubmitBtn" style="display:none">
                            <i class="bi bi-send-fill"></i> Submit Enquiry
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/branding-enquiry.js') }}"></script>
@endpush
