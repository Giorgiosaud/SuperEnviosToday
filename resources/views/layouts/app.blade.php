<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://kit.fontawesome.com/f825b9df8e.js" crossorigin="anonymous"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <div class="container is-fluid has-background-white main-navigation">
        <template>
        <b-navbar>
            <template slot="brand">
                <b-navbar-item tag="router-link" :to="{ path: '/' }">
                    <a class="navbar-item" href="{{ url('/') }}">
                        {{ config('app.name', 'Superenvios Today') }}
                    </a>
                </b-navbar-item>
            </template>
            <!--template slot="start">
                <b-navbar-item href="#">
                    Home
                </b-navbar-item>
                <b-navbar-item href="#">
                    Documentation
                </b-navbar-item>
                <b-navbar-dropdown label="Info">
                    <b-navbar-item href="#">
                        About
                    </b-navbar-item>
                    <b-navbar-item href="#">
                        Contact
                    </b-navbar-item>
                </b-navbar-dropdown>
            </template-->
            @guest
            <template slot="end">
                <b-navbar-item tag="div">
                    <div class="buttons">
                        @if (Route::has('register'))
                        <a class="button is-primary"
                           href="{{ route('register') }}">
                            <strong>{{ __('auth.REGISTER') }}</strong>
                        </a>
                        @endif
                        <a class="button is-light" href="{{ route('login') }}">
                            {{ __('AUTH.LOGIN') }}
                        </a>
                    </div>
                </b-navbar-item>
            </template>
            @else
            <template slot="end">
                <!--b-navbar-item href="#">
                    Home
                </b-navbar-item>
                <b-navbar-item href="#">
                    Documentation
                </b-navbar-item-->
                <b-navbar-dropdown label="{{ Auth::user()->fullName }}">
                    <b-navbar-item href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                              style="display: none;">
                            @csrf
                        </form>
                    </b-navbar-item>
                </b-navbar-dropdown>
            </template>
          @endif
        </b-navbar>
    </template>
    </div>
    @include('layouts/flash-message')
    <main>
        @yield('content')
    </main>
</div>
</body>
</html>
