@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('users.SINGLE:WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('users.SIGLE:USER:DATA',[ 'name'=>$user->name,'last_name'=>$user->last_name]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <user-detail
                :user='@json($user)'
                :accounts='@json($accounts)'
                :all-roles='@json($roles)'
                edit-link="{{route('users.index')}}"
                form-action="{{ route('users.update',$user->id) }}"
                v-cloak>
            <template #csrf>
                <div>
                @csrf
                </div>
            </template>
        </user-detail>
    </section>
@endsection
