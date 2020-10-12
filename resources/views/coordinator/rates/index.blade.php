@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('rates.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('rates.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <rates-list :rates-query='@json($rates)'
                    :all-currencies='@json($currencies)'
                    v-cloak>
        </rates-list>
    </section>
@endsection
