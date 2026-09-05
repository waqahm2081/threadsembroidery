@php
    $requestedColour = request('colour');
    $activeColour = collect($product['colours'])->firstWhere('code', $requestedColour)
        ?? ($product['colours'][0] ?? null);
    $garmentFront = $activeColour['image_front']
        ?? $product['customizer_image']
        ?? asset('assets/images/customizer/tshirtcustom.png');
    $garmentBack = $activeColour['image_back']
        ?? asset('assets/images/customizer/tshirtcustom-back.png');
    $productUrl = route('shop.product', ['categorySlug' => $category['slug'], 'productSlug' => $product['slug']]);
    $subtitle = collect([$product['brand'], $product['material'], $product['type'], $product['gender']])->filter()->implode(' · ');
    $priceWhole = floor($product['min_price']);
    $priceDecimal = str_pad((string) round(($product['min_price'] - $priceWhole) * 100), 2, '0', STR_PAD_LEFT);
    $sizeBreakdown = $cartItem?->size_breakdown ?? null;
    $defaultSize = request('size', $activeColour['sizes'][0]['size'] ?? 'M');
    $defaultQty = max(1, (int) request('qty', $cartItem?->quantity ?? 1));
    $sizeDisplay = $sizeBreakdown
        ? collect($sizeBreakdown)->map(fn ($r) => $r['size'].' × '.$r['qty'])->implode(', ')
        : $defaultSize;
@endphp

@extends('layouts.customizer')

@section('title', 'Customize ' . $product['title'] . ' — Threads Embroidery & Print Ltd')

@section('body_class')
pc-body
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shop-theme.css') }}?v=1.1">
<link rel="stylesheet" href="{{ asset('assets/css/product-customizer.css') }}?v=1.5">
@endpush

