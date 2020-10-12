@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('pendingTransactions.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('pendingTransactions.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <pending-transactions-list

                :pending-transactions-query='@json($pendingTransactions)'>

        </pending-transactions-list>
    </section>
@endsection
