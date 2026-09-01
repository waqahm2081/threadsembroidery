{{--
    Reusable page hero — matches About Us / Contact Us style.
    Props: heroId, eyebrow, title, accent, subtitle, breadcrumbs (array of label + optional url)
--}}
<section id="{{ $heroId ?? 'page-hero' }}" class="page-hero">
    <div class="ph-container">
        @if(!empty($eyebrow))
            <div class="ph-eyebrow">
                <span class="ph-eyebrow-dot"></span>
                {{ $eyebrow }}
            </div>
        @endif
        <h1 class="ph-title">
            {{ $title }}@if(!empty($accent)) <span class="ph-accent">{{ $accent }}</span>@endif
        </h1>
        @if(!empty($subtitle))
            <p class="ph-sub">{{ $subtitle }}</p>
        @endif
        @if(!empty($breadcrumbs))
            <nav class="ph-breadcrumb" aria-label="Breadcrumb">
                @foreach($breadcrumbs as $index => $crumb)
                    @if($index > 0)
                        <span class="ph-sep"><i class="bi bi-chevron-right"></i></span>
                    @endif
                    @if(!empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                    @else
                        <span>{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif
    </div>
</section>
