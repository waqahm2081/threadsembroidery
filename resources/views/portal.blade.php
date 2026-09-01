@extends('layouts.app')

@section('content')
<main>
  <div class="portal-mobtop">
    <button class="btn btn--ghost" id="portalMenuBtn"><i class="bi bi-list"></i> Menu</button>
    <span class="muted" style="font-size:.85rem">Client Portal</span>
  </div>
  <div class="portal">
    <aside class="portal-side">
      <div class="portal-user"><span class="av" id="pUserAv">U</span><div class="portal-user-meta"><b id="pUserName">User</b><span id="pUserOrg"></span></div></div>
      <nav class="portal-nav">
        <button class="active" data-view="overview"><i class="bi bi-grid-1x2"></i> Overview</button>
        <button data-view="orders"><i class="bi bi-box-seam"></i> Order History</button>
        <button data-view="uniformOrders"><i class="bi bi-mortarboard"></i> Uniform Orders</button>
        <button data-view="shopOrders"><i class="bi bi-bag"></i> Shop Orders</button>
        <button data-view="enquiries"><i class="bi bi-chat-left-text"></i> Branding Enquiries</button>
        <button data-view="artwork"><i class="bi bi-images"></i> Artwork Library</button>
        <button data-view="account"><i class="bi bi-person-gear"></i> Account</button>
        <button data-view="shop"><i class="bi bi-bag-heart"></i> Uniform Shop</button>
      </nav>
      <hr class="divider" style="margin:1.2rem 0">
      <a class="btn btn--block" href="{{ route('bundles') }}"><i class="bi bi-collection"></i> New Order</a>
      <form method="POST" action="{{ route('logout') }}" style="margin-top:.6rem">
        @csrf
        <button type="submit" id="navLogout" style="width:100%;display:flex;align-items:center;gap:.8rem;padding:.75rem .9rem;color:var(--muted);border-radius:10px;font-weight:600"><i class="bi bi-box-arrow-right"></i> Log Out</button>
      </form>
    </aside>
    <section class="portal-main" id="portalContent"></section>
  </div>
</main>
@endsection
