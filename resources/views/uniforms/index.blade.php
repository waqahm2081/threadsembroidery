@extends('layouts.app')

@section('content')
<main class="uniform-page">
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>Uniform Shop</span></div>
    <span class="h-eyebrow">School Uniforms</span>
    <h1 class="mt-2">Uniform Shop</h1>
    <p class="lead">Choose your school to browse its uniform range. You’ll need the 4-digit PIN from your school to view products.</p>
  </div></section>

  <section class="section section--tight"><div class="container">
    @if($schools->isEmpty())
      <div class="panel-card" style="padding:2rem;text-align:center">
        <p class="muted" style="margin:0">No school shops are available yet. Please check back soon.</p>
      </div>
    @else
      <div class="su-school-grid">
        @foreach($schools as $school)
          <a class="su-school-card" href="{{ route('uniforms.school', $school->slug) }}" style="--accent: {{ $school->accent_colour ?: '#e85820' }}">
            <div class="su-school-logo">
              @if($school->logo_url)
                <img src="{{ $school->logo_url }}" alt="{{ $school->name }}">
              @else
                <span class="su-school-initial">{{ strtoupper(substr($school->name, 0, 1)) }}</span>
              @endif
            </div>
            <div class="su-school-body">
              <h3>{{ $school->name }}</h3>
              @if($school->description)
                <p>{{ $school->description }}</p>
              @endif
            </div>
            <span class="btn su-school-cta"><i class="bi bi-lock"></i> Enter store</span>
          </a>
        @endforeach
      </div>
    @endif
  </div></section>
</main>
@endsection
