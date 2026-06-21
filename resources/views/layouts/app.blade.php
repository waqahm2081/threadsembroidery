<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Threads Embroidery & Print Ltd')</title>

    @include('partials.styles')
    @stack('styles')
</head>

<body @hasSection('body_class') class="@yield('body_class')" @endif>

    @hasSection('loader')
        @yield('loader')
    @endif

    @unless(View::hasSection('hide_header'))
        @include('partials.header')
    @endunless

    @yield('content')

    @hasSection('after_content')
        @yield('after_content')
    @endif

    @unless(View::hasSection('hide_footer'))
        @include('partials.footer')
    @endunless

    @unless(View::hasSection('hide_scripts'))
        @include('partials.scripts')
    @endunless

    @stack('scripts')

</body>

</html>
