@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('banks.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('banks.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <banks-list :banks-query='@json($banks)'
                    :all-currencies='@json($currencies)'
                    v-cloak>

        </banks-list>
    </section>
@endsection
