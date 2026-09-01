<section id="pd-reviews">
    <div class="pd-reviews-container">
        <div class="pd-reviews-header">
            <div class="pd-reviews-header-left">
                <div class="pd-rev-eyebrow">
                    <span class="pd-rev-eyebrow-dot"></span>
                    Verified Purchases
                </div>
                <h2 class="pd-reviews-heading">Product <span class="pd-rev-accent">Reviews</span></h2>
                <div class="pd-reviews-score">
                    <span class="pd-score-num">4.9</span>
                    <div class="pd-score-right">
                        <div class="pd-score-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <span class="pd-score-count">Based on 127 reviews</span>
                    </div>
                </div>
            </div>
            <button class="pd-write-review-btn" data-bs-toggle="modal" data-bs-target="#reviewModal">
                <i class="bi bi-pencil-square"></i>
                Write a Review
            </button>
        </div>
        <div class="pd-reviews-grid" id="pdReviewsGrid">
            @include('shop.partials.review-cards')
        </div>
    </div>
</section>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered pd-rev-modal-dialog">
        <div class="modal-content pd-rev-modal-content">
            <div class="modal-header pd-rev-modal-header">
                <div>
                    <h5 class="modal-title pd-rev-modal-title" id="reviewModalLabel">Write a Review</h5>
                    <p class="pd-rev-modal-sub">{{ $productTitle ?? 'Product' }}</p>
                </div>
                <button type="button" class="pd-rev-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body pd-rev-modal-body">
                <div class="pd-rev-form-group">
                    <label class="pd-rev-form-label">Your Rating <span class="pd-rev-required">*</span></label>
                    <div class="pd-rev-star-picker" id="pdRevStarPicker" role="group" aria-label="Star rating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="pd-rev-star-btn" data-val="{{ $i }}" aria-label="{{ $i }} star"><i class="bi bi-star"></i></button>
                        @endfor
                    </div>
                    <span class="pd-rev-star-label" id="pdRevStarLabel">Select a rating</span>
                    <input type="hidden" id="pdRevRating" value="0">
                </div>
                <div class="pd-rev-form-row">
                    <div class="pd-rev-form-group">
                        <label class="pd-rev-form-label" for="pdRevName">Full Name <span class="pd-rev-required">*</span></label>
                        <input type="text" id="pdRevName" class="pd-rev-form-input" placeholder="e.g. James Thornton" autocomplete="name">
                    </div>
                    <div class="pd-rev-form-group">
                        <label class="pd-rev-form-label" for="pdRevEmail">Email Address <span class="pd-rev-required">*</span></label>
                        <input type="email" id="pdRevEmail" class="pd-rev-form-input" placeholder="your@email.com" autocomplete="email">
                        <span class="pd-rev-form-hint">Not displayed publicly</span>
                    </div>
                </div>
                <div class="pd-rev-form-group">
                    <label class="pd-rev-form-label" for="pdRevText">Your Review <span class="pd-rev-required">*</span></label>
                    <textarea id="pdRevText" class="pd-rev-form-input pd-rev-textarea" rows="4" placeholder="Tell other customers about your experience with this product…"></textarea>
                    <span class="pd-rev-char-count"><span id="pdRevCharCount">0</span> / 500</span>
                </div>
                <div class="pd-rev-error" id="pdRevError" style="display:none;">
                    <i class="bi bi-exclamation-circle"></i>
                    <span id="pdRevErrorMsg">Please fill in all required fields.</span>
                </div>
            </div>
            <div class="modal-footer pd-rev-modal-footer">
                <button type="button" class="pd-rev-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="pd-rev-submit-btn" id="pdRevSubmitBtn">
                    <i class="bi bi-send"></i>
                    Submit Review
                </button>
            </div>
        </div>
    </div>
</div>
