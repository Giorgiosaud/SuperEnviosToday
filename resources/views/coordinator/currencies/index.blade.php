@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('currencies.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('currencies.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <currencies-list
                :currencies-query='@json($currencies)'
                v-cloak>

        </currencies-list>
    </section>
@endsection
