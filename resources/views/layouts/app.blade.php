<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <script>
      window.locale = "{!! config('app.locale') !!}";
    </script>
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="https://kit.fontawesome.com/f825b9df8e.js" crossorigin="anonymous"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>

<div id="app" v-cloak>
    <b-sidebar
        type="is-light"
        fullheight
        expand-on-hover
        :open.sync="navbarOpen"
        overlay
    >
        <div class="has-padding-50">
            <div class="block">
                <img
                    height="28"
                    src="http://superenvios.cl/image/superenvios.png"
                    alt="{{ config('app.name', 'Superenvios Today') }}"
                />
            </div>
            @auth
                @include('layouts.menu')
            @endauth
        </div>
    </b-sidebar>
    <div class="container is-fluid has-background-white main-navigation">
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a role="button" @click="navbarOpen = !navbarOpen" class="navbar-burger always-on" aria-label="menu"
                   aria-expanded="false">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
                <a class="navbar-item" href="/">
                    <img src="http://superenvios.cl/image/superenvios.png"
                         alt="{{ config('app.name', 'Superenvios Today') }}"
                    >
                </a>


            </div>
        </nav>
    </div>
    @include('layouts/flash-message')
    <main>
        @yield('content')
    </main>
</div>

</body>
</html>
