@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('banks.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('banks.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <banks-list :banks-query='@json($banks)' :all-currencies='@json($currencies)' inline-template v-cloak>
            <div>
                <section class="section">

                    <div class="columns">
                        <div class="column">
                            <button class="button field is-info"
                                    @click="openAddBankModal">
                                {{__('banks.ADD_BANK')}}

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
                                    placeholder="{{__('banks.SELECT:CURRENCY')}}"
                                    @typing="getFilteredTags">
                            </b-taginput>
                        </div>
                    </div>

                    <b-table
                            :data="banks"
                            :loading="loading"
                            :striped="true"
                            :total="query.total"
                            :opened-detailed="defaultOpenedDetails"
                            detailed
                            :current-page="query.current_page"
                            :per-page="query.per_page"
                            :show-detail-icon="true"
                            detail-key="id"
                            ref="banksTable"
                            aria-next-label="Next page"
                            aria-previous-label="Previous page"
                            paginated
                            backend-paginatiopn
                            backend-filtering
                            @filters-change="changedFilter"
                            @page-change="changedPage">

                        <b-table-column field="id"
                                        label="ID"
                                        width="40"
                                        numeric
                                        v-slot="props">
                            @{{ props.row.id }}
                        </b-table-column>

                        <b-table-column field="name"
                                        label="{{__('banks.NAME')}}"
                                        searchable
                                        v-slot="props">
                            @{{ props.row.name }}
                        </b-table-column>

                        <b-table-column field="currency"
                                        label="{{__('banks.CURRENCY')}}"
                                        v-slot="props">
                            @{{ props.row.currency.name }}
                        </b-table-column>

                        <template slot="detail" slot-scope="props">
                            <section>
                                <div class="title">{{__('banks.EDIT')}}</div>
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
                                            @click="removeBank(props.row.id)"
                                            :loading="removingBank"
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
                    <add-bank :currencies='allCurrencies' @bank-saved="loadAsyncData"></add-bank>
                </b-modal>
            </div>
        </banks-list>
    </section>
@endsection
