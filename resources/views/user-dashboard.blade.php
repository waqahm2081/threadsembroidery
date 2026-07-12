@extends('layouts.app')

@section('title', 'Client Portal — Threads Embroidery &amp; Print Ltd')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/client-portal.css') }}?v=3.1">
<link rel="stylesheet" href="{{ asset('assets/css/cart-checkout.css') }}?v=1.3">
@endpush

@section('content')

@php
    $spentWhole = floor($stats['total_spent']);
    $spentPence = str_pad((string) round(($stats['total_spent'] - $spentWhole) * 100), 2, '0', STR_PAD_LEFT);
@endphp

<!-- PAGE HEADER -->
<div id="cp-page-header">
    <div class="cp-page-header-glow"></div>
    <div class="cp-page-header-inner">
        <div class="cp-page-header-left">
            <div class="cp-eyebrow">
                <span class="cp-eyebrow-dot"></span>
                Client Portal
            </div>
            <h1 class="cp-page-title">Welcome back, <span class="cp-name-accent">{{ $firstName }}</span></h1>
            <p class="cp-page-sub">Your orders, tracking, and account — all in one place.</p>
        </div>
        <div class="cp-page-header-right">
            <div class="cp-header-card">
                <div class="cp-avatar-wrap">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="cp-avatar" id="cpAvatarPreview">
                    <span class="cp-avatar-status"></span>
                </div>
                <div class="cp-header-user">
                    <span class="cp-header-name">{{ $user->name }}</span>
                    <span class="cp-header-email">{{ $user->email }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PORTAL BODY -->
<div id="cp-portal">
    <div class="cp-shell">

        <div class="cp-toolbar" id="cp-toolbar">
            <nav class="cp-topnav" aria-label="Portal sections">
                <button class="cp-nav-item active" data-tab="summary">
                    <span class="cp-nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                    <span class="cp-nav-text">Overview</span>
                </button>
                <button class="cp-nav-item" data-tab="orders">
                    <span class="cp-nav-icon"><i class="bi bi-bag-check"></i></span>
                    <span class="cp-nav-text">Orders</span>
                    @if($stats['total_orders'] > 0)
                        <span class="cp-nav-badge">{{ $stats['total_orders'] }}</span>
                    @endif
                </button>
                <button class="cp-nav-item" data-tab="track">
                    <span class="cp-nav-icon"><i class="bi bi-geo-alt-fill"></i></span>
                    <span class="cp-nav-text">Track</span>
                </button>
                <button class="cp-nav-item" data-tab="branding">
                    <span class="cp-nav-icon"><i class="bi bi-palette-fill"></i></span>
                    <span class="cp-nav-text">Branding</span>
                    @if($brandingEnquiries->isNotEmpty())
                        <span class="cp-nav-badge cp-nav-badge--muted">{{ $brandingEnquiries->count() }}</span>
                    @endif
                </button>
                <button class="cp-nav-item" data-tab="profile">
                    <span class="cp-nav-icon"><i class="bi bi-person-fill"></i></span>
                    <span class="cp-nav-text">Profile</span>
                </button>
            </nav>

            <div class="cp-toolbar-actions">
                <a href="{{ route('contact') }}" class="cp-toolbar-link">
                    <i class="bi bi-headset"></i>
                    <span>Help</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="cp-toolbar-logout-form">
                    @csrf
                    <button type="submit" class="cp-toolbar-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>

        <main class="cp-main">

            <!-- TAB: SUMMARY -->
            <div class="cp-tab-panel active" id="cp-tab-summary">
                <div class="cp-panel-header">
                    <div>
                        <h2 class="cp-panel-title">Account Overview</h2>
                        <p class="cp-panel-desc">A snapshot of your account activity</p>
                    </div>
                    <span class="cp-panel-date">Updated <strong>{{ now()->format('d M Y') }}</strong></span>
                </div>

                <div class="cp-stats-grid">
                    <div class="cp-stat-card">
                        <div class="cp-stat-icon cp-stat-icon--total"><i class="bi bi-bag-check"></i></div>
                        <div class="cp-stat-body">
                            <span class="cp-stat-label">Total Orders</span>
                            <strong class="cp-stat-value">{{ $stats['total_orders'] }}</strong>
                        </div>
                    </div>
                    <div class="cp-stat-card">
                        <div class="cp-stat-icon cp-stat-icon--spend"><i class="bi bi-currency-pound"></i></div>
                        <div class="cp-stat-body">
                            <span class="cp-stat-label">Total Spent</span>
                            <strong class="cp-stat-value">£{{ number_format($spentWhole) }}<small>.{{ $spentPence }}</small></strong>
                        </div>
                    </div>
                    <div class="cp-stat-card">
                        <div class="cp-stat-icon cp-stat-icon--active"><i class="bi bi-arrow-repeat"></i></div>
                        <div class="cp-stat-body">
                            <span class="cp-stat-label">Active Orders</span>
                            <strong class="cp-stat-value">{{ $stats['active_orders'] }}</strong>
                        </div>
                    </div>
                    <div class="cp-stat-card">
                        <div class="cp-stat-icon cp-stat-icon--done"><i class="bi bi-patch-check-fill"></i></div>
                        <div class="cp-stat-body">
                            <span class="cp-stat-label">Completed</span>
                            <strong class="cp-stat-value">{{ $stats['completed'] }}</strong>
                        </div>
                    </div>
                </div>

                <div class="cp-section-block">
                    <div class="cp-block-header">
                        <h3 class="cp-block-title">Recent Orders</h3>
                        @if($orders->isNotEmpty())
                            <button class="cp-block-link" data-tab-link="orders">View All <i class="bi bi-arrow-right"></i></button>
                        @endif
                    </div>
                    <div class="cp-table-wrap">
                        <table class="cp-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td><span class="cp-order-num">#{{ $order->order_number }}</span></td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="cp-cell-price">£{{ number_format($order->total, 2) }}</td>
                                        <td><span class="cp-status cp-status--{{ $order->filterStatus() }}">{{ $order->displayStatus() }}</span></td>
                                        <td>
                                            <button type="button" class="cp-btn-view" data-order="{{ $order->order_number }}">View</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="cp-table-empty">No orders yet. <a href="{{ route('shop') }}">Browse our shop</a> to get started.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="cp-quick-actions">
                    <a href="{{ route('shop') }}" class="cp-quick-card">
                        <div class="cp-quick-icon"><i class="bi bi-magic"></i></div>
                        <div class="cp-quick-text">
                            <strong>New Custom Order</strong>
                            <span>Design &amp; order online</span>
                        </div>
                    </a>
                    <button type="button" class="cp-quick-card" data-tab-link="orders">
                        <div class="cp-quick-icon"><i class="bi bi-files"></i></div>
                        <div class="cp-quick-text">
                            <strong>Reorder</strong>
                            <span>From order history</span>
                        </div>
                    </button>
                    <a href="{{ route('contact') }}" class="cp-quick-card">
                        <div class="cp-quick-icon"><i class="bi bi-headset"></i></div>
                        <div class="cp-quick-text">
                            <strong>Contact Support</strong>
                            <span>We're here to help</span>
                        </div>
                    </a>
                    <a href="{{ route('branding-enquiry') }}" class="cp-quick-card">
                        <div class="cp-quick-icon"><i class="bi bi-palette"></i></div>
                        <div class="cp-quick-text">
                            <strong>Branding Enquiry</strong>
                            <span>Brand your own garments</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- TAB: ORDERS -->
            <div class="cp-tab-panel" id="cp-tab-orders">
                <div class="cp-panel-header">
                    <div>
                        <h2 class="cp-panel-title">Order History</h2>
                        <p class="cp-panel-desc">{{ $stats['total_orders'] }} {{ Str::plural('order', $stats['total_orders']) }} placed with us</p>
                    </div>
                </div>

                @if($orders->isNotEmpty())
                <div class="cp-filters">
                    <div class="cp-filter-group">
                        <button class="cp-filter-btn active" data-filter="all">All</button>
                        <button class="cp-filter-btn" data-filter="processing">Processing</button>
                        <button class="cp-filter-btn" data-filter="shipped">Shipped</button>
                        <button class="cp-filter-btn" data-filter="delivered">Delivered</button>
                    </div>
                    <div class="cp-search-wrap">
                        <i class="bi bi-search"></i>
                            <input type="text" class="cp-search-input" id="cpOrderSearch" placeholder="Search by order number…">
                    </div>
                </div>
                @endif

                <div class="cp-section-block cp-section-block--flush">
                    <div class="cp-table-wrap">
                        <table class="cp-table" id="cpOrdersTable">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr data-status="{{ $order->filterStatus() }}">
                                        <td><span class="cp-order-num">#{{ $order->order_number }}</span></td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td class="cp-cell-price">£{{ number_format($order->total, 2) }}</td>
                                        <td><span class="cp-status cp-status--{{ $order->filterStatus() }}">{{ $order->displayStatus() }}</span></td>
                                        <td>
                                            <div class="cp-row-actions">
                                                <button type="button" class="cp-btn-view" data-order="{{ $order->order_number }}">View Order</button>
                                                <button type="button" class="cp-btn-track" data-order="{{ $order->order_number }}">Track</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="cp-table-empty">You haven't placed any orders yet. <a href="{{ route('shop') }}">Start shopping</a></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB: TRACK -->
            <div class="cp-tab-panel" id="cp-tab-track">
                <div class="cp-panel-header">
                    <div>
                        <h2 class="cp-panel-title">Track Your Order</h2>
                        <p class="cp-panel-desc">Follow your order from production to delivery</p>
                    </div>
                </div>

                @if($orders->isNotEmpty())
                <div class="cp-section-block">
                    <div class="cp-track-select-wrap">
                        <label class="cp-track-label" for="cpTrackSelect">Select an order</label>
                        <div class="cp-custom-select-wrap">
                            <select class="cp-custom-select" id="cpTrackSelect">
                                <option value="">Choose an order number…</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->order_number }}">
                                        #{{ $order->order_number }} — {{ $trackingService->productsSummary($order) }} — {{ $order->created_at->format('d M Y') }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <div class="cp-track-result" id="cpTrackResult" style="display:none;">
                    <div class="cp-track-order-bar">
                        <div class="cp-track-order-info">
                            <span class="cp-track-order-num" id="cpTrackOrderNum"></span>
                            <span class="cp-track-order-desc" id="cpTrackOrderDesc"></span>
                        </div>
                        <span class="cp-status" id="cpTrackStatusBadge"></span>
                    </div>

                    <div class="cp-section-block">
                        <h3 class="cp-block-title">Order Timeline</h3>
                        <div class="cp-timeline" id="cpTimeline"></div>
                    </div>

                    <div class="cp-track-eta" id="cpTrackEta">
                        <i class="bi bi-truck"></i>
                        <div>
                            <span class="cp-eta-label">Estimated Delivery</span>
                            <strong class="cp-eta-date" id="cpEtaDate"></strong>
                        </div>
                    </div>
                </div>
                @endif

                <div class="cp-track-empty" id="cpTrackEmpty" @if($orders->isNotEmpty()) style="display:flex;" @endif>
                    <div class="cp-empty-icon"><i class="bi bi-geo-alt"></i></div>
                    <p>
                        @if($orders->isEmpty())
                            You don't have any orders to track yet.
                        @else
                            Select an order above to view its tracking timeline.
                        @endif
                    </p>
                </div>
            </div>

            <!-- TAB: BRANDING ENQUIRIES -->
            <div class="cp-tab-panel" id="cp-tab-branding">
                <div class="cp-panel-header">
                    <div>
                        <h2 class="cp-panel-title">Branding Enquiry Requests</h2>
                        <p class="cp-panel-desc">Track enquiries for branding your own garments</p>
                    </div>
                    <a href="{{ route('branding-enquiry') }}" class="cp-btn-primary-sm">
                        <i class="bi bi-plus-lg"></i> New Enquiry
                    </a>
                </div>

                <div class="cp-branding-banner">
                    <i class="bi bi-info-circle"></i>
                    <p>Already have garments? Send them to us for professional embroidery or print. Submit an enquiry below and track its status here.</p>
                </div>

                <div class="cp-section-block cp-section-block--flush">
                    <div class="cp-table-wrap">
                        <table class="cp-table" id="cpBrandingTable">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Garments</th>
                                    <th>Service</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brandingEnquiries as $enquiry)
                                    <tr>
                                        <td><span class="cp-order-num">{{ $enquiry->reference }}</span></td>
                                        <td>{{ $enquiry->garmentsSummary() }}</td>
                                        <td>{{ $enquiry->serviceSummary() }}</td>
                                        <td>{{ $enquiry->created_at->format('d M Y') }}</td>
                                        <td><span class="cp-status cp-status--{{ $enquiry->filterStatus() }}">{{ $enquiry->displayStatus() }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="cp-table-empty">No branding enquiries yet. <a href="{{ route('branding-enquiry') }}">Submit your first enquiry</a></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="cp-branding-cta">
                    <div class="cp-branding-cta-content">
                        <h3>Have garments ready to brand?</h3>
                        <p>Submit a branding enquiry and our team will get back to you with a quote within 1–2 working days.</p>
                    </div>
                    <a href="{{ route('branding-enquiry') }}" class="cp-btn-primary">Submit Enquiry <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <!-- TAB: PROFILE -->
            <div class="cp-tab-panel" id="cp-tab-profile">
                <div class="cp-panel-header">
                    <div>
                        <h2 class="cp-panel-title">Your Profile</h2>
                        <p class="cp-panel-desc">Manage your personal details and preferences</p>
                    </div>
                </div>

                <div class="cp-profile-layout">
                    <div class="cp-avatar-col">
                        <div class="cp-profile-avatar-wrap">
                            <img src="{{ $user->avatar_url }}" alt="Profile Picture" class="cp-profile-avatar" id="cpProfileAvatarImg">
                            <label for="cpAvatarInput" class="cp-avatar-upload-btn" title="Change photo">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" id="cpAvatarInput" accept="image/*" style="display:none;">
                        </div>
                        <p class="cp-avatar-hint">JPG, PNG or GIF · Max 2MB</p>

                        <div class="cp-profile-meta-list">
                            <div class="cp-profile-meta">
                                <span class="cp-profile-meta-label">Member Since</span>
                                <span class="cp-profile-meta-val">{{ $user->created_at->format('F Y') }}</span>
                            </div>
                            <div class="cp-profile-meta">
                                <span class="cp-profile-meta-label">Account Type</span>
                                <span class="cp-profile-meta-val">{{ $user->account_type_label }}</span>
                            </div>
                            <div class="cp-profile-meta">
                                <span class="cp-profile-meta-label">Total Orders</span>
                                <span class="cp-profile-meta-val" id="cpProfileOrderCount">{{ $stats['total_orders'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="cp-profile-form-col">
                        <div class="cp-form-section-title">Personal Information</div>

                        <div class="cp-form-row cp-form-row--2">
                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpFirstName">First Name</label>
                                <input type="text" id="cpFirstName" class="cp-form-input" value="{{ $firstName }}" placeholder="First name">
                            </div>
                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpLastName">Last Name</label>
                                <input type="text" id="cpLastName" class="cp-form-input" value="{{ $lastName }}" placeholder="Last name">
                            </div>
                        </div>

                        <div class="cp-form-group">
                            <label class="cp-form-label" for="cpEmail">Email Address</label>
                            <div class="cp-input-icon-wrap">
                                <i class="bi bi-envelope"></i>
                                <input type="email" id="cpEmail" class="cp-form-input cp-input-has-icon" value="{{ $user->email }}" placeholder="your@email.com">
                            </div>
                        </div>

                        <div class="cp-form-group">
                            <label class="cp-form-label" for="cpPhone">Phone Number</label>
                            <div class="cp-input-icon-wrap">
                                <i class="bi bi-telephone"></i>
                                <input type="tel" id="cpPhone" class="cp-form-input cp-input-has-icon" value="{{ $user->phone }}" placeholder="+44 0000 000000">
                            </div>
                        </div>

                        <div class="cp-form-divider"></div>
                        <div class="cp-form-section-title">Address</div>

                        <div class="cp-form-group">
                            <label class="cp-form-label" for="cpAddress1">Address Line 1</label>
                            <input type="text" id="cpAddress1" class="cp-form-input" value="{{ $user->address_line1 }}" placeholder="Street address">
                        </div>

                        <div class="cp-form-group">
                            <label class="cp-form-label" for="cpAddress2">Address Line 2 <span class="cp-form-optional">optional</span></label>
                            <input type="text" id="cpAddress2" class="cp-form-input" value="{{ $user->address_line2 }}" placeholder="Apartment, suite, etc.">
                        </div>

                        <div class="cp-form-row cp-form-row--3">
                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpCity">City</label>
                                <input type="text" id="cpCity" class="cp-form-input" value="{{ $user->city }}" placeholder="City">
                            </div>
                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpPostcode">Postcode</label>
                                <input type="text" id="cpPostcode" class="cp-form-input" value="{{ $user->postcode }}" placeholder="EX00 0AB">
                            </div>
                            <div class="cp-form-group">
                                <label class="cp-form-label" for="cpCountry">Country</label>
                                <div class="cp-select-wrap">
                                    <select id="cpCountry" class="cp-form-input cp-form-select">
                                        @php $countries = ['United Kingdom', 'Ireland', 'Other']; @endphp
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" @selected(($user->country ?: 'United Kingdom') === $country)>{{ $country }}</option>
                                        @endforeach
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

        </main>
    </div><!-- /.cp-shell -->
</div>

<!-- ORDER DETAIL MODAL -->
<div class="cp-modal-overlay" id="cpOrderModal" aria-hidden="true">
    <div class="cp-modal" role="dialog" aria-labelledby="cpModalTitle" aria-modal="true">
        <div class="cp-modal-header">
            <div class="cp-modal-header-left">
                <span class="cp-modal-eyebrow">Order Details</span>
                <h2 class="cp-modal-title" id="cpModalTitle">#—</h2>
            </div>
            <div class="cp-modal-header-right">
                <span class="cp-status" id="cpModalStatus"></span>
                <button type="button" class="cp-modal-close" id="cpModalClose" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <div class="cp-modal-body" id="cpModalBody">
            <!-- Populated by JS -->
        </div>

        <div class="cp-modal-footer">
            <button type="button" class="cp-btn-track" id="cpModalTrackBtn">
                <i class="bi bi-geo-alt"></i> Track This Order
            </button>
            <button type="button" class="cp-btn-secondary" id="cpModalCloseBtn">Close</button>
        </div>
    </div>
</div>

<!-- DESIGN PREVIEW MODAL -->
<div class="cp-modal-overlay cp-design-overlay" id="cpDesignModalOverlay" aria-hidden="true">
    <div class="cp-design-modal" role="dialog" aria-labelledby="cpDesignModalLabel" aria-modal="true">
        <div class="cdm-header">
            <div>
                <h5 class="cdm-title" id="cpDesignModalLabel"><i class="bi bi-palette"></i> Your Design</h5>
                <p class="cdm-sub" id="cpDesignSubtitle"></p>
            </div>
            <button type="button" class="cdm-close" id="cpDesignModalClose" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="cdm-body">
            <div class="cdm-preview-wrap">
                <div class="cdm-preview-canvas" id="cpDesignPreviewCanvas">
                    <img class="cdm-garment-img" id="cpDesignGarmentImg" src="" alt="Garment preview">
                    <div class="cdm-layers" id="cpDesignLayers"></div>
                </div>
            </div>
            <div class="cdm-details" id="cpDesignDetails"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
window.__CP_TRACKING_DATA__ = @json($trackingData);
window.__CP_ORDER_DATA__ = @json($orderDetailsData);
window.__CP_PROFILE_URL__ = @json(route('user-dashboard.profile'));
</script>
<script src="{{ asset('assets/js/design-print-area.js') }}"></script>
<script src="{{ asset('assets/js/client-portal.js') }}?v=3.2"></script>
@endpush
