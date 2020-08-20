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
                        <div class="column">
                            <b-taginput
                                v-model="selectedBanks"
                                :data="filteredBanks"
                                autocomplete
                                :allow-new="false"
                                :open-on-focus="true"
                                field="name"
                                icon="label"
                                placeholder="{{__('accounts.SELECT:BANK')}}"
                                @typing="getFilteredBanksTags">
                            </b-taginput>
                        </div>
                    </div>

                    <b-table
                        :data="accounts"
                        :loading="loading"
                        :striped="true"
                        :total="query.total"
                        :opened-detailed="defaultOpenedDetails"
                        detailed
                        :current-page="query.current_page"
                        :per-page="query.per_page"
                        :show-detail-icon="true"
                        detail-key="id"
                        ref="accountsTable"
                        aria-next-label="Next page"
                        aria-previous-label="Previous page"
                        paginated
                        backend-paginatiopn
                        backend-filtering
                        @filters-change="changedFilter"
                        @page-change="changedPage">
                        <template slot-scope="props">
                            <b-table-column field="id" label="ID" width="40" numeric>
                                @{{ props.row.id }}
                            </b-table-column>

                            <b-table-column field="number"
                                            label="{{__('accounts.NUMBER')}}"
                                            searchable>
                                @{{ props.row.number }}
                            </b-table-column>
                            <b-table-column field="name"
                                            label="{{__('accounts.BANK:NAME')}}"
                                            searchable>
                                @{{ props.row.bank.name }}
                            </b-table-column>

                            <b-table-column field="balance" label="{{__('accounts.BALANCE')}}">
                                @{{ props.row.balance |currencyFilter({
                                ...props.row.bank.currency,
                                formatWithSymbol:props.row.bank.currency.format_with_symbol === 1
                                })}}
                            </b-table-column>
                            <b-table-column field="Acciones" label="{{__('accounts.ACTIONS')}}">
                                <button class="button field is-danger"
                                    @click="deleteAccount(props.row.id)">
                                {{__('accounts.DELETE_ACCOUNT')}}

                            </button>
                            </b-table-column>
                        </template>

                        <template slot="detail" slot-scope="props">
                            <section>
                                <div class="title">{{__('accounts.EDIT')}}</div>
                                <b-field label="Name">
                                    <b-input v-model="props.row.name"></b-input>
                                </b-field>
                                <div class="buttons">
                                    <b-button
                                        @click="changeName(props.row.id,props.row.name)"
                                        :loading="savingName"
                                        type="is-info">
                                        Guardar
                                    </b-button>
                                    <b-button
                                        @click="removeAccount(props.row.id)"
                                        :loading="removingAccount"
                                        type="is-danger">
                                        Borrar
                                    </b-button>
                                </div>
                            </section>

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
                    <add-account :currencies='allCurrencies' @account-saved="loadAsyncData"></add-account>
                </b-modal>
            </div>
        </accounts-list>
    </section>
@endsection
