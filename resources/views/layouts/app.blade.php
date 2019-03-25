<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Super Envios Today') }}</title>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/mark.es6.min.js"></script>

    <!--script src="{{ asset('js/app.js') }}" defer></script-->
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <main-menu inline-template>
        <nav class="flex items-center justify-between flex-wrap bg-teal p-6">
            <a class="no-underline" href="{{ url('/') }}">
                <div class="flex items-center flex-no-shrink text-white mr-6">
                    <img src="{{ asset('image/superenvios.png') }}" alt="Super Envios Today" class="img-fluid"
                         width="60">
                    <span
                        class="font-semibold text-xl tracking-tight ml-3">{{ config('app.name', 'Super Envios Today') }}</span>
                </div>
            </a>
            <div class="block md:hidden">
                <button
                    class="flex items-center px-3 py-2 border rounded text-teal-lighter border-teal-light hover:text-white hover:border-white"
                    @click="openMenu= !openMenu">
                    <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>
                            Menu</title>
                        <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                    </svg>
                </button>
            </div>
            <div class="w-full block flex-grow md:flex md:items-center md:w-auto">
                <div class="text-sm md:flex-grow md:flex md:justify-end" v-if="openMenu">
                    @guest
                        <a class="block mt-4 md:inline-block md:mt-0 text-teal-lighter hover:text-white mr-4 no-underline"
                           href="{{ route('login') }}">{{ __('Login') }}</a>
                        @if (Route::has('register'))
                            <a class="block mt-4 md:inline-block md:mt-0 text-teal-lighter hover:text-white mr-4 no-underline"
                               href="{{ route('register') }}">{{ __('Register') }}</a>
                        @endif
                    @else
                        @if(Auth::user()->hasRole('coordinator'))
                            <div class="relative cursor-pointer text-teal-lighter hover:text-white">
                                <a class="flex items-center justify-centerno-underline" v-cloak
                                   @click="toggleSubMenuCoordinator">
                                    Acciones de Coordinador<i class="material-icons">expand_more</i>
                                </a>
                                <div class="absolute z-10 w-full text-teal-lighter hover:text-whiteabsolute bg-teal"
                                     v-if="coordinatorSubMenu">
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="{{route('users')}}">Listar Usuarios</a>

                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="{{route('registerOperator')}}">Registrar Operador</a>
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="{{route('registerOperatorAccount')}}">Agregar Cuenta a Operador Extranjero</a>
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="{{route('addFoundsToVenezuelanOperator')}}">Agregar Fondos a Operador Venezuela</a>
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="{{route('rate')}}">Definir Tasa</a>
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="{{route('settings')}}">Settings</a>

                                </div>
                            </div>
                        @endif
                        @if(Auth::user()->hasRole('foreign_operator')||Auth::user()->hasRole('coordinator'))
                            <div class="relative cursor-pointer text-teal-lighter hover:text-white">
                                <a class="flex items-center justify-centerno-underline" v-cloak
                                   @click="toggleSubMenuChileanOperator">
                                    Acciones de Operador Chile<i class="material-icons">expand_more</i>
                                </a>
                                <div class="absolute z-10 w-full text-teal-lighter hover:text-whiteabsolute bg-teal"
                                     v-if="chileanOperatorSubMenu">
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="{{route('chilean_transactions')}}">Registrar Transacción</a>
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="#">Listar Operadores Venezuela y saldos disponibles</a>
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="{{route('chilean_pending_transactions')}}">Listar mis Transacciones
                                        pendientes</a>

                                </div>
                            </div>
                        @endif
                        @if(Auth::user()->hasRole('venezuelan_operator')||Auth::user()->hasRole('foreign_operator')||Auth::user()->hasRole('coordinator'))
                            <div class="relative cursor-pointer text-teal-lighter hover:text-white">
                                <a class="flex items-center justify-centerno-underline" v-cloak
                                   @click="toggleSubMenuVenezuelanOperator">
                                    Acciones de Operador Venezuela<i class="material-icons">expand_more</i>
                                </a>
                                <div class="absolute z-10 w-full text-teal-lighter hover:text-whiteabsolute bg-teal"
                                     v-if="venezuelanOperatorSubMenu">
                                    <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                       href="#">Mis Transacciones</a>
                                </div>
                            </div>
                        @endif
                        <div class="relative cursor-pointer text-teal-lighter hover:text-white">
                            <a class="flex items-center justify-centerno-underline" v-cloak
                               @click="toggleSubMenuProfile">
                                {{ Auth::user()->email }} <i class="material-icons">expand_more</i>
                            </a>
                            <div class="absolute w-full text-teal-lighter hover:text-whiteabsolute bg-teal"
                                 v-if="showProfileSubMenu">
                                <a class="block no-underline text-teal-lighter hover:text-white p-3" href="profile">My
                                    Profile</a>
                                <a class="block no-underline text-teal-lighter hover:text-white p-3"
                                   @click.prevent="logout">

                                    {{ __('Logout') }}
                                </a>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>
    </main-menu>
    <main class="py-4">
        @yield('content')
    </main>
</div>
@include('layouts.footer')
</body>
</html>
