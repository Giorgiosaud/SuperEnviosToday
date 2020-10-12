@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('settings.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('settings.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <settings-list
                :settings-query='@json($settings)'
                v-cloak>
        </settings-list>
    </section>
@endsection
