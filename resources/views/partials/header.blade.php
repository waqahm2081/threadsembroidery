@php
  $site = config('site');
  $categories = $site['categories'] ?? [];
  $nav = $site['nav'] ?? [];
  $topbar = $site['topbar'] ?? [];
  $pageActive = $pageActive ?? '';
@endphp
<div class="topbar"><div class="marquee">@php
  $topRow = '';
  foreach ($topbar as [$icon, $text]) {
      $topRow .= '<span class="m-item"><i class="bi ' . e($icon) . '"></i>' . e($text) . '</span><span class="m-sep">✦</span>';
  }
  echo $topRow . $topRow;
@endphp</div></div>

<header class="site-header" id="siteHeader"><div class="container"><nav class="nav">
  <a class="nav-logo" href="{{ route('home') }}"><img src="{{ asset('assets/img/logo.png') }}" width="391" height="96" alt="{{ $site['name'] }}"></a>
  <ul class="nav-links">
@foreach ($nav as $n)
  @php $act = ($pageActive === ($n['key'] ?? '')) ? ' active' : ''; @endphp
  @if (!empty($n['mega']))
    <li class="has-mega"><a class="{{ trim($act) }}" href="{{ route($n['route']) }}">{{ $n['label'] }} <i class="bi bi-chevron-down" style="font-size:.7em"></i></a>
      <div class="mega">
        <div class="mega-head"><div><span class="h-eyebrow">Browse by Category</span><h3 class="h-3" style="margin-top:.4rem">Product Shop</h3></div>
        <a class="btn btn--ghost" href="{{ route('shop.cart') }}"><i class="bi bi-bag"></i> Shop Cart{{ (!empty($shopCartCount) && $shopCartCount > 0) ? ' ('.$shopCartCount.')' : '' }}</a></div>
        <div class="mega-grid">
@php $shopCats = ($n['key'] ?? '') === 'shop' ? ($shopCategories ?? []) : ($categories ?? []); @endphp
@foreach ($shopCats as $c)
          <a href="{{ route('shop.category', $c['slug']) }}"><span class="n">{{ $c['name'] }}</span><span class="c">{{ (int) ($c['count'] ?? 0) }}</span></a>
@endforeach
        </div>
        <p class="muted" style="margin-top:16px;font-size:.86rem">Can't find what you're looking for? <a class="link-arrow" href="{{ route('contact') }}" style="display:inline">Contact us</a> and we'll source it for you.</p>
      </div></li>
  @else
    <li><a class="{{ trim($act) }}" href="{{ route($n['route']) }}">{{ $n['label'] }}</a></li>
  @endif
@endforeach
  </ul>
  <div class="nav-actions">
    {{-- Search temporarily hidden
    <button class="icon-btn search-btn" aria-label="Search products" data-search-open><i class="bi bi-search"></i></button>
    --}}
    <a class="icon-btn" href="{{ route('cart') }}" aria-label="View cart"><i class="bi bi-bag"></i><span class="badge" data-cart-badge style="display:none">0</span></a>
    <span id="navAuthSlot" data-server-auth="1">
      @auth
        <span class="nav-auth">
          <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
            @csrf
            <button type="submit" class="icon-btn nav-logout" aria-label="Log out" title="Log out"><i class="bi bi-box-arrow-right"></i></button>
          </form>
          <a class="btn nav-cta" href="{{ route('portal') }}"><i class="bi bi-person-circle"></i> User Portal</a>
        </span>
      @else
        <a class="btn nav-cta" href="{{ route('login') }}"><i class="bi bi-person"></i> Login / Signup</a>
      @endauth
    </span>
    <button class="icon-btn hamburger" aria-label="Open menu" data-drawer-open><i class="bi bi-list" style="font-size:1.5rem"></i></button>
  </div>
</nav></div></header>

<div class="drawer" data-drawer>
  <div class="scrim" data-drawer-close></div>
  <div class="panel">
    <div class="d-top"><img src="{{ asset('assets/img/logo.png') }}" style="height:36px;width:auto;max-width:none;object-fit:contain" alt="Threads"><button class="icon-btn" data-drawer-close aria-label="Close menu"><i class="bi bi-x-lg"></i></button></div>
    <nav>
@foreach ($nav as $n)
      <a href="{{ route($n['route']) }}">{{ $n['label'] }}</a>
@endforeach
    </nav>
    @auth
      <a class="btn btn--block mt-3" id="drawerAuthBtn" href="{{ route('portal') }}"><i class="bi bi-person-circle"></i> User Portal</a>
      <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn--ghost btn--block"><i class="bi bi-box-arrow-right"></i> Log out</button>
      </form>
    @else
      <a class="btn btn--block mt-3" id="drawerAuthBtn" href="{{ route('login') }}"><i class="bi bi-person"></i> Login / Signup</a>
    @endauth
  </div>
</div>

<div class="search-overlay" data-search><div class="search-box">
  <div class="field"><i class="bi bi-search" style="color:var(--brand);font-size:1.3rem"></i>
    <input type="text" placeholder="Search products, categories…" data-search-input aria-label="Search">
    <button class="icon-btn" data-search-close aria-label="Close search"><i class="bi bi-x-lg"></i></button></div>
  <div class="search-results" data-search-results hidden></div>
</div></div>
