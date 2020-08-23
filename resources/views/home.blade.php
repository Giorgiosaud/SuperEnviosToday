@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
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
                <p class="subtitle is-3">Respecto a Las Transacciones</p>
                <div class="buttons">
                    <a href="#" class="button is-primary" disabled>Crear Transaccion de ajuste</a>
                </div>
            </article>
        @endif
    </section>
@endsection
