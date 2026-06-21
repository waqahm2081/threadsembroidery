@extends('layouts.app')

@section('title', 'Create Account — Threads Embroidery &amp; Print Ltd')

@section('body_class')
auth-page
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endpush

@section('content')

<!-- ============================================================
         PAGE HERO / BREADCRUMB BANNER
    ============================================================ -->
    <section id="auth-hero">
        <div class="ah-container">
            <div class="ah-eyebrow">
                <span class="ah-eyebrow-dot"></span>
                Account Access
            </div>
            <h1 class="ah-title">Create Your <span class="ah-accent">Account</span></h1>
            <p class="ah-sub">Join thousands of businesses, schools and sports teams who trust Threads.</p>
            <nav class="ah-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="ah-sep"><i class="bi bi-chevron-right"></i></span>
                <span>Sign Up</span>
            </nav>
        </div>
    </section>


    <!-- ============================================================
         AUTH WRAPPER — SIGNUP
    ============================================================ -->
    <main id="auth-main">
        <div class="auth-bg-glow" aria-hidden="true"></div>

        <div class="auth-card-wrap" id="authCardWrap">

            <div class="auth-panel" id="panelSignup">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="auth-logo-link">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Threads Embroidery & Print" class="auth-logo">
                </a>

                <div class="auth-panel-header">
                    <h1 class="auth-title">Create your account</h1>
                    <p class="auth-subtitle">Join thousands of businesses who trust Threads</p>
                </div>

                <form class="auth-form" id="signupForm" novalidate>

                    <div class="auth-form-row">
                        <div class="auth-field">
                            <label class="auth-label" for="signupFirstName">First Name</label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-person auth-input-icon"></i>
                                <input type="text" id="signupFirstName" class="auth-input" placeholder="John" required />
                            </div>
                            <span class="auth-error">Please enter your first name.</span>
                        </div>
                        <div class="auth-field">
                            <label class="auth-label" for="signupLastName">Last Name</label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-person auth-input-icon"></i>
                                <input type="text" id="signupLastName" class="auth-input" placeholder="Smith" required />
                            </div>
                            <span class="auth-error">Please enter your last name.</span>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="signupEmail">Email Address</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-envelope auth-input-icon"></i>
                            <input type="email" id="signupEmail" class="auth-input"
                                placeholder="you@company.co.uk" autocomplete="email" required />
                        </div>
                        <span class="auth-error">Please enter a valid email address.</span>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="signupCompany">Company Name <span class="auth-optional">(optional)</span></label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-building auth-input-icon"></i>
                            <input type="text" id="signupCompany" class="auth-input" placeholder="Your Company Ltd" />
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="signupPassword">Password</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-lock auth-input-icon"></i>
                            <input type="password" id="signupPassword" class="auth-input"
                                placeholder="Min. 8 characters" autocomplete="new-password" required />
                            <button type="button" class="auth-eye-btn" data-target="signupPassword" aria-label="Show password">
                                <i class="bi bi-eye auth-eye-icon"></i>
                            </button>
                        </div>
                        <!-- Password strength bar -->
                        <div class="auth-strength-bar">
                            <div class="auth-strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="auth-strength-label" id="strengthLabel"></span>
                        <span class="auth-error">Password must be at least 8 characters.</span>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="signupConfirm">Confirm Password</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-lock-fill auth-input-icon"></i>
                            <input type="password" id="signupConfirm" class="auth-input"
                                placeholder="Repeat password" autocomplete="new-password" required />
                            <button type="button" class="auth-eye-btn" data-target="signupConfirm" aria-label="Show password">
                                <i class="bi bi-eye auth-eye-icon"></i>
                            </button>
                        </div>
                        <span class="auth-error">Passwords do not match.</span>
                    </div>

                    <div class="auth-remember">
                        <label class="auth-checkbox-item">
                            <input type="checkbox" id="signupTerms" required>
                            <span class="auth-checkbox-label">I agree to the <a href="#">Terms &amp; Conditions</a> and <a href="#">Privacy Policy</a></span>
                        </label>
                        <span class="auth-error" id="termsError" style="display:none">You must agree to continue.</span>
                    </div>

                    <button type="submit" class="auth-submit-btn" id="signupSubmit">
                        <span class="auth-submit-inner">
                            Create Account
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </button>

                </form>

                <p class="auth-switch">
                    Already have an account?
                    <a href="{{ route('login') }}" class="auth-switch-link">Sign in</a>
                </p>

            </div>

        </div>
    </main>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/auth.js') }}"></script>
@endpush
