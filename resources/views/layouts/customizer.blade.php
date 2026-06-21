<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Product Customizer — Threads Embroidery & Print Ltd')</title>

    @include('partials.styles')
    @stack('styles')
</head>

<body @hasSection('body_class') class="@yield('body_class')" @endif>

    @yield('content')

    @stack('scripts')

</body>

</html>
