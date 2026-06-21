@extends('layouts.app')

@section('title', 'Forgot Password — Threads Embroidery &amp; Print Ltd')

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
            <h1 class="ah-title">Reset Your <span class="ah-accent">Password</span></h1>
            <p class="ah-sub">No worries — we'll send a secure link to get you back into your account.</p>
            <nav class="ah-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="ah-sep"><i class="bi bi-chevron-right"></i></span>
                <a href="{{ route('login') }}">Login</a>
                <span class="ah-sep"><i class="bi bi-chevron-right"></i></span>
                <span>Forgot Password</span>
            </nav>
        </div>
    </section>


    <!-- ============================================================
         AUTH WRAPPER — FORGOT PASSWORD
    ============================================================ -->
    <main id="auth-main">
        <div class="auth-bg-glow" aria-hidden="true"></div>

        <div class="auth-card-wrap" id="authCardWrap">

            <!-- ── STEP 1: Enter email ── -->
            <div class="auth-panel" id="panelForgot">

                <a href="{{ route('home') }}" class="auth-logo-link">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Threads Embroidery & Print" class="auth-logo">
                </a>

                <div class="auth-panel-header">
                    <div class="auth-back-icon"><i class="bi bi-lock-fill"></i></div>
                    <h1 class="auth-title">Forgot your password?</h1>
                    <p class="auth-subtitle">Enter your email and we'll send you a reset link.</p>
                </div>

                <form class="auth-form" id="forgotForm" novalidate>

                    <div class="auth-field">
                        <label class="auth-label" for="forgotEmail">Email Address</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-envelope auth-input-icon"></i>
                            <input type="email" id="forgotEmail" class="auth-input"
                                placeholder="you@company.co.uk" autocomplete="email" required />
                        </div>
                        <span class="auth-error">Please enter a valid email address.</span>
                    </div>

                    <button type="submit" class="auth-submit-btn" id="forgotSubmit">
                        <span class="auth-submit-inner">
                            Send Reset Link
                            <i class="bi bi-send-fill"></i>
                        </span>
                    </button>

                </form>

                <!-- Success state (hidden initially) -->
                <div class="auth-reset-success" id="resetSuccess" style="display:none">
                    <div class="auth-success-icon"><i class="bi bi-envelope-check-fill"></i></div>
                    <h3 class="auth-success-title">Check your inbox</h3>
                    <p class="auth-success-text">We've sent a password reset link to <strong id="resetEmailDisplay"></strong>. It may take a few minutes to arrive.</p>
                    <a href="{{ route('login') }}" class="auth-submit-btn" style="display:inline-flex;text-decoration:none;justify-content:center;margin-top:8px;">
                        <span class="auth-submit-inner">Back to Login <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>

                <p class="auth-switch" id="fpSwitch">
                    Remembered it?
                    <a href="{{ route('login') }}" class="auth-switch-link">Back to login</a>
                </p>

            </div>

        </div>
    </main>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/auth.js') }}"></script>
@endpush
