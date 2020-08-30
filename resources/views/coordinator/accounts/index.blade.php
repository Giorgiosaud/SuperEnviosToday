@extends('layouts.app')
@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('accounts.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('accounts.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <accounts-list :accounts-query='@json($accounts)'
                       :all-banks='@json($banks)'
                       :all-currencies='@json($currencies)'
                       inline-template
                       v-cloak>
            <div>

                <section class="section">

                    <div class="columns">
                        <div class="column">
                            <button class="button field is-info"
                                    @click="openAddAccountModal">
                                {{__('accounts.ADD_ACCOUNT')}}

                            </button>
                        </div>
                        <div class="column">
                            <b-taginput
                                    v-model="selectedCurrencies"
                                    :data="filteredCurrencies"
                                    autocomplete
                                    :allow-new="false"
                                    :open-on-focus="true"
                                    field="name"
                                    icon="label"
                                    placeholder="{{__('accounts.SELECT:CURRENCY')}}"
                                    @typing="getFilteredCurrenciesTags">
                            </b-taginput>
                        </div>

                    </div>

                    <b-table
                            :data="accounts"
                            :loading="loading"
                            :striped="true"
                            :total="query.total"
                            :current-page="query.current_page"
                            :per-page="query.per_page"
                            ref="accountsTable"
                            aria-next-label="Next page"
                            aria-previous-label="Previous page"
                            paginated
                            backend-paginatiopn
                            backend-filtering
                            @filters-change="changedFilter"
                            @page-change="changedPage"
                            detailed>
                        <template #default="{row:account}">
                            <b-table-column field="id" label="ID" width="40" numeric>
                                @{{ account.id }}
                            </b-table-column>

                            <b-table-column field="number"
                                            label="{{__('accounts.NUMBER')}}">
                                @{{ account.number }}
                            </b-table-column>

                            <b-table-column field="name"
                                            label="{{__('accounts.BANK:NAME')}}">
                                @{{ account.bank.name }}
                            </b-table-column>
                            <b-table-column field="name"
                                            label="{{__('accounts.BANK:CURRENCY')}}">
                                @{{ account.bank.currency.name }}
                            </b-table-column>
                            <b-table-column field="balance" label="{{__('accounts.BALANCE')}}">
                                @{{ account.balance |currencyFilter({
                                ...account.bank.currency,
                                formatWithSymbol:account.bank.currency.format_with_symbol === 1
                                })}}
                            </b-table-column>
                            <b-table-column field="Acciones" label="{{__('accounts.ACTIONS')}}">
                                <div class="buttons">

                                    <button class="button field is-danger"
                                            @click="deleteAccount(account.id)">
                                        {{__('accounts.DELETE_ACCOUNT')}}

                                    </button>
                                    <a class="button field is-info"
                                       :href="`/accounts/${account.id}`">
                                        {{__('accounts.CREATE_ADJUSTMENT_TRANSACTION')}}
                                    </a>
                                </div>
                            </b-table-column>
                        </template>
                        <template #detail="{row:account}">
                            <article>
                                <header>
                                    <h1>
                                        Operadores Asociados a esta cuenta
                                    </h1>
                                </header>
                                <b-table
                                        :data="account.owners">
                                    <template #default="{row:owner}">
                                        <b-table-column field="idn_type" label="Tipo de identificación">
                                            @{{ owner.idn_type }}
                                        </b-table-column>
                                        <b-table-column field="idn" label="Número">
                                            @{{ owner.idn }}
                                        </b-table-column>
                                        <b-table-column field="name" label="Nombres">
                                            @{{ owner.name }}
                                        </b-table-column>
                                        <b-table-column field="last_name" label="Apellidos">
                                            @{{ owner.last_name }}
                                        </b-table-column>
                                        <b-table-column field="phone" label="Teléfono">
                                            @{{ owner.phone }}
                                        </b-table-column>

                                        <b-table-column field="email" label="Email">
                                            @{{owner.email }}
                                        </b-table-column>
                                        <b-table-column field="remove" label="Accion">
                                            <b-button type="is-danger" @click="unBind(owner,account)">Desasociar
                                            </b-button>
                                        </b-table-column>
                                    </template>
                                </b-table>

                                <footer>
                                    <b-button @click="asociateToAccount(account,account.owners)">asociar operador
                                    </b-button>
                                </footer>
                            </article>
                        </template>
                        <template #empty>
                            <section class="section">
                                <div class="content has-text-grey has-text-centered">
                                    <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
                                    <p>No hay datos coincidentes.</p>
                                </div>
                            </section>
                        </template>
                    </b-table>
                </section>
                <b-modal
                        :active.sync="isOpenModal"
                        has-modal-card
                        trap-focus
                        :destroy-on-hide="false"
                        aria-role="dialog"
                        aria-modal>
                    <add-account v-if="modal === 'account'" :currencies='allCurrencies' :banks="allBanks"
                                 @account-saved="loadAsyncData"></add-account>
                    <add-operator v-else :account="accountToAsociate" :actual-owners="actualOwnersOfAccount"
                                  @add-operator-to-account="addOperatorToAccount"></add-operator>

                </b-modal>
            </div>
        </accounts-list>
    </section>
@endsection
