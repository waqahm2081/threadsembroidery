@extends('layouts.app')

@section('title', 'Client Portal — Threads Embroidery &amp; Print Ltd')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/client-portal.css') }}">
@endpush

@section('content')

<!-- ============================================================
         PORTAL HERO / PAGE HEADER
    ============================================================ -->
    <div id="cp-page-header">
        <div class="cp-page-header-inner">
            <div class="cp-page-header-left">
                <div class="cp-eyebrow">
                    <span class="cp-eyebrow-dot"></span>
                    Client Portal
                </div>
                <h1 class="cp-page-title">Welcome back, <span class="cp-name-accent">James</span></h1>
                <p class="cp-page-sub">Manage your orders, track deliveries and update your account details.</p>
            </div>
            <div class="cp-page-header-right">
                <div class="cp-avatar-wrap">
                    <img src="{{ asset('assets/images/about1.jpg') }}" alt="James Thornton" class="cp-avatar" id="cpAvatarPreview">
                    <span class="cp-avatar-status"></span>
                </div>
                <div class="cp-header-user">
                    <span class="cp-header-name">James Thornton</span>
                    <span class="cp-header-email">james@thorntonservices.co.uk</span>
                </div>
            </div>
        </div>
    </div>


    <!-- ============================================================
         PORTAL BODY
    ============================================================ -->
    <div id="cp-portal">
        <div class="cp-portal-inner">

            <!-- ── Sidebar nav ── -->
            <aside class="cp-sidebar">
                <nav class="cp-nav">
                    <button class="cp-nav-item active" data-tab="summary">
                        <span class="cp-nav-icon"><i class="bi bi-grid-1x2"></i></span>
                        <span>Summary</span>
                    </button>
                    <button class="cp-nav-item" data-tab="orders">
                        <span class="cp-nav-icon"><i class="bi bi-bag"></i></span>
                        <span>Order History</span>
                        <span class="cp-nav-badge">6</span>
                    </button>
                    <button class="cp-nav-item" data-tab="track">
                        <span class="cp-nav-icon"><i class="bi bi-geo-alt"></i></span>
                        <span>Track Order</span>
                    </button>
                    <button class="cp-nav-item" data-tab="profile">
                        <span class="cp-nav-icon"><i class="bi bi-person"></i></span>
                        <span>Update Profile</span>
                    </button>
                </nav>

                <!-- Sidebar bottom -->
                <div class="cp-sidebar-bottom">
                    <a href="#" class="cp-sidebar-help">
                        <i class="bi bi-headset"></i> Need Help?
                    </a>
                    <a href="{{ route('login') }}" class="cp-sidebar-logout">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </a>
                </div>
            </aside>

            <!-- ── Main content area ── -->
            <main class="cp-main">


                <!-- ════════════════════════════════════════
                     TAB 1: SUMMARY
                ════════════════════════════════════════ -->
                <div class="cp-tab-panel active" id="cp-tab-summary">

                    <div class="cp-panel-header">
                        <h2 class="cp-panel-title">Account Summary</h2>
                        <span class="cp-panel-date">Last updated: <strong>22 May 2026</strong></span>
                    </div>

                    <!-- Stats grid -->
                    <div class="cp-stats-grid">

                        <div class="cp-stat-card">
                            <div class="cp-stat-icon cp-stat-icon--total">
                                <i class="bi bi-bag-check"></i>
                            </div>
                            <div class="cp-stat-body">
                                <span class="cp-stat-label">Total Orders</span>
                                <strong class="cp-stat-value">24</strong>
                            </div>
                        </div>

                        <div class="cp-stat-card">
                            <div class="cp-stat-icon cp-stat-icon--spend">
                                <i class="bi bi-currency-pound"></i>
                            </div>
                            <div class="cp-stat-body">
                                <span class="cp-stat-label">Total Spent</span>
                                <strong class="cp-stat-value">£4,832<small>.50</small></strong>
                            </div>
                        </div>

                        <div class="cp-stat-card">
                            <div class="cp-stat-icon cp-stat-icon--active">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <div class="cp-stat-body">
                                <span class="cp-stat-label">Active Orders</span>
                                <strong class="cp-stat-value">3</strong>
                            </div>
                        </div>

                        <div class="cp-stat-card">
                            <div class="cp-stat-icon cp-stat-icon--done">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            <div class="cp-stat-body">
                                <span class="cp-stat-label">Completed</span>
                                <strong class="cp-stat-value">21</strong>
                            </div>
                        </div>

                    </div>

                    <!-- Recent orders mini table -->
                    <div class="cp-section-block">
                        <div class="cp-block-header">
                            <h3 class="cp-block-title">Recent Orders</h3>
                            <button class="cp-block-link" data-tab-link="orders">View All <i class="bi bi-arrow-right"></i></button>
                        </div>
                        <div class="cp-table-wrap">
                            <table class="cp-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Product</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="cp-order-num">#TE-2045</span></td>
                                        <td>Classic Polo Shirt × 25</td>
                                        <td>18 May 2026</td>
                                        <td>£212.50</td>
                                        <td><span class="cp-status cp-status--processing">Processing</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="cp-order-num">#TE-2031</span></td>
                                        <td>Softshell Jacket × 10</td>
                                        <td>02 May 2026</td>
                                        <td>£220.00</td>
                                        <td><span class="cp-status cp-status--shipped">Shipped</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="cp-order-num">#TE-2019</span></td>
                                        <td>Fleece Hoodie × 30</td>
                                        <td>14 Apr 2026</td>
                                        <td>£442.50</td>
                                        <td><span class="cp-status cp-status--delivered">Delivered</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Quick actions -->
                    <div class="cp-section-block">
                        <h3 class="cp-block-title" style="margin-bottom:18px;">Quick Actions</h3>
                        <div class="cp-quick-actions">
                            <a href="#" class="cp-quick-card">
                                <i class="bi bi-magic"></i>
                                <span>New Custom Order</span>
                            </a>
                            <a href="#" class="cp-quick-card">
                                <i class="bi bi-files"></i>
                                <span>Reorder Previous</span>
                            </a>
                            <a href="#" class="cp-quick-card">
                                <i class="bi bi-headset"></i>
                                <span>Contact Support</span>
                            </a>
                            <a href="#" class="cp-quick-card">
                                <i class="bi bi-download"></i>
                                <span>Download Invoices</span>
                            </a>
                        </div>
                    </div>

                </div>
                <!-- /summary -->


                <!-- ════════════════════════════════════════
                     TAB 2: ORDER HISTORY
                ════════════════════════════════════════ -->
                <div class="cp-tab-panel" id="cp-tab-orders">

                    <div class="cp-panel-header">
                        <h2 class="cp-panel-title">Order History</h2>
                        <span class="cp-panel-date">24 orders total</span>
                    </div>

                    <!-- Filters -->
                    <div class="cp-filters">
                        <div class="cp-filter-group">
                            <button class="cp-filter-btn active" data-filter="all">All Orders</button>
                            <button class="cp-filter-btn" data-filter="processing">Processing</button>
                            <button class="cp-filter-btn" data-filter="shipped">Shipped</button>
                            <button class="cp-filter-btn" data-filter="delivered">Delivered</button>
                        </div>
                        <div class="cp-search-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" class="cp-search-input" id="cpOrderSearch" placeholder="Search orders…">
                        </div>
                    </div>

                    <!-- Orders table -->
                    <div class="cp-section-block">
                        <div class="cp-table-wrap">
                            <table class="cp-table" id="cpOrdersTable">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Product(s)</th>
                                        <th>Category</th>
                                        <th>Qty</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-status="processing">
                                        <td><span class="cp-order-num">#TE-2045</span></td>
                                        <td>Classic Polo Shirt</td>
                                        <td><span class="cp-tag">Corporatewear</span></td>
                                        <td>25</td>
                                        <td>18 May 2026</td>
                                        <td>£212.50</td>
                                        <td><span class="cp-status cp-status--processing">Processing</span></td>
                                        <td><button class="cp-row-track-btn" data-order="#TE-2045">Track</button></td>
                                    </tr>
                                    <tr data-status="shipped">
                                        <td><span class="cp-order-num">#TE-2031</span></td>
                                        <td>Softshell Jacket</td>
                                        <td><span class="cp-tag">Workwear</span></td>
                                        <td>10</td>
                                        <td>02 May 2026</td>
                                        <td>£220.00</td>
                                        <td><span class="cp-status cp-status--shipped">Shipped</span></td>
                                        <td><button class="cp-row-track-btn" data-order="#TE-2031">Track</button></td>
                                    </tr>
                                    <tr data-status="delivered">
                                        <td><span class="cp-order-num">#TE-2019</span></td>
                                        <td>Fleece Hoodie</td>
                                        <td><span class="cp-tag">Sports</span></td>
                                        <td>30</td>
                                        <td>14 Apr 2026</td>
                                        <td>£442.50</td>
                                        <td><span class="cp-status cp-status--delivered">Delivered</span></td>
                                        <td><button class="cp-row-track-btn" data-order="#TE-2019">Track</button></td>
                                    </tr>
                                    <tr data-status="delivered">
                                        <td><span class="cp-order-num">#TE-2008</span></td>
                                        <td>Hi-Vis Safety Vest</td>
                                        <td><span class="cp-tag">Safetywear</span></td>
                                        <td>50</td>
                                        <td>28 Mar 2026</td>
                                        <td>£325.00</td>
                                        <td><span class="cp-status cp-status--delivered">Delivered</span></td>
                                        <td><button class="cp-row-track-btn" data-order="#TE-2008">Track</button></td>
                                    </tr>
                                    <tr data-status="delivered">
                                        <td><span class="cp-order-num">#TE-1994</span></td>
                                        <td>School PE Kit Set</td>
                                        <td><span class="cp-tag">Schoolwear</span></td>
                                        <td>60</td>
                                        <td>10 Mar 2026</td>
                                        <td>£540.00</td>
                                        <td><span class="cp-status cp-status--delivered">Delivered</span></td>
                                        <td><button class="cp-row-track-btn" data-order="#TE-1994">Track</button></td>
                                    </tr>
                                    <tr data-status="processing">
                                        <td><span class="cp-order-num">#TE-1982</span></td>
                                        <td>Embroidered Cap</td>
                                        <td><span class="cp-tag">Accessories</span></td>
                                        <td>40</td>
                                        <td>05 Mar 2026</td>
                                        <td>£239.60</td>
                                        <td><span class="cp-status cp-status--processing">Processing</span></td>
                                        <td><button class="cp-row-track-btn" data-order="#TE-1982">Track</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- /orders -->


                <!-- ════════════════════════════════════════
                     TAB 3: TRACK ORDER
                ════════════════════════════════════════ -->
                <div class="cp-tab-panel" id="cp-tab-track">

                    <div class="cp-panel-header">
                        <h2 class="cp-panel-title">Track Your Order</h2>
                        <span class="cp-panel-date">Real-time tracking</span>
                    </div>

                    <!-- Order selector -->
                    <div class="cp-section-block">
                        <div class="cp-track-select-wrap">
                            <label class="cp-track-label" for="cpTrackSelect">Select an order to track</label>
                            <div class="cp-select-row">
                                <div class="cp-custom-select-wrap">
                                    <select class="cp-custom-select" id="cpTrackSelect">
                                        <option value="">— Choose an order number —</option>
                                        <option value="TE-2045">#TE-2045 — Classic Polo Shirt × 25 — 18 May 2026</option>
                                        <option value="TE-2031">#TE-2031 — Softshell Jacket × 10 — 02 May 2026</option>
                                        <option value="TE-2019">#TE-2019 — Fleece Hoodie × 30 — 14 Apr 2026</option>
                                        <option value="TE-2008">#TE-2008 — Hi-Vis Safety Vest × 50 — 28 Mar 2026</option>
                                        <option value="TE-1994">#TE-1994 — School PE Kit Set × 60 — 10 Mar 2026</option>
                                        <option value="TE-1982">#TE-1982 — Embroidered Cap × 40 — 05 Mar 2026</option>
                                    </select>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking result — shown after selection -->
                    <div class="cp-track-result" id="cpTrackResult" style="display:none;">

                        <!-- Order summary bar -->
                        <div class="cp-track-order-bar">
                            <div class="cp-track-order-info">
                                <span class="cp-track-order-num" id="cpTrackOrderNum"></span>
                                <span class="cp-track-order-desc" id="cpTrackOrderDesc"></span>
                            </div>
                            <span class="cp-status" id="cpTrackStatusBadge"></span>
                        </div>

                        <!-- Timeline -->
                        <div class="cp-section-block">
                            <h3 class="cp-block-title" style="margin-bottom:28px;">Order Timeline</h3>
                            <div class="cp-timeline" id="cpTimeline">
                                <!-- Populated by JS -->
                            </div>
                        </div>

                        <!-- Estimated delivery -->
                        <div class="cp-track-eta" id="cpTrackEta">
                            <i class="bi bi-truck"></i>
                            <div>
                                <span class="cp-eta-label">Estimated Delivery</span>
                                <strong class="cp-eta-date" id="cpEtaDate"></strong>
                            </div>
                        </div>

                    </div>

                    <!-- Empty state -->
                    <div class="cp-track-empty" id="cpTrackEmpty">
                        <i class="bi bi-geo-alt"></i>
                        <p>Select an order number above to see its tracking history.</p>
                    </div>

                </div>
                <!-- /track -->


                <!-- ════════════════════════════════════════
                     TAB 4: UPDATE PROFILE
                ════════════════════════════════════════ -->
                <div class="cp-tab-panel" id="cp-tab-profile">

                    <div class="cp-panel-header">
                        <h2 class="cp-panel-title">Update Profile</h2>
                        <span class="cp-panel-date">Keep your details up to date</span>
                    </div>

                    <div class="cp-profile-layout">

                        <!-- Left: Avatar upload -->
                        <div class="cp-avatar-col">
                            <div class="cp-profile-avatar-wrap">
                                <img src="{{ asset('assets/images/about1.jpg') }}" alt="Profile Picture" class="cp-profile-avatar" id="cpProfileAvatarImg">
                                <label for="cpAvatarInput" class="cp-avatar-upload-btn" title="Change photo">
                                    <i class="bi bi-camera"></i>
                                </label>
                                <input type="file" id="cpAvatarInput" accept="image/*" style="display:none;">
                            </div>
                            <p class="cp-avatar-hint">JPG, PNG or GIF · Max 2MB</p>

                            <div class="cp-profile-meta">
                                <span class="cp-profile-meta-label">Member Since</span>
                                <span class="cp-profile-meta-val">March 2024</span>
                            </div>
                            <div class="cp-profile-meta">
                                <span class="cp-profile-meta-label">Account Type</span>
                                <span class="cp-profile-meta-val">Business</span>
                            </div>
                            <div class="cp-profile-meta">
                                <span class="cp-profile-meta-label">Total Orders</span>
                                <span class="cp-profile-meta-val">24</span>
                            </div>
                        </div>

                        <!-- Right: Form -->
                        <div class="cp-profile-form-col">
                            <div class="cp-form-section-title">Personal Information</div>

                            <div class="cp-form-row cp-form-row--2">
                                <div class="cp-form-group">
                                    <label class="cp-form-label" for="cpFirstName">First Name</label>
                                    <input type="text" id="cpFirstName" class="cp-form-input" value="James" placeholder="First name">
                                </div>
                                <div class="cp-form-group">
                                    <label class="cp-form-label" for="cpLastName">Last Name</label>
                                    <input type="text" id="cpLastName" class="cp-form-input" value="Thornton" placeholder="Last name">
                                </div>
                            </div>

                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpEmail">Email Address</label>
                                <div class="cp-input-icon-wrap">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" id="cpEmail" class="cp-form-input cp-input-has-icon" value="james@thorntonservices.co.uk" placeholder="your@email.com">
                                </div>
                            </div>

                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpPhone">Phone Number</label>
                                <div class="cp-input-icon-wrap">
                                    <i class="bi bi-telephone"></i>
                                    <input type="tel" id="cpPhone" class="cp-form-input cp-input-has-icon" value="+44 7916 916369" placeholder="+44 0000 000000">
                                </div>
                            </div>

                            <div class="cp-form-divider"></div>
                            <div class="cp-form-section-title">Address</div>

                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpAddress1">Address Line 1</label>
                                <input type="text" id="cpAddress1" class="cp-form-input" value="14 High Street" placeholder="Street address">
                            </div>

                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpAddress2">Address Line 2 <span class="cp-form-optional">optional</span></label>
                                <input type="text" id="cpAddress2" class="cp-form-input" value="" placeholder="Apartment, suite, etc.">
                            </div>

                            <div class="cp-form-row cp-form-row--3">
                                <div class="cp-form-group">
                                    <label class="cp-form-label" for="cpCity">City</label>
                                    <input type="text" id="cpCity" class="cp-form-input" value="Cullompton" placeholder="City">
                                </div>
                                <div class="cp-form-group">
                                    <label class="cp-form-label" for="cpPostcode">Postcode</label>
                                    <input type="text" id="cpPostcode" class="cp-form-input" value="EX15 1AB" placeholder="EX00 0AB">
                                </div>
                                <div class="cp-form-group">
                                    <label class="cp-form-label" for="cpCountry">Country</label>
                                    <div class="cp-select-wrap">
                                        <select id="cpCountry" class="cp-form-input cp-form-select">
                                            <option selected>United Kingdom</option>
                                            <option>Ireland</option>
                                            <option>Other</option>
                                        </select>
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="cp-form-divider"></div>
                            <div class="cp-form-section-title">Change Password <span class="cp-form-optional">leave blank to keep current</span></div>

                            <div class="cp-form-row cp-form-row--2">
                                <div class="cp-form-group">
                                    <label class="cp-form-label" for="cpPassNew">New Password</label>
                                    <div class="cp-input-icon-wrap">
                                        <i class="bi bi-lock"></i>
                                        <input type="password" id="cpPassNew" class="cp-form-input cp-input-has-icon" placeholder="New password">
                                        <button type="button" class="cp-pass-toggle" data-target="cpPassNew" aria-label="Show password"><i class="bi bi-eye"></i></button>
                                    </div>
                                </div>
                                <div class="cp-form-group">
                                    <label class="cp-form-label" for="cpPassConfirm">Confirm Password</label>
                                    <div class="cp-input-icon-wrap">
                                        <i class="bi bi-lock"></i>
                                        <input type="password" id="cpPassConfirm" class="cp-form-input cp-input-has-icon" placeholder="Confirm password">
                                        <button type="button" class="cp-pass-toggle" data-target="cpPassConfirm" aria-label="Show password"><i class="bi bi-eye"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Save button -->
                            <div class="cp-form-actions">
                                <button type="button" class="cp-save-btn" id="cpSaveBtn">
                                    <i class="bi bi-check2-circle"></i>
                                    Save Changes
                                </button>
                                <span class="cp-save-feedback" id="cpSaveFeedback"></span>
                            </div>

                        </div>
                    </div>

                </div>
                <!-- /profile -->


            </main>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/client-portal.js') }}"></script>
@endpush