@section('content')
    <div id="pc-app">

        <header id="pc-topbar">
            <div class="pct-left">
                <a href="{{ route('home') }}" class="pct-logo">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Threads Embroidery &amp; Print" class="pct-logo-img">
                    <div class="pct-logo-fallback" style="display:none;">
                        <span class="pct-logo-fallback-icon"><i class="bi bi-stars"></i></span>
                        <span class="pct-logo-fallback-text">Threads<span>Embroidery &amp; Print</span></span>
                    </div>
                </a>
                <a href="{{ $productUrl }}" class="pct-back-btn">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back to Product</span>
                </a>
            </div>

            <div class="pct-center">
                <span class="pct-product-meta"><i class="bi bi-rulers"></i> Sizes: <strong id="pctSize">{{ $sizeDisplay }}</strong></span>
                <span class="pct-product-meta"><i class="bi bi-stack"></i> Qty: <strong id="pctQty">{{ $defaultQty }}</strong></span>
            </div>

            <div class="pct-right">
                <div class="pct-price-box">
                    <span class="pct-price-label">Unit Price</span>
                    <span class="pct-price-value" id="pctPrice">£{{ $priceWhole }}.{{ $priceDecimal }} + VAT</span>
                </div>
                <button class="pct-cart-btn" id="pcAddToCartBtn">
                    <i class="bi bi-cart-plus"></i>
                    <span>Add to Cart</span>
                </button>
            </div>
        </header>

        <main id="pc-workspace">

            <aside id="pc-left">

                <div class="pcl-section pcl-product">
                    <div class="pcl-product-thumb">
                        <img src="{{ $garmentFront }}" alt="{{ $product['title'] }}" id="pclProductThumb">
                    </div>
                    <div class="pcl-product-info">
                        <h2 class="pcl-product-title" id="pclProductTitle">{{ $product['title'] }}</h2>
                        <p class="pcl-product-sub" id="pclProductSub">{{ $subtitle ?: $category['name'] }}</p>
                    </div>
                </div>

                <div class="pcl-divider"></div>

                <div class="pcl-accordion" id="pcAccordion">

                    <div class="pcl-acc-item">
                        <button class="pcl-acc-header active" type="button" data-acc-target="pcAccColour">
                            <span class="pcl-acc-header-label"><i class="bi bi-palette2"></i> Garment Colour</span>
                            <span class="pcl-acc-header-right">
                                <span class="pcl-selected-colour-name" id="pcColourName">{{ $activeColour['name'] ?? 'Select colour' }}</span>
                                <i class="bi bi-chevron-down pcl-acc-chevron"></i>
                            </span>
                        </button>
                        <div class="pcl-acc-body active" id="pcAccColour">
                            <div class="pcl-acc-body-inner">
                                <div class="pcl-swatches" id="pcSwatches">
                                    @foreach($product['colours'] as $index => $colour)
                                        <button class="pcl-swatch {{ ($activeColour['code'] ?? null) === $colour['code'] ? 'active' : '' }}"
                                                data-colour="{{ $colour['name'] }}"
                                                data-code="{{ $colour['code'] }}"
                                                data-hex="{{ $colour['hex'] }}"
                                                style="background:{{ $colour['hex'] }}; {{ in_array(strtolower($colour['hex']), ['#ffffff', '#fff', '#f0f0f0']) ? 'border-color:rgba(0,0,0,0.15);' : '' }}"
                                                aria-label="{{ $colour['name'] }}"
                                                title="{{ $colour['name'] }}"></button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pcl-acc-item">
                        <button class="pcl-acc-header" type="button" data-acc-target="pcAccUpload">
                            <span class="pcl-acc-header-label"><i class="bi bi-cloud-arrow-up"></i> Upload Images</span>
                            <span class="pcl-acc-header-right">
                                <i class="bi bi-chevron-down pcl-acc-chevron"></i>
                            </span>
                        </button>
                        <div class="pcl-acc-body" id="pcAccUpload">
                            <div class="pcl-acc-body-inner">
                                <div class="pcl-upload-zone" id="pcUploadZone">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <p class="pcl-upload-title">Drop your logo or artwork here</p>
                                    <p class="pcl-upload-sub">PNG, JPG or SVG — up to 10MB</p>
                                    <label class="pcl-upload-btn" for="pcFileInput">Browse Files</label>
                                    <input type="file" id="pcFileInput" accept="image/png, image/jpeg, image/svg+xml" hidden multiple>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pcl-acc-item">
                        <button class="pcl-acc-header" type="button" data-acc-target="pcAccText">
                            <span class="pcl-acc-header-label"><i class="bi bi-fonts"></i> Add Text</span>
                            <span class="pcl-acc-header-right">
                                <i class="bi bi-chevron-down pcl-acc-chevron"></i>
                            </span>
                        </button>
                        <div class="pcl-acc-body" id="pcAccText">
                            <div class="pcl-acc-body-inner">
                                <div class="pcl-field">
                                    <label class="pcl-label" for="pcTextInput">Your Text</label>
                                    <input type="text" id="pcTextInput" class="pcl-input" placeholder="e.g. Team Threads" maxlength="30">
                                </div>

                                <div class="pcl-field-row">
                                    <div class="pcl-field">
                                        <label class="pcl-label" for="pcFontSelect">Font</label>
                                        <select id="pcFontSelect" class="pcl-select">
                                            <option value="'Manrope', sans-serif" selected>Manrope</option>
                                            <option value="'Inter', sans-serif">Inter</option>
                                            <option value="'Georgia', serif">Serif Classic</option>
                                            <option value="'Courier New', monospace">Monospace</option>
                                            <option value="'Brush Script MT', cursive">Script</option>
                                        </select>
                                    </div>
                                    <div class="pcl-field">
                                        <label class="pcl-label" for="pcFontSizeRange">Size: <span id="pcFontSizeVal">28</span>px</label>
                                        <input type="range" id="pcFontSizeRange" class="pcl-range" min="12" max="80" value="28">
                                    </div>
                                </div>

                                <div class="pcl-field">
                                    <label class="pcl-label">Text Colour</label>
                                    <div class="pcl-text-colour-row" id="pcTextColourRow">
                                        <button class="pcl-text-colour active" data-hex="#ffffff" style="background:#ffffff;" aria-label="White"></button>
                                        <button class="pcl-text-colour" data-hex="#000000" style="background:#000000;" aria-label="Black"></button>
                                        <button class="pcl-text-colour" data-hex="#ee6023" style="background:#ee6023;" aria-label="Orange"></button>
                                        <button class="pcl-text-colour" data-hex="#c0392b" style="background:#c0392b;" aria-label="Red"></button>
                                        <button class="pcl-text-colour" data-hex="#2471a3" style="background:#2471a3;" aria-label="Royal Blue"></button>
                                        <button class="pcl-text-colour" data-hex="#f1c40f" style="background:#f1c40f;" aria-label="Yellow"></button>
                                        <input type="color" id="pcTextColourCustom" class="pcl-text-colour-custom" value="#ffffff" aria-label="Custom colour">
                                    </div>
                                </div>

                                <div class="pcl-field-row">
                                    <div class="pcl-field">
                                        <label class="pcl-label" for="pcFontWeightSelect">Style</label>
                                        <select id="pcFontWeightSelect" class="pcl-select">
                                            <option value="400">Regular</option>
                                            <option value="700" selected>Bold</option>
                                            <option value="800">Extra Bold</option>
                                            <option value="italic">Italic</option>
                                        </select>
                                    </div>
                                    <div class="pcl-field">
                                        <label class="pcl-label" for="pcLetterSpacingRange">Letter Spacing</label>
                                        <input type="range" id="pcLetterSpacingRange" class="pcl-range" min="-2" max="12" value="0">
                                    </div>
                                </div>

                                <button class="pcl-add-btn" id="pcAddTextBtn">
                                    <i class="bi bi-plus-circle"></i> Add Text to Design
                                </button>

                                <p class="pcl-tool-hint">
                                    <i class="bi bi-arrows-angle-expand"></i>
                                    Each click adds a new text layer — drag to position, use the corner handle to resize. Select a text layer to edit its style.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pcl-acc-item">
                        <button class="pcl-acc-header" type="button" data-acc-target="pcAccLayers">
                            <span class="pcl-acc-header-label"><i class="bi bi-layers"></i> Layers</span>
                            <span class="pcl-acc-header-right">
                                <span class="pcl-acc-badge" id="pcLayersBadge">0</span>
                                <i class="bi bi-chevron-down pcl-acc-chevron"></i>
                            </span>
                        </button>
                        <div class="pcl-acc-body" id="pcAccLayers">
                            <div class="pcl-acc-body-inner">
                                <p class="pcl-layers-empty" id="pcLayersEmpty">
                                    <i class="bi bi-layers"></i>
                                    No design elements yet. Upload a logo or add text to get started.
                                </p>
                                <ul class="pcl-layers-list" id="pcLayersList"></ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pcl-divider"></div>

                <div class="pcl-section">
                    <div class="pcl-summary">
                        <div class="pcl-summary-row">
                            <span>Garment</span>
                            <strong id="pcSummaryGarment">{{ $product['type'] ?: $product['title'] }}</strong>
                        </div>
                        <div class="pcl-summary-row">
                            <span>Colour</span>
                            <strong id="pcSummaryColour">{{ $activeColour['name'] ?? '—' }}</strong>
                        </div>
                        <div class="pcl-summary-row">
                            <span>Decoration</span>
                            <strong id="pcSummaryPlacement">—</strong>
                        </div>
                        <div class="pcl-summary-row">
                            <span>Design Elements</span>
                            <strong id="pcSummaryElements">0</strong>
                        </div>
                        <div class="pcl-summary-divider"></div>
                        <div class="pcl-summary-row pcl-summary-total">
                            <span>Estimated Total <small class="pc-vat-note">(ex. VAT)</small></span>
                            <strong id="pcSummaryTotal">£{{ $priceWhole }}.{{ $priceDecimal }} <small class="pc-vat-note">+ VAT</small></strong>
                        </div>
                    </div>
                </div>

            </aside>

            <section id="pc-canvas-area">

                <div class="pcc-toolbar">
                    <div class="pcc-view-toggle" id="pcViewToggleTop">
                        <button class="pcc-view-btn active" data-view="front"><i class="bi bi-person-standing" aria-hidden="true"></i> <span>Front</span></button>
                        <button class="pcc-view-btn" data-view="back"><i class="bi bi-person-standing pcc-view-icon--back" aria-hidden="true"></i> <span>Back</span></button>
                    </div>
                    <div class="pcc-toolbar-actions">
                        <button class="pcc-tool-btn" id="pcUndoBtn" title="Remove selected element"><i class="bi bi-trash3"></i></button>
                        <button class="pcc-tool-btn" id="pcResetBtn" title="Clear all design elements"><i class="bi bi-arrow-counterclockwise"></i></button>
                    </div>
                </div>

                <div class="pcc-canvas-wrap">
                    <div class="pcc-canvas" id="pcCanvas">
                        <div class="pcc-garment-render" id="pcGarmentRender">
                            <img class="pcc-garment-img" id="pcGarmentImg"
                                 src="{{ $garmentFront }}"
                                 alt="{{ $product['title'] }} preview">
                        </div>
                        <div class="pcc-print-area" id="pcPrintArea" aria-hidden="true"></div>
                        <div class="pcc-layers" id="pcLayers"></div>
                    </div>
                    <p class="pcc-canvas-hint"><i class="bi bi-arrows-move"></i> Drag elements to position them. Use the handles to resize.</p>
                </div>

            </section>

        </main>

    </div>

    {{-- Decoration placement modal (shown on load) --}}
    <div class="modal fade" id="pcPlacementModal" tabindex="-1" aria-labelledby="pcPlacementModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered pcm-placement-dialog">
            <div class="modal-content pcm-placement-content">
                <div class="modal-header pcm-placement-header">
                    <div>
                        <h5 class="modal-title pcm-placement-title" id="pcPlacementModalLabel">
                            <i class="bi bi-bounding-box"></i> Choose Decoration Area
                        </h5>
                        <p class="pcm-placement-sub">Select where your design will be applied on the garment.</p>
                    </div>
                </div>
                <div class="modal-body pcm-placement-body">
                    <div class="pcm-placement-options">
                        <button type="button" class="pcm-placement-option" data-placement="left_chest">
                            <span class="pcm-placement-icon"><i class="bi bi-heart"></i></span>
                            <span class="pcm-placement-label">Left Chest</span>
                            <span class="pcm-placement-desc">Classic logo position on the front</span>
                            <span class="pcm-placement-price">£3.50 + VAT</span>
                        </button>
                        <button type="button" class="pcm-placement-option" data-placement="back_print">
                            <span class="pcm-placement-icon"><i class="bi bi-printer"></i></span>
                            <span class="pcm-placement-label">Back Print</span>
                            <span class="pcm-placement-desc">Full back print area</span>
                            <span class="pcm-placement-price">£6.00 + VAT</span>
                        </button>
                        <button type="button" class="pcm-placement-option" data-placement="back_embroidery">
                            <span class="pcm-placement-icon"><i class="bi bi-magic"></i></span>
                            <span class="pcm-placement-label">Back Embroidery</span>
                            <span class="pcm-placement-desc">Full back embroidery area</span>
                            <span class="pcm-placement-price">£8.00 + VAT</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pcPreviewModal" tabindex="-1" aria-labelledby="pcPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered pcm-dialog">
            <div class="modal-content pcm-content">
                <div class="modal-header pcm-header">
                    <div>
                        <h5 class="modal-title pcm-title" id="pcPreviewModalLabel"><i class="bi bi-eye"></i> Preview Your Design</h5>
                        <p class="pcm-sub">Review your design before adding to cart</p>
                    </div>
                    <button type="button" class="pcm-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body pcm-body">
                    <div class="pcm-preview-wrap">
                        <div class="pcm-preview-canvas" id="pcmPreviewCanvas">
                            <img class="pcc-garment-img" id="pcmGarmentImg" src="{{ $garmentFront }}" alt="{{ $product['title'] }} preview">
                            <div class="pcc-layers" id="pcmLayers"></div>
                        </div>
                    </div>
                    <div class="pcm-details">
                        <div class="pcm-detail-row"><span>Garment</span><strong id="pcmGarment">{{ $product['type'] ?: $product['title'] }}</strong></div>
                        <div class="pcm-detail-row"><span>Colour</span><strong id="pcmColour">{{ $activeColour['name'] ?? '—' }}</strong></div>
                        <div class="pcm-detail-row"><span>Sizes</span><strong id="pcmSize">{{ $sizeDisplay }}</strong></div>
                        <div class="pcm-detail-row"><span>Quantity</span><strong id="pcmQty">{{ $defaultQty }}</strong></div>
                        <div class="pcm-detail-row"><span>Design Elements</span><strong id="pcmElements">0</strong></div>
                        <div class="pcm-detail-divider"></div>
                        <div class="pcm-detail-row pcm-detail-total"><span>Estimated Total <small class="pc-vat-note">(ex. VAT)</small></span><strong id="pcmTotal">£{{ $priceWhole }}.{{ $priceDecimal }} <small class="pc-vat-note">+ VAT</small></strong></div>
                    </div>
                </div>
                <div class="modal-footer pcm-footer" id="pcmFooter">
                    <div class="pcm-editing-actions" id="pcmEditingActions">
                        <button type="button" class="pcm-continue-btn" data-bs-dismiss="modal">Continue Editing</button>
                        <button type="button" class="pcm-checkout-btn" id="pcmConfirmAddBtn"><i class="bi bi-cart-plus"></i> Confirm &amp; Add to Cart</button>
                    </div>
                    <div class="pcm-success-actions" id="pcmSuccessActions" style="display:none">
                        <a href="{{ route('shop.checkout') }}" class="pcm-checkout-now-btn" id="pcmCheckoutNowBtn"><i class="bi bi-credit-card"></i> Checkout Now</a>
                        <a href="{{ route('shop.cart') }}" class="pcm-view-cart-btn" id="pcmViewCartBtn"><i class="bi bi-cart3"></i> View Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
