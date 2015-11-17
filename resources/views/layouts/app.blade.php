<!DOCTYPE html>
<html lang="en">
    @include('partials.global.head')
    <body class="{{ $body_class or '' }}">

        @include('partials.global.header')

        <div id="main-container" class="container">
            <div class="row">
                @yield('body')
            </div>
        </div>
        <div id="footer-container">
            @include('partials.global.footer')
        </div>
        <!-- ALL JS  -->
        <script src="/js/vendor.js"></script>
        <script src="/js/portal.js"></script>

        @include('partials.global.flash')
    </body>
</html>
