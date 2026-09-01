@php
  $pageTitle = $pageTitle ?? config('site.name');
  $pageDescription = $pageDescription ?? '';
  $pageStyles = $pageStyles ?? ['pages.css'];
  $pageScripts = $pageScripts ?? [];
  $pageActive = $pageActive ?? '';
  $pageBodyClass = $pageBodyClass ?? '';
  $pageExtraHead = $pageExtraHead ?? '';
  $pageExtraBody = $pageExtraBody ?? '';
  $pageInlineJs = $pageInlineJs ?? '';
  $mount = $mount ?? false;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@hasSection('title')@yield('title')@else{{ $pageTitle }}@endif</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@if ($pageDescription)
<meta name="description" content="{{ $pageDescription }}">
@endif
<link rel="icon" href="{{ asset('assets/img/logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@foreach ((array) $pageStyles as $css)
<link rel="stylesheet" href="{{ asset('css/' . ltrim($css, '/')) }}">
@endforeach
@stack('styles')
{!! $pageExtraHead !!}
</head>
<body class="@hasSection('body_class')@yield('body_class')@else{{ $pageBodyClass }}@endif">
@include('partials.header')

@yield('content')

@include('partials.footer')

{!! $pageExtraBody !!}
<script src="{{ asset('js/data.js') }}"></script>
@if (!empty($bundlesData))
<script>
window.THREADS = window.THREADS || {};
THREADS.BUNDLES = @json($bundlesData['bundles']);
THREADS.SIZE_GUIDES = @json($bundlesData['sizeGuides']);
THREADS.BUNDLE_COLOURS = @json($bundlesData['colours']);
THREADS.BUNDLE_PLACEMENTS = @json($bundlesData['placements']);
if (THREADS.CONFIG) {
  THREADS.CONFIG.placements = THREADS.BUNDLE_PLACEMENTS;
  THREADS.CONFIG.sizeScale = @json($bundlesData['sizeScale']);
  THREADS.CONFIG.vatRate = @json($bundlesData['vatRate']);
  THREADS.CONFIG.freeDeliveryOver = @json($bundlesData['freeDeliveryOver']);
}
</script>
@endif
@if (!empty($cartBootstrap))
<script>window.THREADS_CART = @json($cartBootstrap);</script>
@endif
@if (!empty($portalBootstrap))
<script>window.THREADS_PORTAL = @json($portalBootstrap);</script>
@endif
@if (!empty($authRoutes))
<script>window.THREADS_AUTH_ROUTES = @json($authRoutes);</script>
@endif
@auth
@php
  $threadsUser = [
      'id' => auth()->id(),
      'name' => auth()->user()->name,
      'email' => auth()->user()->email,
  ];
  $threadsUserArtwork = auth()->user()->artworks()->get()->map->toFrontend()->values()->all();
@endphp
<script>
window.THREADS_USER = @json($threadsUser);
window.THREADS_USER_ARTWORK = @json($threadsUserArtwork);
</script>
@endauth
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/api.js') }}"></script>
@foreach ((array) $pageScripts as $js)
<script src="{{ asset('js/' . ltrim($js, '/')) }}"></script>
@endforeach
@if ($mount !== false)
@php
  $active = e($mount['active'] ?? $pageActive);
  $onReady = $mount['onReady'] ?? null;
  $mountArgs = "{active:'{$active}',base:''";
  if ($onReady) {
      $mountArgs .= ',onReady:' . $onReady;
  }
  $mountArgs .= '}';
@endphp
<script>THREADS.mount({!! $mountArgs !!});</script>
@endif
@if ($pageInlineJs !== '')
<script>{!! $pageInlineJs !!}</script>
@endif
@stack('scripts')
</body>
</html>
