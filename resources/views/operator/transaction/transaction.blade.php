<transaction-data
    {{ $properties }}
    inline-template>
    <section class="section">
        <nav class="level">
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading" v-if="!tryAnotherRate">{{__('transaction.EXCHANGE.RATE')}}</p>
                    <p class="title" v-if="tryAnotherRate">{{__('transaction.RATE:NEW')}}: @{{ newRate|rateCurrency(selectedCurrency) }}</p>
                    <p :class="{'sub-title':tryAnotherRate,'title':!tryAnotherRate}">
                        <span v-if="tryAnotherRate">{{__('transaction.EXCHANGE.RATE')}}:</span>
                        @{{ exchangeRate|rateCurrency(selectedCurrency) }}
                    </p>
                    <p class="sub-title" v-if="tryAnotherRate"
                       :class="{'has-text-danger':newRate-exchangeRate>0,'has-text-success':newRate-exchangeRate<=0}">
                        Diff: (@{{ newRate-exchangeRate|currency }})</p>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div>
                    <p class="heading">{{__('transaction.RATE:CALC')}}</p>
                    <p class="title">@{{calcExchange|currency({
                        symbol: 'Bs ', precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
                        })}}</p>
                </div>
            </div>
        </nav>
        <validation-observer v-slot="{invalid}" ref="fields" slim>
            <section class="section is-paddingless">

                <div class="columns">
                    <div class="column is-narrow">
                        <validation-provider
                            rules="required"
                            v-slot="{ classes,errors,valid }"
                            name="{{__('transaction.CURRENCY')}}"
                            tag="div"
                            class="control">
                            <label class="label">{{__('transaction.CURRENCY')}}</label>
                            <div class="control has-icons-left has-icons-right">
                                <div class="select"
                                     :class="classes">
                                    <select
                                        id="idn_type"
                                        name="idn_type"
                                        v-model="selectedCurrency">
                                        <option value="">{{__('transaction.DEFAULT:CURRENCY')}}</option>
                                        <option v-for="foreignCurrency in foreignCurrencies"
                                                :key="foreignCurrency.id"
                                                :value="foreignCurrency">@{{foreignCurrency.name}}
                                            (@{{foreignCurrency.identifier}})
                                        </option>
                                    </select>
                                    <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                                </div>
                                <span class="icon is-small is-left">
                            <font-awesome-icon icon="coins"></font-awesome-icon>
                        </span>
                            </div>
                            <strong
                                v-if="errors[0]"
                                class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                    </div>
                    <div class="column is-narrow">
                        <validation-provider
                            rules="required"
                            v-slot="{ classes,errors,valid }"
                            name="{{__('transaction.ACCOUNTS')}}"
                            tag="div"
                            vid="selectedAccount"
                            class="control">
                            <label class="label">{{__('transaction.ACCOUNTS')}}</label>
                            <div class="control has-icons-left has-icons-right">
                                <div class="select"
                                     :class="classes">
                                    <select
                                        :disabled="!accountsOfCurrency.length"
                                        id="idn_type"
                                        name="idn_type"
                                        v-model="selectedAccount">
                                        <option value="">{{__('transaction.DEFAULT:ACCOUNT')}}</option>
                                        <option v-for="account in accountsOfCurrency"
                                                :key="account.id"
                                                :value="account">@{{account.bank.name}} (@{{account.number}})
                                        </option>
                                    </select>
                                    <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                                </div>
                                <span class="icon is-small is-left">
                            <font-awesome-icon icon="piggy-bank"></font-awesome-icon>
                        </span>
                            </div>
                            <strong
                                v-if="errors[0]"
                                class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                    </div>
                    <div class="column is-narrow">
                        <validation-provider
                            rules="required|min_value:{{config('app.min_amount')}}"
                            name="{{__('transaction.AMOUNT')}}"
                            v-slot="{ classes,errors,valid}"
                            tag="div"
                            class="control">
                            <label class="label" for="idn">{{__('transaction.AMOUNT')}}</label>
                            <div class="control has-icons-right">
                                <money id="amount"
                                       v-model="amount"
                                       :disabled="!selectedAccount"
                                       v-bind="clp"
                                       name="amount"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('transaction.AMOUNT')}}"
                                       autocomplete="amount" autofocus></money>
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
                    </div>
                </div>
                <div class="columns">
                    <validation-provider tag="div" class="column is-narrow" vid="tryAnotherRate" v-slot="x">
                        <label class="checkbox">
                            <input type="checkbox" v-model="tryAnotherRate">
                            {{__('transaction.RATE:CHANGE')}}
                        </label>
                    </validation-provider>
                </div>
                <div class="columns" v-if="tryAnotherRate">
                    <div class="column is-narrow">
                        <validation-provider
                            rules="required_if:tryAnotherRate,true|min_value:0.00000001"
                            name="{{__('transaction.RATE:NEW')}}"
                            v-slot="{ classes,errors,valid}"
                            tag="div"
                            class="control">
                            <label class="label" for="idn">{{__('transaction.RATE:NEW')}}</label>
                            <div class="control has-icons-right">
                                <money id="idn"
                                       v-model="newRate"
                                       v-bind="clp"
                                       name="newRate"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('transaction.RATE:NEW')}}"></money>
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
                    </div>
                </div>

            </section>
            <section class="section is-paddingless">
                <div class="columns" v-if="selectedAccount && selectedAccount.bank.name!=='Efectivo'">
                    <div class="column">
                        <validation-provider
                            rules="required"
                            name="{{__('transaction.VOUCHER:NUMBER')}}"
                            v-slot="{ classes,errors,valid}"
                            tag="div"
                            class="control">
                            <label class="label" for="idn">
                                {{__('transaction.VOUCHER:NUMBER')}}
                            </label>
                            <div class="control has-icons-right">
                                <input id="voucher"
                                       v-model="voucher"
                                       name="voucher"
                                       class="input"
                                       :class="classes"
                                       type="text"
                                       placeholder="{{__('transaction.VOUCHER:NUMBER')}}"

                                       autocomplete="idn" autofocus></input>
                                <span class="icon is-small has-text-warning	is-right" v-if="errors[0]">
                                    <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                                </span>
                                <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                            </div>
                            <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                        </validation-provider>
                    </div>
                </div>
                <validation-provider
                    rules="required|min:1"
                    v-if="selectedAccount && selectedAccount.bank.name!=='Efectivo'"
                    name="{{__('transaction.VOUCHER:FILES')}}"
                    v-slot="{ classes,errors,valid}"
                    tag="div"
                    class="control">
                    <uppy-uploader
                        :class="classes"
                        v-model="uploadedFiles"
                        :max-file-size-in-bytes="1000000">
                    </uppy-uploader>
                    <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
                </validation-provider>

            </section>
            <div class="columns has-padding-top-5" v-if="selectedAccount && selectedAccount.bank.name!=='Efectivo'">
                <div class="column">
                    <b-button size="is-big"
                              type="is-info"
                              :loading="verifyingTransaction"
                              icon-right="search-dollar"
                              @click="verifyTransactionNumberAsUnique">
                        {{__('transaction.VERIFY:BUTTON')}}
                    </b-button>
                </div>
                <Validation-provider rules="required|is:ok" name="canGoOn" v-slot="{ errors }">
                    <input type="hidden" v-model="canGoOn">
                </Validation-provider>
            </div>
            <div class="columns has-padding-top-5">
                <div class="column">
                    <b-button size="is-big"
                              type="is-info"
                              :loading="savingTransaction"
                              :disabled="invalid"
                              icon-right="arrow-circle-right"
                              @click="nextStep">
                        {{__('transaction.NEXT:BUTTON')}}
                    </b-button>
                </div>
            </div>
        </validation-observer>
        <b-modal :active.sync="isComponentModalActive"
                 has-modal-card
                 trap-focus
                 :destroy-on-hide="false"
                 aria-role="dialog"
                 aria-modal>
            <transaction-invalid @continuar="continueWithThat" :datos-de-transaccion="datosDeTransaccionRepetida"></transaction-invalid>
        </b-modal>
    </section>
</transaction-data>
