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
        <div class="columns">
            <div class="column">
              @can('create-transaction')
                <a class="button is-primary is-light" href="{{route('transaction.create')}}">{{__('transaction.MENU:CREATE')}}</a>
              @endcan
            </div>
        </div>
    </section>
@endsection
