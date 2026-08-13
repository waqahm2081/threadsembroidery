@extends('layouts.app')

@section('content')
<main>
  <section class="section" style="padding-top:clamp(28px,4vw,44px)"><div class="container">
    <div id="productRoot"></div>
  </div></section>
  <section class="section section--tight section--muted"><div class="container">
    <div class="section-head" data-reveal><span class="h-eyebrow">You May Also Like</span><h2 class="display-2 display-2--xs mt-2">Related Products</h2></div>
    <div class="prod-grid" id="relatedGrid"></div>
  </div></section>
</main>
@endsection
