@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    {{ __('message.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('message.WELCOME:MESSAGE',['name' => Auth::user()->name, 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        @if(Auth::user()->hasRole('coordinator') )
            <article class="">
                <h1 class="title is-1">Acciones de los Coordinadores</h1>
                <p class="subtitle is-3">Respecto a los usuarios</p>
                <div class="buttons">
                    <a href="{{route('users.index')}}" class="button is-primary">Lista de usuarios</a>
                    <a href="{{route('users.index')}}" class="button is-primary" disabled>Operaciones Pendientes</a>
                    <!--a href="#" class="button is-primary" disabled>Registrar Operador</a-->
                </div>
                <h1 class="subtitle is-3">Respecto a las cuentas</h1>
                <div class="buttons">

                    <a href="#" class="button is-primary" disabled>Agregar Cuenta</a>
                    <a href="#" class="button is-primary" disabled>Asignar Operador a Cuenta</a>
                    <a href="#" class="button is-primary" disabled>Agregar Fondos a Cuenta en Venezuela</a>
                    <a href="#" class="button is-primary" disabled>Listar cuentas, saldos y operadores asociados a cuentas extranjeras</a>
                    <a href="#" class="button is-primary" disabled>Listar cuentas, saldos y operadores asociados a cuentas en Venezurela</a>

                </div>
                <p class="subtitle is-3">Respecto a Las Transacciones</p>
                <div class="buttons">

                    <a href="#" class="button is-primary" disabled>Listar Transacciones</a>
                    <a href="#" class="button is-primary" disabled>Crear Transaccion de ajuste</a>
                </div>
                <p class="subtitle is-3">Respecto a La Tasa</p>
                <div class="buttons">

                    <a href="#" class="button is-primary" disabled>Definir Tasa</a>
                </div>
                <p class="subtitle is-3">Otros</p>
                <div class="buttons">

                    <a href="#" class="button is-primary" disabled>Configuraciones</a>
                </div>
            </article>
        @endif
    </section>
@endsection
