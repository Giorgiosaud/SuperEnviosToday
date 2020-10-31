@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('transaction.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('transaction.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <my-transactions
                :transactions-query='@json($transactions)'
                app-url="{{Config::get('app.url')}}"
                :currencies='@json($currencies)'
                :currency='@json($currency)'>

        </my-transactions>
    </section>
@endsection
