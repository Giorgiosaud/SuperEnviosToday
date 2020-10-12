@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('users.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('users.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        @if($users && $roles)
            <users-list :users-query='@json($users)' :all-roles='@json($roles)'  v-cloak>
            </users-list>
        @endif
    </section>
@endsection
