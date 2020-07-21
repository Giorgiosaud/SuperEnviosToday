<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
<div id="app">
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
            <b-menu class="is-custom-mobile">
                @can('manage-users')
                    <b-menu-list label="{{__('users.MENU:TITLE')}}">
                        <b-menu-item icon="users"
                                     label="{{__('users.MENU:LIST')}}"
                                     tag="a"
                                     href="{{ route('users.index') }}"></b-menu-item>
                    </b-menu-list>
                @endcan
                @can('manage-transactions')
                    <b-menu-list label="{{__('transaction.MENU:TITLE')}}">
                        @can('see-all-transactions')
                            <b-menu-item icon="calendar-check"
                                         label="{{__('transaction.MENU:ADJUSTMENT:TRANSACTIONS')}}"
                                         tag="a"
                                         href="{{ route('transaction.adjust') }}"></b-menu-item>
                        @endcan
                        @can('see-all-transactions')
                            <b-menu-item icon="calendar-check"
                                         label="{{__('transaction.MENU:LIST:ALL')}}"
                                         tag="a"
                                         href="{{ route('transaction.index') }}"></b-menu-item>
                        @endcan
                        @can('see-my-transactions')
                            <b-menu-item icon="calendar-check"
                                         label="{{__('transaction.MENU:MY:LIST:ALL')}}"
                                         tag="a"
                                         href="{{ route('transaction.my.index') }}"></b-menu-item>
                        @endcan
                        @can('approve-operations')
                            <b-menu-item icon="calendar-check"
                                         label="{{__('transaction.MENU:PENDING')}}"
                                         tag="a"
                                         href="{{ route('pending-transactions.index') }}"></b-menu-item>
                        @endcan
                        @can('create-transaction')
                            <b-menu-item icon="money-check-alt"
                                         label="{{__('transaction.MENU:CREATE')}}"
                                         tag="a"
                                         href="{{ route('transaction.create') }}"></b-menu-item>
                        @endcan
                    </b-menu-list>
                @endcan
                @can('manage-currencies')
                    <b-menu-list label="{{__('currencies.MENU:TITLE')}}">
                        <b-menu-item icon="coins"
                                     label="{{__('currencies.MENU:MANAGE')}}"
                                     tag="a"
                                     href="{{ route('currencies.index') }}"></b-menu-item>
                    </b-menu-list>
                @endcan
                @can('manage-banks')
                    <b-menu-list label="{{__('banks.MENU:TITLE')}}">
                        <b-menu-item icon="university"
                                     label="{{__('banks.MENU:MANAGE')}}"
                                     tag="a"
                                     href="{{ route('banks.index') }}"></b-menu-item>
                    </b-menu-list>
                @endcan
                @can('manage-rates')
                    <b-menu-list label="{{__('rates.MENU:TITLE')}}">
                        <b-menu-item icon="chart-line"
                                     label="{{__('rates.MENU:MANAGE')}}"
                                     tag="a"
                                     href="{{ route('rates.index') }}"></b-menu-item>
                    </b-menu-list>
                @endcan
                @can('manage-settings')
                    <b-menu-list label="{{__('settings.MENU:TITLE')}}">
                        <b-menu-item icon="tools"
                                     label="{{__('settings.MENU:MANAGE')}}"
                                     tag="a"
                                     href="{{ route('settings.index') }}"></b-menu-item>
                    </b-menu-list>
                @endcan

                <b-menu-list label="Actions">
                    @auth
                        <form id="logout-form"
                              action="{{ route('logout') }}"
                              method="POST"
                              style="display: none;">
                            @csrf
                        </form>
                        <b-menu-item icon="sign-out-alt"
                                     label="Logout"
                                     onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                        >
                        </b-menu-item>
                    @else
                        <b-menu-item label="{{ __('auth.REGISTER') }}"
                                     icon="file-signature"
                                     tag="a"
                                     href="{{ route('register') }}"></b-menu-item>
                        <b-menu-item label="{{ __('auth.LOGIN') }}"
                                     icon="sign-in-alt"
                                     tag="a"
                                     href="{{ route('login') }}"></b-menu-item>
                    @endauth

                </b-menu-list>
            </b-menu>
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
