<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KC7N76G');</script>
<!-- End Google Tag Manager -->

@auth
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1F9J2QH202"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  var userid = {{ auth()->id()}}
  var user = "{{ auth()->user()->first_name;}}"
  gtag('config', 'G-1F9J2QH202', {
  'user_id': userid,
  'userName': user
 
});
dataLayer.push({'user_id': userid});
</script>

@endauth
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{-- <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script> --}}
    	
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script> --}}
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/decoupled-document/ckeditor.js"></script> --}}

    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script> --}}
    {{-- <script src="../node_modules/ckeditor4/ckeditor.js"></script>
    <script src="../node_modules/ckeditor4-vue/dist/ckeditor.js"></script> --}}
    
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/super-build/ckeditor.js"></script> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- TODO translatable suffix --}}
    <title>@yield('title', trans('admin.general.home_title')) - {{ trans('admin.general.app_title') }}</title>

    @include('brackets/admin-ui::admin.partials.main-styles')

    @yield('styles')
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                "gtm.start": new Date().getTime(),
                event: "gtm.js"
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != "dataLayer" ? "&l=" + l : "";
            j.async = true;
            j.src = "https://www.googletagmanager.com/gtm.js?id=" + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, "script", "dataLayer", "GTM-PVSJFTW");
    </script>
    <!-- End Google Tag Manager -->
    

      

        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <meta name="csrf-token" content="{{ csrf_token() }}" />

        {{-- TODO translatable suffix --}}
        <title>
            @yield('title', trans('admin.general.home_title')) -
            {{ trans("admin.general.app_title") }}
        </title>

        @include('brackets/admin-ui::admin.partials.main-styles')
        @yield('styles')
        <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    </head>

<body class="app header-fixed sidebar-fixed sidebar-lg-show">
<!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PVSJFTW" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @yield('header')

    @yield('content')

    @yield('footer')

    @include('brackets/admin-ui::admin.partials.wysiwyg-svgs')
    @include('brackets/admin-ui::admin.partials.main-bottom-scripts')
    @yield('bottom-scripts')
    @stack('graph-scripts')
</body>



</html>
