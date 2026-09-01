{{-- Wide upload wizard modal - replaces customizer redirect for now --}}
<div class="modal fade" id="pdUploadWizard" tabindex="-1" aria-labelledby="pdUploadWizardLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered uw-dialog">
        <div class="modal-content uw-content">
            <div class="uw-header">
                <div class="uw-heading">
                    <h2 class="uw-title" id="pdUploadWizardLabel">Customise your garment</h2>
                    <p class="uw-intro">Choose the position, application method, then send us the artwork details for this item.</p>
                </div>
                <button type="button" class="uw-close" data-uw-close aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="uw-progress" aria-label="Customizer progress">
                <div class="uw-progress-track">
                    <span class="uw-progress-fill" id="uwProgressFill"></span>
                </div>
                <div class="uw-progress-steps">
                    <span class="uw-progress-step is-active" data-progress-step="1">
                        <span class="uw-progress-dot">1</span>
                        <span class="uw-progress-label">Position</span>
                    </span>
                    <span class="uw-progress-step" data-progress-step="2">
                        <span class="uw-progress-dot">2</span>
                        <span class="uw-progress-label">Method</span>
                    </span>
                    <span class="uw-progress-step" data-progress-step="3">
                        <span class="uw-progress-dot">3</span>
                        <span class="uw-progress-label">Design</span>
                    </span>
                </div>
            </div>

            <div class="uw-body">
                {{-- Step 1: Position --}}
                <div class="uw-step uw-step-active" data-step="1">
                    <h2 class="uw-step-title">1. Choose position</h2>
                    <p class="uw-step-sub" id="uwPositionCount">0 position selected</p>
                    <div class="uw-panel">
                        <div class="uw-position-grid" id="uwPositionGrid">
                            <button type="button" class="uw-position-card" data-placement="left_chest">
                                <div class="uw-position-visual">
                                    <svg viewBox="0 0 120 140" aria-hidden="true">
                                        <path d="M30 35 L60 25 L90 35 L95 55 L100 130 L20 130 L25 55 Z" fill="#f0f0f0" stroke="#ccc" stroke-width="1.5"/>
                                        <circle cx="72" cy="52" r="10" fill="rgba(238,96,35,0.25)" stroke="var(--c-primary)" stroke-width="1.5"/>
                                        <text x="72" y="55" text-anchor="middle" font-size="5" fill="var(--c-primary)" font-weight="600">LOGO</text>
                                    </svg>
                                </div>
                                <span class="uw-position-label">Left Chest</span>
                                <span class="uw-position-badges">
                                    <span title="Print available"><i class="bi bi-droplet-fill"></i></span>
                                    <span title="Embroidery available"><i class="bi bi-circle-fill"></i></span>
                                </span>
                            </button>
                            <button type="button" class="uw-position-card" data-placement="back_print">
                                <div class="uw-position-visual">
                                    <svg viewBox="0 0 120 140" aria-hidden="true">
                                        <path d="M30 35 L60 25 L90 35 L95 55 L100 130 L20 130 L25 55 Z" fill="#f0f0f0" stroke="#ccc" stroke-width="1.5"/>
                                        <rect x="42" y="45" width="36" height="55" rx="3" fill="rgba(238,96,35,0.2)" stroke="var(--c-primary)" stroke-width="1.5"/>
                                        <text x="60" y="76" text-anchor="middle" font-size="5" fill="var(--c-primary)" font-weight="600">LOGO</text>
                                    </svg>
                                </div>
                                <span class="uw-position-label">Back Print</span>
                                <span class="uw-position-badges">
                                    <span title="Print available"><i class="bi bi-droplet-fill"></i></span>
                                </span>
                            </button>
                            <button type="button" class="uw-position-card" data-placement="back_embroidery">
                                <div class="uw-position-visual">
                                    <svg viewBox="0 0 120 140" aria-hidden="true">
                                        <path d="M30 35 L60 25 L90 35 L95 55 L100 130 L20 130 L25 55 Z" fill="#f0f0f0" stroke="#ccc" stroke-width="1.5"/>
                                        <rect x="42" y="45" width="36" height="55" rx="3" fill="rgba(238,96,35,0.2)" stroke="var(--c-primary)" stroke-width="1.5"/>
                                        <text x="60" y="76" text-anchor="middle" font-size="5" fill="var(--c-primary)" font-weight="600">LOGO</text>
                                    </svg>
                                </div>
                                <span class="uw-position-label">Back Embroidery</span>
                                <span class="uw-position-badges">
                                    <span title="Embroidery available"><i class="bi bi-circle-fill"></i></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Application method --}}
                <div class="uw-step" data-step="2">
                    <h2 class="uw-step-title">2. Choose application method</h2>
                    <div class="uw-panel">
                        <div class="uw-method-grid" id="uwMethodGrid">
                            <button type="button" class="uw-method-card" data-method="embroidery">
                                <div class="uw-method-thumb uw-method-thumb--embroidery">
                                    <i class="bi bi-scissors"></i>
                                </div>
                                <div class="uw-method-body">
                                    <h3 class="uw-method-title">
                                        Embroidery (Stitching)
                                        <i class="bi bi-circle-fill uw-method-icon"></i>
                                    </h3>
                                    <p class="uw-method-desc">Embroidery (or stitching) is detailed and durable which is better suited to uniforms.</p>
                                </div>
                                <span class="uw-method-check"><i class="bi bi-check-lg"></i></span>
                            </button>
                            <button type="button" class="uw-method-card" data-method="print">
                                <div class="uw-method-thumb uw-method-thumb--print">
                                    <i class="bi bi-droplet-fill"></i>
                                </div>
                                <div class="uw-method-body">
                                    <h3 class="uw-method-title">
                                        Print
                                        <i class="bi bi-droplet-fill uw-method-icon"></i>
                                    </h3>
                                    <p class="uw-method-desc">The print application method is both vivid and flexible, ideal for general use.</p>
                                </div>
                                <span class="uw-method-check"><i class="bi bi-check-lg"></i></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Upload or Text --}}
                <div class="uw-step" data-step="3">
                    <h2 class="uw-step-title">3. Add your design</h2>
                    <div class="uw-panel uw-panel--design">
                        <div class="uw-design-toggle">
                            <button type="button" class="uw-design-toggle-btn uw-design-toggle-btn--active" data-design-mode="upload">
                                <i class="bi bi-cloud-arrow-up"></i> Upload Artwork
                            </button>
                            <button type="button" class="uw-design-toggle-btn" data-design-mode="text">
                                <i class="bi bi-fonts"></i> Add Text
                            </button>
                        </div>

                        {{-- Upload panel --}}
                        <div class="uw-design-panel uw-design-panel--active" id="uwUploadPanel">
                            <div class="uw-upload-zone" id="uwUploadZone">
                                <input type="file" id="uwFileInput" class="uw-upload-input" accept=".png,.jpg,.jpeg,.svg,.webp">
                                <div class="uw-upload-placeholder" id="uwUploadPlaceholder">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <p>Drag &amp; drop your logo or artwork here</p>
                                    <span>or click to browse · PNG, JPG, SVG · Max 10 MB</span>
                                </div>
                                <div class="uw-upload-preview" id="uwUploadPreview" hidden>
                                    <img id="uwPreviewImg" src="" alt="Uploaded artwork preview">
                                    <button type="button" class="uw-upload-remove" id="uwRemoveFile" aria-label="Remove file">
                                        <i class="bi bi-trash3"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Text panel --}}
                        <div class="uw-design-panel" id="uwTextPanel">
                            <div class="uw-text-form">
                                <div class="uw-field">
                                    <label for="uwTextLine1">Text Line 1 <span class="uw-required">*</span></label>
                                    <input type="text" id="uwTextLine1" class="uw-input" placeholder="e.g. Workwear Express" maxlength="40">
                                </div>
                                <div class="uw-field">
                                    <label for="uwTextLine2">Text Line 2 <span class="uw-optional">(Optional)</span></label>
                                    <input type="text" id="uwTextLine2" class="uw-input" placeholder="" maxlength="40">
                                </div>
                                <div class="uw-field">
                                    <label for="uwTextLine3">Text Line 3 <span class="uw-optional">(Optional)</span></label>
                                    <input type="text" id="uwTextLine3" class="uw-input" placeholder="" maxlength="40">
                                </div>
                                <div class="uw-field-row">
                                    <div class="uw-field">
                                        <label for="uwTextFont">Font</label>
                                        <div class="uw-select-wrap">
                                            <select id="uwTextFont" class="uw-select">
                                                <option value="Arial">Arial</option>
                                                <option value="Helvetica">Helvetica</option>
                                                <option value="Georgia">Georgia</option>
                                                <option value="Times New Roman">Times New Roman</option>
                                                <option value="Verdana">Verdana</option>
                                                <option value="Tahoma">Tahoma</option>
                                                <option value="Impact">Impact</option>
                                            </select>
                                            <i class="bi bi-chevron-down uw-select-arrow"></i>
                                        </div>
                                    </div>
                                    <div class="uw-field">
                                        <label for="uwTextColor">Colour</label>
                                        <div class="uw-select-wrap">
                                            <select id="uwTextColor" class="uw-select">
                                                <option value="#ffffff" data-label="White">White</option>
                                                <option value="#000000" data-label="Black">Black</option>
                                                <option value="#1a2744" data-label="Navy">Navy</option>
                                                <option value="#c0392b" data-label="Red">Red</option>
                                                <option value="#ee6023" data-label="Orange">Orange</option>
                                                <option value="#f5c518" data-label="Yellow">Yellow</option>
                                                <option value="#27ae60" data-label="Green">Green</option>
                                                <option value="#3498db" data-label="Blue">Blue</option>
                                            </select>
                                            <i class="bi bi-chevron-down uw-select-arrow"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="uw-text-preview-wrap">
                                    <span class="uw-text-preview-label">Text Preview</span>
                                    <div class="uw-text-preview" id="uwTextPreview">
                                        <span id="uwTextPreviewContent">Your text will appear here</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="uw-footer">
                <button type="button" class="uw-btn uw-btn-back" id="uwBackBtn" hidden>
                    Back a step
                </button>
                <span class="uw-footer-status" id="uwFooterStatus"></span>
                <button type="button" class="uw-btn uw-btn-continue" id="uwContinueBtn" disabled>
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>
