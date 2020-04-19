@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
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
            :all-roles='@json($roles)'
            inline-template v-cloak>
            <div>
                <button class="button field is-info"
                        v-if="!editable"
                        @click="editable = true">
                    <span>{{__('users.EDIT')}}</span>
                </button>
                <a class="button field is-danger"
                   v-if="!editable"
                   href="{{route('users.index')}}"
                >
                    <span>{{__('users.LIST')}}</span>
                </a>
                <div v-if="!editable">
                    <div><strong>{{__('auth.IDN_TYPE')}}:</strong> @{{ userData.idn_type }}</div>
                    <div><strong>{{__('auth.IDN')}}:</strong> @{{ userData.idn }}</div>
                    <div><strong>{{__('auth.NAME')}}:</strong> @{{ userData.name }}</div>
                    <div><strong>{{__('auth.LAST_NAME')}}:</strong> @{{ userData.last_name }}</div>
                    <div><strong>{{__('auth.EMAIL')}}:</strong> @{{ userData.email }}</div>
                    <div><strong>{{__('auth.PHONE')}}:</strong> @{{ userData.phone }}</div>
                    <div><strong>{{__('auth.ADDRESS')}}:</strong> @{{ userData.address }}</div>
                    <div><strong>{{__('users.ROLES')}}:</strong> <span class="tag"
                                                                       v-for="role in userData.roles"
                                                                       :class="{
                                      'is-danger':role.name_id==='coordinator',
                                      'is-warning':role.name_id==='foreign_operator',
                                      'is-success':role.name_id==='receiver',
                                      'is-info':role.name_id==='venezuelan_operator',
                                      'is-primary':role.name_id==='client'
                                      }">
                                   @{{ role.name}}
                                </span></div>
                </div>
                <div v-else>
                    <validation-observer ref="form">
                        <form method="POST"
                              action="{{ route('users.update',$user->id) }}">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <validation-provider
                                rules="required"
                                v-slot="{ classes,errors,valid }"
                                name="{{__('users.ROLES')}}"
                                tag="div"
                                class="field">
                                <label class="label">{{__('users.ROLES')}}</label>
                                <div class="control has-icons-right">
                                    <b-taginput
                                        v-model="userData.roles"
                                        :data="filteredRoles"
                                        autocomplete
                                        :allow-new="false"
                                        :open-on-focus="true"
                                        field="name"
                                        icon="label"
                                        placeholder="{{__('users.SELECT:ROLE')}}"
                                        @typing="getFilteredTags">
                                    </b-taginput>
                                </div>

                                <strong
                                    v-if="errors[0]"
                                    class="help is-danger">@{{errors[0]}}</strong>
                            </validation-provider>
                            <input type="text"
                                   name="roles"
                                   v-model="roleNames">
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
                                            v-model="userData.idn_type">
                                            <option value="">{{__('auth.DEFAULT:IDNTYPE')}}</option>
                                            <option value="CI">Cédula Venezolana</option>
                                            <option value="PASSPORT">Pasaporte</option>
                                            <option value="RUT">RUT</option>
                                            <option value="DNI">DNI</option>
                                            <option value="RIF">RIF</option>
                                        </select>
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
                                           v-model="userData.idn"
                                           name="idn"
                                           class="input"
                                           :class="classes"
                                           type="text"
                                           placeholder="{{__('auth.IDN')}}"
                                           autocomplete="idn" autofocus>

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
                                rules="required"
                                v-slot="{ classes,errors, valid }"
                                tag="div"
                                class="field">

                                <label class="label" for="name">{{__('auth.NAME')}}</label>
                                <div class="control has-icons-right">
                                    <input id="name"
                                           v-model="userData.name"
                                           name="name"
                                           class="input"
                                           :class="classes"
                                           type="text"
                                           placeholder="{{__('auth.NAME')}}"
                                           autocomplete="name" autofocus>
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
                                           v-model="userData.lastName"
                                           class="input"
                                           :class="classes"
                                           type="text"
                                           placeholder="{{__('auth.LAST_NAME')}}"
                                           autocomplete="last_name" autofocus>
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
                                           v-model="userData.email"
                                           placeholder="{{__('auth.EMAIL')}}"
                                           autocomplete="email" autofocus>
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
                                           v-model="userData.phone"
                                           placeholder="{{__('auth.PHONE')}}"
                                           autocomplete="phone" autofocus>
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

                                <label class="label" for="address">{{__('auth.ADDRESS')}}</label>
                                <div class="control has-icons-right">
                            <textarea id="address"
                                      name="address"
                                      class="textarea"
                                      :class="classes"
                                      type="text"
                                      v-model="userData.address"
                                      rows="3"
                                      placeholder="{{__('auth.ADDRESS')}}"
                                      autocomplete="phone" autofocus></textarea>
                                    <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                                </div>
                                <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                            </validation-provider>
                            <div class="field is-grouped">
                                <div class="control">
                                    <button type="submit" class="button is-primary">{{ __('users.UPDATE') }}</button>
                                </div>
                                <div class="control">
                                    <button @click.prevent="cancel"
                                            class="button is-danger">{{ __('users.CANCEL') }}</button>
                                </div>
                            </div>
                        </form>
                    </validation-observer>
                </div>
            </div>
        </user-detail>
    </section>
@endsection
