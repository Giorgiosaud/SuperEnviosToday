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
        <nav class="navbar navbar-expand-lg navbar-light bg-teal p-6">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar"
                    aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('image/superenvios.png') }}" alt="Super Envios Today" class="img-fluid"
                     width="60">
                <span
                    class="font-semibold text-xl tracking-tight ml-3">{{ config('app.name', 'Super Envios Today') }}</span>
            </a>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('login') }}">{{ __('Ingresar al sistema') }}</a>
                        </li>
                    @else
                        @if(Auth::user()->hasRole('coordinator'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="coordinator-dropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones de Coordinador
                                </a>
                                <div class="dropdown-menu" aria-labelledby="coordinator-dropdown">
                                    <a class="dropdown-item" href="{{route('users')}}">Listar Usuarios</a>
                                    <a class="dropdown-item" href="{{route('pending_operations')}}">Operaciones Pendientes</a>
                                    <a class="dropdown-item" href="{{route('registerOperator')}}">Registrar
                                        Operador</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{route('registerOperatorAccount')}}">Agregar Cuenta
                                        a Operador </a>
                                    <a class="dropdown-item" href="{{route('addFoundsToVenezuelanOperator')}}">Agregar
                                        Fondos a Operador Venezuela</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{route('foreign_operators')}}">Listar Operadores
                                        Extranjero y saldos
                                        disponibles</a>
                                    <a class="dropdown-item" href="{{route('venezuelan_operators')}}">Listar Operadores Venezuela y saldos
                                        disponibles</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{route('transactions_list')}}">Listar Transacciones</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{route('rate')}}">Definir Tasa</a>
                                    <a class="dropdown-item" href="{{route('settings')}}">Configuraciones</a>
                                </div>
                            </li>
                        @endif
                        @if(Auth::user()->hasRole('foreign_operator')||Auth::user()->hasRole('coordinator'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="operator-dropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones de Operador Chile
                                </a>
                                <div class="dropdown-menu" aria-labelledby="operator-dropdown">
                                    <a class="dropdown-item" href="{{route('make_transaction')}}">Registrar
                                        Transacción</a>
                                    <a class="dropdown-item" href="{{route('venezuelan_operators')}}">Listar Operadores Venezuela y saldos
                                        disponibles</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{route('my_transactions')}}">Mis
                                        Transacciones
                                        </a>
                                    <a class="dropdown-item" href
                                    ="{{route('chilean_pending_transactions')}}">Mis Transacciones Pendientes</a>

                                </div>
                            </li>
                        @endif

                        @if(Auth::user()->hasRole('venezuelan_operator')||Auth::user()->hasRole('coordinator'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="operator-dropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones de Operador Venezuela
                                </a>
                                <div class="dropdown-menu" aria-labelledby="operator-dropdown">
                                    <a class="dropdown-item" href="{{route('venezuelan_transactions')}}">Mis Transacciones</a>
                                </div>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="operator-dropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->email }}
                            </a>
                            <div class="dropdown-menu" aria-labelledby="operator-dropdown">
                                <a class="dropdown-item" href="{{route('change_password')}}">Cambio de contraseña</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" @click.prevent="logout">
                                    {{ __('Logout') }}</a>
                            </div>
                        </li>
                    @endguest
                </ul>
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
