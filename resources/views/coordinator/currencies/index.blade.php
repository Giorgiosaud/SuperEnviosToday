@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('currencies.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('currencies.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <currencies-list :currencies-query='@json($currencies)' inline-template v-cloak>
            <div>
                <section class="section">

                    <div class="columns">
                        <div class="column">
                            <button class="button field is-info"
                                    @click="openAddCurrencyModal">
                                {{__('currency.ADD_CURRENCY')}}

                            </button>
                        </div>
                    </div>

                    <b-table
                        :data="currencies"
                        :loading="loading"
                        :striped="true"
                        :total="query.total"
                        :opened-detailed="defaultOpenedDetails"
                        detailed
                        :current-page="query.current_page"
                        :per-page="query.per_page"
                        :show-detail-icon="true"
                        detail-key="id"
                        ref="currenciesTable"
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

                            <b-table-column field="name"
                                            label="{{__('currency.NAME')}}"
                                            searchable>
                                @{{ props.row.name }}
                            </b-table-column>

                            <b-table-column field="identifier" label="{{__('currency.CURRENCY')}}">
                                @{{ props.row.identifier }}
                            </b-table-column>
                            <b-table-column field="sign" label="{{__('currency.CURRENCY')}}">
                                @{{ props.row.sign }}
                            </b-table-column>
                        </template>

                        <template slot="detail" slot-scope="props">
                            <section>
                                <b-field label="Name">
                                    <b-input v-model="props.row.name"></b-input>
                                </b-field>
                                <b-field label="identifier">
                                    <b-input v-model="props.row.identifier"></b-input>
                                </b-field>
                                <b-field label="sign">
                                    <b-input v-model="props.row.sign"></b-input>
                                </b-field>
                                <div class="buttons">
                                    <b-button
                                        @click="changeCurrency(props.row.id,props.row.name,props.row.identifier,props.row.sign)"
                                        :loading="savingCurrency"
                                        type="is-info">
                                        Guardar
                                    </b-button>
                                    <b-button
                                        @click="removeCurrency(props.row.id)"
                                        :loading="removingCurrency"
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
                    <add-currency @currency-created="loadAsyncData"></add-currency>
                </b-modal>
            </div>
        </currencies-list>
    </section>
@endsection
