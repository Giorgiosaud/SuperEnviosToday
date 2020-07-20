@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('transaction.WELCOME:ADJUST') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('transaction.MESSAGE:ADJUST',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">

    </section>
@endsection
