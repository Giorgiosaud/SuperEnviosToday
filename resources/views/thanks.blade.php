@extends('layouts.app')
<section class="hero is-success is-fullheight">
    <div class="hero-body">
        <div class="container">
            <h1 class="super-title">
                {{ __('message.THANKS',['app'=>config('app.name')]) }}
            </h1>
            <h2 class="subtitle">
                {{ __('message.THANKS:MESSAGE' ) }}
            </h2>
        </div>
    </div>
</section>
