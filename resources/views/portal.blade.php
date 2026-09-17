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
        <a class="portal-nav-link" href="{{ route('uniforms') }}"><i class="bi bi-bag-heart"></i> Uniform Shop</a>
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

@push('styles')
<style>
.portal-design-modal[hidden]{display:none!important}
.portal-design-modal{position:fixed;inset:0;z-index:1200;display:flex;align-items:center;justify-content:center;padding:1rem}
.portal-design-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.55)}
.portal-design-dialog{position:relative;z-index:1;background:#fff;border-radius:16px;max-width:720px;width:100%;max-height:90vh;overflow:auto;box-shadow:0 24px 64px rgba(0,0,0,.3)}
.portal-design-header{display:flex;justify-content:space-between;gap:1rem;padding:1.1rem 1.25rem;border-bottom:1px solid #eee}
.portal-design-header h5{margin:0;font-size:1.05rem}
.portal-design-sub{margin:.25rem 0 0;color:#888;font-size:.85rem}
.portal-design-close{border:none;background:transparent;font-size:1.2rem;cursor:pointer}
.portal-design-body{padding:1.1rem 1.25rem 1.35rem}
.portal-design-gallery{display:flex;flex-wrap:wrap;gap:1rem;justify-content:center}
.portal-design-panel{flex:1 1 180px;max-width:240px}
.portal-design-panel-label{text-align:center;font-weight:700;font-size:.82rem;margin-bottom:.4rem}
.portal-design-panel img{width:100%;aspect-ratio:4/5;object-fit:contain;background:#f5f5f5;border-radius:10px}
.portal-design-canvas{position:relative;width:100%;aspect-ratio:4/5;background:#f5f5f5;border-radius:10px;overflow:hidden}
.portal-design-canvas>img{position:absolute;inset:0;width:100%;height:100%;object-fit:contain}
.portal-design-layers{position:absolute;inset:0}
.portal-design-layers .cdm-layer-item{position:absolute;overflow:hidden;display:flex;align-items:center;justify-content:center;box-sizing:border-box}
.portal-design-layers .cdm-layer-item img{position:static;width:100%;height:100%;object-fit:contain}
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/design-print-area.js') }}?v=1.7"></script>
@endpush
