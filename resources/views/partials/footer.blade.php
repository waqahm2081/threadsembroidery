

    <!-- ============================================================
     FOOTER  —  footer.html
     Light theme · 4-column layout
     Link in <head>: <link rel="stylesheet" href="footer.css">
     ============================================================ -->

    <footer id="site-footer">

        <!-- ── Main footer columns ── -->
        <div class="footer-main">
            <div class="footer-grid">

                <!-- Col 1: Logo + About + Social -->
                <div class="footer-col footer-col--brand">
                    <a href="{{ route('home') }}" class="footer-logo-link">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Threads Embroidery & Print" class="footer-logo">
                    </a>
                    <p class="footer-about">
                        UK's trusted custom embroidery and print specialists. We help businesses, schools and sports
                        clubs look their best — with branded garments crafted to last.
                    </p>
                    <div class="footer-social">
                        <a href="https://www.facebook.com/profile.php?id=61576908124134" target="_blank" rel="noopener"
                            aria-label="Facebook" class="footer-social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/threadsembroidery/" target="_blank" rel="noopener"
                            aria-label="Instagram" class="footer-social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                            </svg>
                        </a>
                        <a href="https://www.tiktok.com/@threads.embroider0" target="_blank" rel="noopener"
                            aria-label="TikTok" class="footer-social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="footer-col">
                    <h4 class="footer-col-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('branding-enquiry') }}">Branding Enquiry</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Col 3: Featured Categories -->
                <div class="footer-col">
                    <h4 class="footer-col-title">Featured Categories</h4>
                    <ul class="footer-links">
                        @foreach(array_slice($catalogCategories ?? [], 0, 5) as $cat)
                            <li><a href="{{ route('shop.category', $cat['slug']) }}">{{ $cat['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Col 4: Contact Details -->
                <div class="footer-col">
                    <h4 class="footer-col-title">Get In Touch</h4>
                    <ul class="footer-contact-list">
                        <li>
                            <span class="footer-contact-icon"><i class="bi bi-telephone"></i></span>
                            <a href="tel:+447916916369">+44 7916 916369</a>
                        </li>
                        <li>
                            <span class="footer-contact-icon"><i class="bi bi-envelope"></i></span>
                            <a
                                href="mailto:customerservice@threadsembroideryprint.co.uk">customerservice@threadsembroideryprint.co.uk</a>
                        </li>
                        <li>
                            <span class="footer-contact-icon"><i class="bi bi-geo-alt"></i></span>
                            <span>Cullompton, United Kingdom</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- ── Bottom bar ── -->
        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <p class="footer-copy">
                    &copy; <span id="footer-year"></span> Threads Embroidery &amp; Print Ltd. All rights reserved.
                </p>
                <div class="footer-legal-links">
                    <a href="#">Terms &amp; Conditions</a>
                    <span class="footer-legal-divider"></span>
                    <a href="#">Privacy Policy</a>
                </div>
            </div>
        </div>

    </footer>