@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    {{ __('auth.FORGET') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('auth.FORGET:MESSAGE',['name' => config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <forget-form inline-template>
        <section class="section">
            <div class="container">
                <validation-observer ref="form">
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <validation-provider
                            rules="required"
                            v-slot="{ classes,errors,valid }"
                            name="{{__('auth.IDN_TYPE')}}"
                            tag="div"
                            class="field">
                            <label class="label">{{__('auth.IDN_TYPE')}}</label>
                            <div class="control has-icons-left has-icons-right">
                                <div class="select"
                                     :class="classes">
                                    <select
                                        id="idn_type"
                                        name="idn_type"
                                        v-model="idnType">
                                        <option value="">{{__('auth.DEFAULT:IDNTYPE')}}</option>
                                        <option value="CI">Cédula Venezolana</option>
                                        <option value="PASSPORT">Pasaporte</option>
                                        <option value="RUT">RUT</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RIF">RIF</option>
                                    </select>
                                    <input type="hidden"
                                           ref="idn_type"
                                           value="{{ old('idn_type') }}">
                                    <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                                </div>
                                <span class="icon is-small is-left">
                            <font-awesome-icon icon="passport"></font-awesome-icon>
                        </span>
                            </div>
                            <strong
                                v-if="errors[0]"
                                class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                        <validation-provider
                            rules="required"
                            name="{{__('auth.IDN')}}"
                            v-slot="{ classes,errors,valid}"
                            tag="div"
                            class="field">
                            <label class="label" for="idn">{{__('auth.IDN')}}</label>
                            <div class="control has-icons-right">
                                <input id="idn"
                                       v-model="idn"
                                       name="idn"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('auth.IDN')}}"
                                       value="123"
                                       autocomplete="name" autofocus>

                                <input type="hidden"
                                       ref="idn"
                                       value="{{ old('idn') }}">
                                <span class="icon is-small has-text-warning	is-right"
                                      v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                            </div>
                            <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                        <validation-provider
                            name="{{__('auth.EMAIL')}}"
                            rules="required|email"
                            v-slot="{ classes,errors,valid }"
                            tag="div"
                            class="field">

                            <label class="label" for="email">{{__('auth.EMAIL')}}</label>
                            <div class="control has-icons-left has-icons-right">
                                <input id="email" name="email"
                                       :class="classes"
                                       class="input"
                                       type="text"
                                       v-model="email"
                                       placeholder="{{__('auth.EMAIL')}}"
                                       autocomplete="email" autofocus>
                                <input type="hidden"
                                       ref="email"
                                       value="{{ old('email') }}">
                                <span class="icon is-small is-left">
                                <font-awesome-icon icon="envelope"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-warning	is-right" v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                            </div>
                            <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                        <div class="field is-grouped">
                            <div class="control">
                                <button type="submit" class="button is-link">{{ __('auth.SEND:RESET:EMAIL') }}</button>
                            </div>
                        </div>
                    </form>
                </validation-observer>
            </div>
        </section>
    </forget-form>
@endsection
