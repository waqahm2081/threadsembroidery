@extends('layouts.app')

@section('title', 'Login — Threads Embroidery &amp; Print Ltd')

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
            <h1 class="ah-title">Welcome <span class="ah-accent">Back</span></h1>
            <p class="ah-sub">Sign in to manage your orders, track designs, and reorder your favourite items.</p>
            <nav class="ah-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="ah-sep"><i class="bi bi-chevron-right"></i></span>
                <span>Login</span>
            </nav>
        </div>
    </section>


    <!-- ============================================================
         AUTH WRAPPER
    ============================================================ -->
    <main id="auth-main">
        <div class="auth-bg-glow" aria-hidden="true"></div>

        <div class="auth-card-wrap" id="authCardWrap">

            <!-- ── LOGIN PANEL (shown first) ── -->
            <div class="auth-panel" id="panelLogin">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="auth-logo-link">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Threads Embroidery & Print" class="auth-logo">
                </a>

                <div class="auth-panel-header">
                    <h1 class="auth-title">Welcome back</h1>
                    <p class="auth-subtitle">Sign in to your Threads account</p>
                </div>

                <form class="auth-form" id="loginForm" novalidate>

                    <div class="auth-field">
                        <label class="auth-label" for="loginEmail">Email Address</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-envelope auth-input-icon"></i>
                            <input type="email" id="loginEmail" class="auth-input"
                                placeholder="you@company.co.uk" autocomplete="email" required />
                        </div>
                        <span class="auth-error">Please enter a valid email address.</span>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="loginPassword">
                            Password
                            <a href="{{ route('forgot-password') }}" class="auth-forgot-link">Forgot password?</a>
                        </label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-lock auth-input-icon"></i>
                            <input type="password" id="loginPassword" class="auth-input"
                                placeholder="Enter your password" autocomplete="current-password" required />
                            <button type="button" class="auth-eye-btn" data-target="loginPassword" aria-label="Show password">
                                <i class="bi bi-eye auth-eye-icon"></i>
                            </button>
                        </div>
                        <span class="auth-error">Please enter your password.</span>
                    </div>

                    <div class="auth-remember">
                        <label class="auth-checkbox-item">
                            <input type="checkbox" id="loginRemember">
                            <span class="auth-checkbox-label">Keep me signed in</span>
                        </label>
                    </div>

                    <div class="auth-form-error" id="loginFormError" style="display:none">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Invalid email or password. Please try again.</span>
                    </div>

                    <button type="submit" class="auth-submit-btn" id="loginSubmit">
                        <span class="auth-submit-inner">
                            Sign In
                            <i class="bi bi-arrow-right"></i>
                        </span>
                    </button>

                </form>

                <div class="auth-divider"><span>or</span></div>

                <p class="auth-switch">
                    Don't have an account?
                    <a href="{{ route('signup') }}" class="auth-switch-link">Create one free</a>
                </p>

            </div>
            <!-- end panelLogin -->

        </div>
    </main>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/auth.js') }}"></script>
@endpush
