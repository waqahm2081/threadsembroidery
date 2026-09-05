@php
  $title = $title ?? 'Admin';
  $subtitle = $subtitle ?? '';
  $nav = $nav ?? '';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }} | Threads Admin</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('assets/img/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@stack('styles')
</head>
<body @if(!empty($cfgAdmin)) data-cfg-admin="1" @endif>
@php $admin = auth()->user(); @endphp
<div class="ad-shell">
  <aside class="ad-side" id="adSide">
    <div class="ad-brand">
      <img src="{{ asset('assets/img/logo.png') }}" alt="Threads Embroidery & Print">
    </div>
    <nav class="ad-nav">
      <a href="{{ route('admin.dashboard') }}" class="{{ $nav === 'dashboard' ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>
      <a href="{{ route('admin.users.index') }}" class="{{ $nav === 'users' ? 'active' : '' }}"><i class="bi bi-people"></i> Users</a>
      <a href="{{ route('admin.bundles.index') }}" class="{{ $nav === 'bundles' ? 'active' : '' }}"><i class="bi bi-boxes"></i> Bundles</a>
      <a href="{{ route('admin.size-guides.index') }}" class="{{ $nav === 'size-charts' ? 'active' : '' }}"><i class="bi bi-rulers"></i> Size Charts</a>
      <a href="{{ route('admin.orders.index') }}" class="{{ $nav === 'orders' ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Orders</a>
      <a href="{{ route('admin.shop-orders.index') }}" class="{{ $nav === 'shop-orders' ? 'active' : '' }}"><i class="bi bi-bag-check"></i> Shop Orders</a>
      <a href="{{ route('admin.enquiries.index') }}" class="{{ $nav === 'enquiries' ? 'active' : '' }}"><i class="bi bi-chat-left-text"></i> Branding Enquiries</a>
      <a href="{{ route('admin.customizer') }}" class="{{ $nav === 'customizer' ? 'active' : '' }}"><i class="bi bi-sliders"></i> Product Customizer</a>
      <a href="{{ route('admin.product-quotes.index') }}" class="{{ $nav === 'product-quotes' ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Product Quotes</a>
      <div class="ad-nav-divider">Uniform Shop</div>
      <a href="{{ route('admin.schools.index') }}" class="{{ $nav === 'schools' ? 'active' : '' }}"><i class="bi bi-building"></i> Schools</a>
      <a href="{{ route('admin.school-categories.index') }}" class="{{ $nav === 'school-categories' ? 'active' : '' }}"><i class="bi bi-tags"></i> Categories</a>
      <a href="{{ route('admin.school-products.index') }}" class="{{ $nav === 'school-products' ? 'active' : '' }}"><i class="bi bi-bag"></i> Products</a>
      <a href="{{ route('admin.school-uniform-orders.index') }}" class="{{ $nav === 'school-uniform-orders' ? 'active' : '' }}"><i class="bi bi-cart-check"></i> Uniform Orders</a>
    </nav>
    <div class="ad-side-foot">
      <div class="ad-side-user">
        <span class="ad-av">
          @if($admin?->avatar_url)
            <img src="{{ $admin->avatar_url }}" alt="">
          @else
            {{ $admin?->initials() ?: 'A' }}
          @endif
        </span>
        <div>
          <b>{{ $admin?->name }}</b>
          <span>{{ $admin?->email }}</span>
        </div>
      </div>
    </div>
  </aside>

  <div class="ad-main">
    <div class="ad-mobtop">
      <button type="button" class="ad-btn ghost sm" id="adMenuBtn"><i class="bi bi-list"></i> Menu</button>
      <span class="muted" style="color:var(--ad-muted);font-size:.82rem">Admin</span>
    </div>
    <header class="ad-top">
      <div>
        <h1>{{ $title }}</h1>
        @if($subtitle)<p>{{ $subtitle }}</p>@endif
      </div>
      <div class="ad-top-actions">
        <a href="{{ route('admin.profile.edit') }}" class="ad-btn sm {{ $nav === 'profile' ? 'active' : '' }}"><i class="bi bi-person-gear"></i> My Profile</a>
        <a class="ad-btn ghost sm" href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View site</a>
        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
          @csrf
          <button class="ad-btn ghost sm" type="submit"><i class="bi bi-box-arrow-right"></i> Log out</button>
        </form>
      </div>
    </header>
    <div class="ad-content">
      @if(session('success'))
        <div class="ad-flash ok"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="ad-flash err"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
      @endif
      @if($errors->any())
        <div class="ad-flash err">
          <i class="bi bi-exclamation-circle"></i>
          {{ $errors->first() }}
        </div>
      @endif
      @yield('content')
    </div>
  </div>
</div>
<script>
  const side=document.getElementById('adSide');
  const btn=document.getElementById('adMenuBtn');
  if(btn&&side){
    btn.addEventListener('click',()=>side.classList.toggle('open'));
    document.addEventListener('click',e=>{
      if(window.innerWidth>980) return;
      if(!side.classList.contains('open')) return;
      if(side.contains(e.target)||btn.contains(e.target)) return;
      side.classList.remove('open');
    });
  }
</script>
@stack('scripts')
</body>
</html>
