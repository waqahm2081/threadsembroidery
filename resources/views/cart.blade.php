@extends('layouts.app')

@section('content')
<main>
  @include('partials.guest-auth-bar')
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>Cart</span></div>
    <h1>Your Cart</h1>
  </div></section>
  <section class="section section--tight"><div class="container"><div id="cartRoot"></div></div></section>
</main>
@endsection