@php
    $customizerData = [
        'title' => $product['title'],
        'type' => $product['type'],
        'brand' => $product['brand'],
        'min_price' => $cartItem?->unit_price ?? $product['min_price'],
        'customizer_image' => $garmentFront,
        'customizer_image_back' => $garmentBack,
        'fallback_customizer_front' => asset('assets/images/customizer/tshirtcustom.png'),
        'fallback_customizer_back' => asset('assets/images/customizer/tshirtcustom-back.png'),
        'uploaded_customizer_image' => $product['uploaded_customizer_image'] ?? null,
        'colours' => $product['colours'],
        'default_size' => $defaultSize,
        'default_qty' => $defaultQty,
        'default_colour' => $activeColour['code'] ?? request('colour'),
        'size_breakdown' => $sizeBreakdown,
        'size_display' => $sizeDisplay,
        'cart_item_id' => request('cart_item_id'),
        'logo_setup_fee' => $cartItem?->logo_setup_fee ?? 0,
        'customize_url' => request('cart_item_id') ? route('shop.cart.customize', request('cart_item_id')) : null,
        'cart_url' => route('shop.cart'),
        'checkout_url' => route('shop.checkout'),
        'pricing' => \App\Services\ShopPricingService::clientConfig(),
    ];
@endphp
window.__CUSTOMIZER_DATA__ = @json($customizerData);
</script>
<script src="{{ asset('assets/js/design-print-area.js') }}?v=1.2"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/product-customizer.js?v=2.2') }}"></script>
@endpush