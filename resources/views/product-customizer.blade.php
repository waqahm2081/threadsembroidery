@extends('layouts.customizer')

@section('title', 'Product Customizer — Threads Embroidery &amp; Print Ltd')

@section('body_class')
pc-body
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/product-customizer.css') }}">
@endpush

@section('hide_scripts')
@endsection

@section('content')
<!-- ============================================================
         CUSTOMIZER APP SHELL — full-width, no header/footer
         Top bar: logo, back-to-home, product name/meta, add to cart
         Left panel: product details (always visible) + collapsible
         option groups (Colour / Upload / Text / Layers) + summary
         (always visible)
         Right: large live preview canvas (Lumise-style)
    ============================================================ -->
    <div id="pc-app">

        <!-- ============================================================
             TOP BAR
        ============================================================ -->
        <header id="pc-topbar">
            <div class="pct-left">
                <a href="{{ route('home') }}" class="pct-logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Threads Embroidery &amp; Print" class="pct-logo-img">
                    <div class="pct-logo-fallback" style="display:none;">
                        <span class="pct-logo-fallback-icon"><i class="bi bi-stars"></i></span>
                        <span class="pct-logo-fallback-text">Threads<span>Embroidery &amp; Print</span></span>
                    </div>
                </a>
                <a href="{{ route('home') }}" class="pct-back-btn">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back to Home</span>
                </a>
            </div>

            <div class="pct-center">
                <span class="pct-product-meta"><i class="bi bi-rulers"></i> Sizes: <strong id="pctSize">M</strong></span>
                <span class="pct-product-meta"><i class="bi bi-stack"></i> Qty: <strong id="pctQty">25</strong></span>
            </div>

            <div class="pct-right">
                <div class="pct-price-box">
                    <span class="pct-price-label">Unit Price</span>
                    <span class="pct-price-value" id="pctPrice">£6.50</span>
                </div>
                <button class="pct-cart-btn" id="pcAddToCartBtn" data-bs-toggle="modal" data-bs-target="#pcPreviewModal">
                    <i class="bi bi-cart-plus"></i>
                    <span>Add to Cart</span>
                </button>
            </div>
        </header>


        <!-- ============================================================
             WORKSPACE — left controls panel + right preview canvas
        ============================================================ -->
        <main id="pc-workspace">

            <!-- ══════════ LEFT PANEL: Product info + collapsible controls ══════════ -->
            <aside id="pc-left">

                <!-- Product summary — always visible -->
                <div class="pcl-section pcl-product">
                    <div class="pcl-product-thumb">
                        <img src="{{ asset('assets/images/customizer/tshirtcustom.png') }}" alt="Custom T-Shirt" id="pclProductThumb">
                    </div>
                    <div class="pcl-product-info">
                        <h2 class="pcl-product-title">Custom T-Shirt</h2>
                        <p class="pcl-product-sub">Premium 180gsm Cotton — Unisex Fit</p>
                    </div>
                </div>
                
                <div class="pcl-divider"></div>

                <!-- ══════════ COLLAPSIBLE OPTION GROUPS (accordion) ══════════ -->
                <div class="pcl-accordion" id="pcAccordion">

                    <!-- COLOUR — open by default -->
                    <div class="pcl-acc-item">
                        <button class="pcl-acc-header active" type="button" data-acc-target="pcAccColour">
                            <span class="pcl-acc-header-label"><i class="bi bi-palette2"></i> Garment Colour</span>
                            <span class="pcl-acc-header-right">
                                <span class="pcl-selected-colour-name" id="pcColourName">White</span>
                                <i class="bi bi-chevron-down pcl-acc-chevron"></i>
                            </span>
                        </button>
                        <div class="pcl-acc-body active" id="pcAccColour">
                            <div class="pcl-acc-body-inner">
                                <div class="pcl-swatches" id="pcSwatches">
                                    <button class="pcl-swatch active" data-colour="White"       data-hex="#ffffff" style="background:#ffffff; border-color:rgba(0,0,0,0.15);" aria-label="White" title="White"></button>
                                    <button class="pcl-swatch"        data-colour="Navy"        data-hex="#1a1a2e" style="background:#1a1a2e;" aria-label="Navy" title="Navy"></button>
                                    <button class="pcl-swatch"        data-colour="Black"       data-hex="#101010" style="background:#101010;" aria-label="Black" title="Black"></button>
                                    <button class="pcl-swatch"        data-colour="Red"         data-hex="#c0392b" style="background:#c0392b;" aria-label="Red" title="Red"></button>
                                    <button class="pcl-swatch"        data-colour="Royal Blue"  data-hex="#2471a3" style="background:#2471a3;" aria-label="Royal Blue" title="Royal Blue"></button>
                                    <button class="pcl-swatch"        data-colour="Bottle Green" data-hex="#1e6641" style="background:#1e6641;" aria-label="Bottle Green" title="Bottle Green"></button>
                                    <button class="pcl-swatch"        data-colour="Orange"      data-hex="#ee6023" style="background:#ee6023;" aria-label="Orange" title="Orange"></button>
                                    <button class="pcl-swatch"        data-colour="Heather Grey" data-hex="#9ea5aa" style="background:#9ea5aa;" aria-label="Heather Grey" title="Heather Grey"></button>
                                    <button class="pcl-swatch"        data-colour="Yellow"      data-hex="#f1c40f" style="background:#f1c40f;" aria-label="Yellow" title="Yellow"></button>
                                    <button class="pcl-swatch"        data-colour="Purple"      data-hex="#6c3483" style="background:#6c3483;" aria-label="Purple" title="Purple"></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UPLOAD -->
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

                    <!-- TEXT -->
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

                    <!-- LAYERS -->
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

                </div><!-- /pcAccordion -->

                <div class="pcl-divider"></div>

                <!-- Order Summary — always visible -->
                <div class="pcl-section">
                    <div class="pcl-summary">
                        <div class="pcl-summary-row">
                            <span>Garment</span>
                            <strong id="pcSummaryGarment">T-Shirt</strong>
                        </div>
                        <div class="pcl-summary-row">
                            <span>Colour</span>
                            <strong id="pcSummaryColour">White</strong>
                        </div>
                        <div class="pcl-summary-row">
                            <span>Design Elements</span>
                            <strong id="pcSummaryElements">0</strong>
                        </div>
                        <div class="pcl-summary-divider"></div>
                        <div class="pcl-summary-row pcl-summary-total">
                            <span>Estimated Unit Price</span>
                            <strong id="pcSummaryTotal">£6.50</strong>
                        </div>
                    </div>
                </div>

            </aside><!-- /pc-left -->


            <!-- ══════════ RIGHT: Live Preview Canvas ══════════ -->
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

                        <!-- Garment render: single white PNG, recoloured live via CSS filters (Lumise-style) -->
                        <div class="pcc-garment-render" id="pcGarmentRender">
                            <img class="pcc-garment-img" id="pcGarmentImg"
                                 src="{{ asset('assets/images/customizer/tshirtcustom.png') }}"
                                 alt="Custom T-Shirt preview">
                        </div>

                        <!-- Print/decoration area guide -->
                        <div class="pcc-print-area" id="pcPrintArea" aria-hidden="true"></div>

                        <!-- Layer container: uploaded images & text get appended here -->
                        <div class="pcc-layers" id="pcLayers"></div>

                    </div><!-- /pcCanvas -->

                    <p class="pcc-canvas-hint"><i class="bi bi-arrows-move"></i> Drag elements to position them. Use the handles to resize.</p>
                </div>

            </section><!-- /pc-canvas-area -->

        </main>

    </div><!-- /pc-app -->


    <!-- ============================================================
         ADD TO CART / DESIGN PREVIEW MODAL
    ============================================================ -->
    <div class="modal fade" id="pcPreviewModal" tabindex="-1" aria-labelledby="pcPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered pcm-dialog">
            <div class="modal-content pcm-content">

                <div class="modal-header pcm-header">
                    <div>
                        <h5 class="modal-title pcm-title" id="pcPreviewModalLabel"><i class="bi bi-check-circle-fill"></i> Added to Cart</h5>
                        <p class="pcm-sub">Here's a preview of your custom design</p>
                    </div>
                    <button type="button" class="pcm-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="modal-body pcm-body">
                    <div class="pcm-preview-wrap">
                        <div class="pcm-preview-canvas" id="pcmPreviewCanvas">
                            <img class="pcc-garment-img" id="pcmGarmentImg" src="{{ asset('assets/images/customizer/tshirtcustom.png') }}" alt="Custom T-Shirt preview">
                            <div class="pcc-layers" id="pcmLayers"></div>
                        </div>
                    </div>
                    <div class="pcm-details">
                        <div class="pcm-detail-row"><span>Garment</span><strong id="pcmGarment">T-Shirt</strong></div>
                        <div class="pcm-detail-row"><span>Colour</span><strong id="pcmColour">White</strong></div>
                        <div class="pcm-detail-row"><span>Size</span><strong id="pcmSize">M</strong></div>
                        <div class="pcm-detail-row"><span>Quantity</span><strong id="pcmQty">25</strong></div>
                        <div class="pcm-detail-row"><span>Design Elements</span><strong id="pcmElements">0</strong></div>
                        <div class="pcm-detail-divider"></div>
                        <div class="pcm-detail-row pcm-detail-total"><span>Estimated Total</span><strong id="pcmTotal">£6.50</strong></div>
                    </div>
                </div>

                <div class="modal-footer pcm-footer">
                    <button type="button" class="pcm-continue-btn" data-bs-dismiss="modal">Continue Editing</button>
                    <a href="{{ route('shop') }}" class="pcm-checkout-btn"><i class="bi bi-bag-check"></i> View Cart &amp; Checkout</a>
                </div>

            </div>
        </div>
    </div>


    <!-- Bootstrap 5 JS (needed for the Add to Cart modal) -->

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/product-customizer.js?v=1.1') }}"></script>
@endpush
