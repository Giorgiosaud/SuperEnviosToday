@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('accounts.WELCOME:SHOW') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('accounts.MESSAGE:SHOW',[ 'app'=>config('app.name'),'account'=>$account->number,'bank'=>$account->bank->name]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <accounts-details
                inline-template
                :account='@json($account)'>

        </accounts-details>
    </section>
@endsection
