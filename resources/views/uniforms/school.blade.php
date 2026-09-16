@extends('layouts.app')

@section('content')
<main class="uniform-page">
  <section class="page-hero page-hero--compact"><div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i>
      <a href="{{ route('uniforms') }}">Uniform Shop</a><i class="bi bi-chevron-right"></i>
      <span>{{ $school->name }}</span>
    </div>
    <span class="h-eyebrow">School Uniforms</span>
    <h1 class="mt-2">{{ $school->name }}</h1>
    <p class="lead">{{ $unlocked ? 'Browse the school uniform range below.' : 'Enter the 4-digit PIN from your school to unlock this catalogue.' }}</p>
  </div></section>

  <section class="section section--tight"><div class="container">
    <div id="uniformShopRoot"></div>
  </div></section>
</main>
@endsection

@push('scripts')
<script>
window.UNIFORM_SHOP = {
  school: @json($school->toFrontend()),
  unlocked: @json($unlocked),
  schoolsUrl: @json(route('uniforms')),
  unlockUrl: @json(route('api.uniforms.unlock', $school)),
  catalogueUrl: @json(route('api.uniforms.school', $school)),
  productUrlBase: @json(url('/api/uniforms/products')),
  checkoutUrl: @json(route('api.uniforms.checkout')),
  user: @json(auth()->check() ? [
    'name' => auth()->user()->name,
    'email' => auth()->user()->email,
    'phone' => auth()->user()->phone ?? '',
    'company_name' => auth()->user()->company_name ?? '',
  ] : null),
};
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.THREADS && typeof THREADS.initPublicUniformShop === 'function') {
    THREADS.initPublicUniformShop();
  }
});
</script>
@endpush
