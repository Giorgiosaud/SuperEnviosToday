@extends('layouts.app')
@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('accounts.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('accounts.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <accounts-list :accounts-query='@json($accounts)'
                       :all-banks='@json($banks)'
                       :all-currencies='@json($currencies)'

                       v-cloak>

        </accounts-list>
    </section>
@endsection
