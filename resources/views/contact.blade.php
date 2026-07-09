@extends('layouts.app')

@section('title', 'Contact Us — Threads Embroidery &amp; Print Ltd')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}">
@endpush

@section('content')

<!-- ============================================================
         PAGE HERO
    ============================================================ -->
    <section id="contact-hero">
        <div class="cth-container">
            <div class="cth-eyebrow">
                <span class="cth-eyebrow-dot"></span>
                Get In Touch
            </div>
            <h1 class="cth-title">Contact <span class="cth-accent">Us</span></h1>
            <p class="cth-sub">Have a question or want a quote? Our team is always ready to help.</p>
            <nav class="cth-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="cth-sep"><i class="bi bi-chevron-right"></i></span>
                <span>Contact Us</span>
            </nav>
        </div>
    </section>


    <!-- ============================================================
         CONTACT BODY
    ============================================================ -->
    <section id="contact-body">
        <div class="ctb-container">

            <!-- LEFT: Form -->
            <div class="ctb-form-col">
                <div class="ctb-form-card">
                    <div class="ctb-form-header">
                        <h2 class="ctb-form-title">Send Us a Message</h2>
                        <p class="ctb-form-sub">Fill in the form and we'll get back to you as soon as possible.</p>
                    </div>

                    <form class="ctb-form" id="contactForm" novalidate>
                        <div class="ctb-form-row">
                            <div class="ctb-field">
                                <label class="ctb-label" for="ctFirstName">First Name</label>
                                <div class="ctb-input-wrap">
                                    <i class="bi bi-person ctb-input-icon"></i>
                                    <input type="text" id="ctFirstName" class="ctb-input" placeholder="John" required />
                                </div>
                                <span class="ctb-error">Please enter your first name.</span>
                            </div>
                            <div class="ctb-field">
                                <label class="ctb-label" for="ctLastName">Last Name</label>
                                <div class="ctb-input-wrap">
                                    <i class="bi bi-person ctb-input-icon"></i>
                                    <input type="text" id="ctLastName" class="ctb-input" placeholder="Smith" required />
                                </div>
                                <span class="ctb-error">Please enter your last name.</span>
                            </div>
                            <div class="ctb-field">
                                <label class="ctb-label" for="ctEmail">Email Address</label>
                                <div class="ctb-input-wrap">
                                    <i class="bi bi-envelope ctb-input-icon"></i>
                                    <input type="email" id="ctEmail" class="ctb-input" placeholder="john@company.co.uk" required />
                                </div>
                                <span class="ctb-error">Please enter a valid email address.</span>
                            </div>
    
                            <div class="ctb-field">
                                <label class="ctb-label" for="ctPhone">Phone Number <span class="ctb-optional">(optional)</span></label>
                                <div class="ctb-input-wrap">
                                    <i class="bi bi-telephone ctb-input-icon"></i>
                                    <input type="tel" id="ctPhone" class="ctb-input" placeholder="+44 7700 900000" />
                                </div>
                            </div>
                        </div>


                        <div class="ctb-field">
                            <label class="ctb-label" for="ctCompany">Company Name <span class="ctb-optional">(optional)</span></label>
                            <div class="ctb-input-wrap">
                                <i class="bi bi-building ctb-input-icon"></i>
                                <input type="text" id="ctCompany" class="ctb-input" placeholder="Your Company Ltd" />
                            </div>
                        </div>

                        <div class="ctb-field">
                            <label class="ctb-label" for="ctSubject">Subject</label>
                            <div class="ctb-select-wrap">
                                <i class="bi bi-chat-dots ctb-input-icon"></i>
                                <select id="ctSubject" class="ctb-select" required>
                                    <option value="">Select a subject…</option>
                                    <option value="quote">Request a Quote</option>
                                    <option value="order">Order Enquiry</option>
                                    <option value="artwork">Artwork &amp; Design Help</option>
                                    <option value="bulk">Bulk Order</option>
                                    <option value="delivery">Delivery Query</option>
                                    <option value="other">Other</option>
                                </select>
                                <i class="bi bi-chevron-down ctb-select-chevron"></i>
                            </div>
                            <span class="ctb-error">Please select a subject.</span>
                        </div>

                        <div class="ctb-field">
                            <label class="ctb-label" for="ctMessage">Message</label>
                            <div class="ctb-textarea-wrap">
                                <textarea id="ctMessage" class="ctb-textarea" rows="4"
                                    placeholder="Tell us about your requirements — product type, quantity, colours, delivery timescales…"
                                    required></textarea>
                                <span class="ctb-char-count" id="ctCharCount">0 / 1000</span>
                            </div>
                            <span class="ctb-error">Please enter your message.</span>
                        </div>

                        <!-- Success message -->
                        <div class="ctb-success" id="ctSuccess" style="display:none">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>
                                <strong>Message sent!</strong>
                                <span>Thanks for reaching out — we'll be in touch shortly.</span>
                            </div>
                        </div>

                        <button type="submit" class="ctb-submit-btn" id="ctSubmit">
                            <span class="ctb-submit-inner">
                                <i class="bi bi-send-fill"></i>
                                Send Message
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- RIGHT: Contact details + map placeholder -->
            <div class="ctb-info-col">

                <!-- Contact cards -->
                <div class="ctb-info-cards">

                    <div class="ctb-info-card">
                        <div class="ctb-info-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div class="ctb-info-content">
                            <h4 class="ctb-info-title">Phone</h4>
                            <a href="tel:+447916916369" class="ctb-info-value">+44 7916 916369</a>
                            <span class="ctb-info-note">Mon–Fri, 9am–5:30pm</span>
                        </div>
                    </div>

                    <div class="ctb-info-card">
                        <div class="ctb-info-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div class="ctb-info-content">
                            <h4 class="ctb-info-title">Email</h4>
                            <a href="mailto:customerservice@threadsembroideryprint.co.uk" class="ctb-info-value">customerservice@threadsembroideryprint.co.uk</a>
                            <span class="ctb-info-note">We aim to reply within 1–2 working days</span>
                        </div>
                    </div>

                    <div class="ctb-info-card">
                        <div class="ctb-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div class="ctb-info-content">
                            <h4 class="ctb-info-title">Location</h4>
                            <span class="ctb-info-value">Cullompton, Devon</span>
                            <span class="ctb-info-note">United Kingdom</span>
                        </div>
                    </div>

                    <div class="ctb-info-card">
                        <div class="ctb-info-icon"><i class="bi bi-clock-fill"></i></div>
                        <div class="ctb-info-content">
                            <h4 class="ctb-info-title">Opening Hours</h4>
                            <span class="ctb-info-value">Mon–Fri: 9:00am – 5:30pm</span>
                            <span class="ctb-info-note">Sat–Sun: Closed</span>
                        </div>
                    </div>

                </div>

                <!-- Express quote strip -->
                <div class="ctb-express-strip">
                    <div class="ctb-express-icon"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <strong>Order Turnaround</strong>
                        <span>Standard orders are completed within 7 to 10 working days from artwork approval.</span>
                    </div>
                </div>

                <!-- Social links -->
                <div class="ctb-social">
                    <p class="ctb-social-label">Follow us</p>
                    <div class="ctb-social-links">
                        <a href="https://www.facebook.com/profile.php?id=61576908124134" target="_blank" rel="noopener" class="ctb-social-btn" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                            Facebook
                        </a>
                        <a href="https://www.instagram.com/threadsembroidery/" target="_blank" rel="noopener" class="ctb-social-btn" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            Instagram
                        </a>
                        <a href="https://www.tiktok.com/@threads.embroider0" target="_blank" rel="noopener" class="ctb-social-btn" aria-label="TikTok">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06z"/></svg>
                            TikTok
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </section>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/contact.js') }}"></script>
@endpush
