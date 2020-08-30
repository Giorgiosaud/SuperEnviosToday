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
            <article>
                <div class="columns">
                    <div class="column">
                        <a href="/accounts" class="is-link"><< Ir al listado de cuentas</a>
                    </div>
                </div>
                <validation-observer v-slot="{invalid}">
                    <div class="columns">
                        <div class="column">
                            {{__('accounts.BANK:NAME')}}
                        </div>
                        <div class="column">
                            @{{ account.bank.name }}
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            {{__('accounts.NUMBER')}}
                        </div>
                        <div class="column">
                            @{{ account.number }}
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            {{__('accounts.BALANCE')}}
                        </div>
                        <div class="column">
                            @{{ account.balance | currencyFilter({
                            ...account.bank.currency,
                            formatWithSymbol:account.bank.currency.format_with_symbol === 1
                            }) }}
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <validation-provider rules="required" v-slot="{ errors }" name="{{__('transaction.TYPE')}}">
                                <label for="transaction-type">{{__('transaction.TYPE')}}</label>
                                <b-select id="transaction-type" v-model="transactionType"
                                          placeholder="{{__('transaction.TYPE')}}" expanded>
                                    <option value="income">{{__('transaction.INCOME')}}</option>
                                    <option value="outcome">{{__('transaction.OUTCOME')}}</option>
                                </b-select>
                                <span id="is-error">@{{ errors[0] }}</span>

                            </validation-provider>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <validation-provider :rules="`required|decimals:2|balance:${newBalance}`"
                                                 v-slot="{ errors,classes }"
                                                 name="{{__('transaction.AMOUNT')}}">
                                <b-field label="{{__('transaction.AMOUNT')}}"
                                         :type="classes"
                                         :message="errors[0]">
                                    <b-input
                                            id="amount"
                                            expanded
                                            :custom-class="classes['is-danger']?'is-danger':''"
                                            v-model="amount"
                                            :disabled="!transactionType"
                                            type="text"
                                            placeholder="{{__('transaction.AMOUNT')}}"
                                    ></b-input>
                                </b-field>
                            </validation-provider>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label for="comments"></label>
                                <quill-editor class="textarea"

                                              id="comments"
                                              v-model="comment"></quill-editor>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <strong>{{__('accounts.NEW:BALANCE')}}</strong>
                        </div>
                        <div class="column">
                            @{{ newBalance | currencyFilter({
                            ...account.bank.currency,
                            formatWithSymbol:account.bank.currency.format_with_symbol === 1
                            }) }}
                        </div>
                    </div>
                    <div class="columns">
                        <b-button expanded
                                  :disabled="invalid"
                                  @click="executeTransaction"
                                  type="is-info">Ejecutar
                        </b-button>
                    </div>
                </validation-observer>
            </article>
        </accounts-details>
    </section>
@endsection
