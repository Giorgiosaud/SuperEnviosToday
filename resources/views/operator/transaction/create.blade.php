@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    {{ __('transaction.TITLE') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('transaction.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <transaction-create inline-template>
            <section>
                <b-steps
                    size="is-large"
                    :has-navigation="false"
                    :animated="true">
                    <b-step-item label="{{__('transaction.CLIENT:TITLE')}}" icon="user-edit">
                        {{__('transaction.CLIENT:TITLE')}}
                        <div class="field is-grouped">

                        <validation-provider
                            rules="required"
                            v-slot="{ classes,errors,valid }"
                            name="{{__('auth.IDN_TYPE')}}"
                            tag="div"
                            class="control">
                            <label class="label">{{__('auth.IDN_TYPE')}}</label>
                            <div class="control has-icons-left has-icons-right">
                                <div class="select"
                                     :class="classes">
                                    <select
                                        id="idn_type"
                                        name="idn_type"
                                        v-model="client.idn_type">
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
                            class="control">
                            <label class="label" for="idn">{{__('auth.IDN')}}</label>
                            <div class="control has-icons-right">
                                <input id="idn"
                                       v-model="client.idn"
                                       name="idn"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('auth.IDN')}}"
                                       value="123"
                                       autocomplete="idn" autofocus>

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
                        <div class="control">
                            <label data-v-402394c6="" for="idn" class="label">{{__('transaction.SEARCH:LABEL')}}</label>
                            <b-button size="is-big"
                                      type="is-info"
                                      :disabled="client.idn===''||client.idn_type===''"
                                      icon-left="search"
                                      @click="searchClient">
                                @{{searchButtonText}}
                            </b-button>
                        </div>
                    </div>

                    <section class="section" v-if="searchingClient">
                        <div class="skeleton">
                            <b-skeleton width="80%" animated></b-skeleton>

                            <b-skeleton width="80%" animated></b-skeleton>

                            <b-skeleton width="80%" animated></b-skeleton>

                            <b-skeleton animated></b-skeleton>
                        </div>
                    </section>
                    <section class="section">
                        <validation-provider
                            rules="required"
                            v-slot="{ classes,errors, valid }"
                            tag="div"
                            class="field">

                            <label class="label" for="name">{{__('auth.NAME')}}</label>
                            <div
                                v-if="showCreateClientForm"
                                class="control has-icons-right">
                                <input id="name"
                                       v-model="name"
                                       name="name"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('auth.NAME')}}"
                                       autocomplete="name" autofocus>

                            </div>
                            <div v-else>
                                @{{  }}
                            </div>
                            <input type="hidden" ref="name" value="{{ old('name') }}">
                            <span class="icon is-small has-text-warning	is-right"
                                  v-if="errors[0]">
                            <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                            </div>
                            <strong
                                v-if="errors[0]"
                                class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>

                        <validation-provider
                            name="{{__('auth.LAST_NAME')}}"
                            rules="required"
                            v-slot="{ classes,errors, valid }"
                            tag="div"
                            class="field">
                            <label
                                class="label"
                                for="last_name">{{__('auth.LAST_NAME')}}</label>
                            <div class="control has-icons-right">
                                <input id="last_name"
                                       name="last_name"
                                       v-model="lastName"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('auth.LAST_NAME')}}"
                                       autocomplete="last_name" autofocus>
                                <input type="hidden"
                                       ref="lastName"
                                       value="{{ old('last_name') }}">
                                <span class="icon is-small has-text-warning	is-right"
                                      v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                            </div>
                            <strong
                                v-if="errors[0]"
                                class="help is-danger">@{{errors[0]}}</strong>
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
                        <validation-provider
                            name="{{__('auth.PHONE')}}"
                            rules="alpha_dash"
                            v-slot="{ classes,errors,valid }"
                            tag="div"
                            class="field">

                            <label class="label" for="email">{{__('auth.PHONE')}}</label>
                            <div class="control has-icons-left has-icons-right">
                                <input id="phone" name="phone"
                                       :class="classes"
                                       class="input"
                                       type="text"
                                       v-model="phone"
                                       placeholder="{{__('auth.PHONE')}}"
                                       autocomplete="phone" autofocus>
                                <input type="hidden"
                                       ref="phone"
                                       value="{{ old('phone') }}">
                                <span class="icon is-small is-left">
                                <font-awesome-icon icon="phone"></font-awesome-icon>
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
                        <validation-provider
                            name="{{__('auth.ADDRESS')}}"
                            rules="alpha_dash"
                            v-slot="{ classes,errors,valid }"
                            tag="div"
                            class="field">

                            <label class="label" for="email">{{__('auth.ADDRESS')}}</label>
                            <div class="control has-icons-right">
                            <textarea id="address"
                                      name="address"
                                      class="textarea"
                                      :class="classes"
                                      type="text"
                                      v-model="address"
                                      rows="3"
                                      placeholder="{{__('auth.ADDRESS')}}"
                                      autocomplete="phone" autofocus></textarea>
                                <input type="hidden"
                                       ref="address"
                                       value="{{ old('address') }}">
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                            </div>
                            <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                    </section>
                    <section class="section" v-if="showClientData">
@{{ obtainedClient.name }}
                    </section>

                    </b-step-item>
                    <b-step-item :clickable="clientReady" label="{{__('transaction.TRANSACTION:TITLE')}}" icon="money-check-alt">
                        {{__('transaction.TRANSACTION:TITLE')}}
                    </b-step-item>
                    <b-step-item label="{{__('transaction.RECEIVER:TITLE')}}" icon="hand-holding-usd">
                        {{__('transaction.RECEIVER:TITLE')}}
                    </b-step-item>
                    <b-step-item label="{{__('transaction.VENEZUELAN_OPERATOR:TITLE')}}" icon="comment-dollar">
                        {{__('transaction.VENEZUELAN_OPERATOR:TITLE')}}
                    </b-step-item>
                    <b-step-item label="{{__('transaction.REVIEW:TITLE')}}" icon="file-invoice-dollar">
                        {{__('transaction.REVIEW:TITLE')}}
                    </b-step-item>

                </b-steps>
            </section>
        </transaction-create>
    </section>
@endsection
