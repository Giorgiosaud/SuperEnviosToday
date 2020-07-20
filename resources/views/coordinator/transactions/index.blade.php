@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('transaction.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('transaction.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <transactions
            inline-template
            :transactions-query='@json($transactions)'
            :currencies='@json($currencies)'
            :currency='@json($currency)'>
            <section>
                <div class="columns">
                    <div class="column">
                        <b-select v-model="statusFilter" placeholder="Seleccione estado">
                            <option value="">{{__('transaction.STATUS:ALL')}}</option>
                            <option value="pending">{{__('transaction.STATUS:PENDING')}}</option>
                            <option value="executed">{{__('transaction.STATUS:APPROVED')}}</option>
                            <option value="in-progress">{{__('transaction.STATUS:IN:PROGRESS')}}</option>
                        </b-select>
                    </div>
                    <div class="column">
                        <b-select v-model="selectedCurrencyId" placeholder="Seleccione una moneda estado">
                            <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
                                @{{currency.name}}
                            </option>
                        </b-select>
                    </div>
                    <div class="column is-narrow">
                        <b-button type="is-info" outlined @click="refreshData">{{__('transaction.REFRESH')}}</b-button>
                    </div>
                </div>

                <b-table
                    :data="transactions"
                    :total="query.total"
                    :current-page="query.current_page"
                    :loading="loading"
                    :per-page="query.perPage"
                    @click="transactionClicked"
                    paginated
                    backend-pagination
                    backend-filtering
                    :striped="true"
                    :scrollable="true"
                    @page-change="changedPage"
                    @filters-change="changedFilter"
                    aria-next-label="Next page"
                    aria-previous-label="Previous page"
                    :selected.sync="selected">

                    <template slot-scope="props">
                        <b-table-column field="id"
                                        label="ID"
                                        width="40"
                                        numeric
                                        sticky>
                            @{{ props.row.id }}
                        </b-table-column>
                        <b-table-column field="bank_reference"
                                        label="{{__('transaction.BANK:REFERENCE')}}"
                                        width="200"
                        >
                            @{{ props.row.bank_reference}}
                        </b-table-column>
                        <b-table-column field="track_number"
                                        label="{{__('transaction.TRACKING:NUMBER')}}"
                                        width="200"
                        >
                            @{{ props.row.track_number}}
                        </b-table-column>
                        <b-table-column field="operator"
                                        label="{{__('transaction.OPERATOR')}}"
                                        width="200"
                        >
                            @{{ props.row.operator.name}} @{{ props.row.operator.last_name}}
                        </b-table-column>
                        <b-table-column field="client_id"
                                        label="{{__('transaction.CLIENT')}}"
                                        width="200"
                                        v-if="props.row.client"
                        >
                            @{{ props.row.client.name}} @{{ props.row.client.last_name}}
                        </b-table-column>
                        <b-table-column field="client_id"
                                        label="{{__('transaction.CLIENT')}}"
                                        width="200"
                                        v-else
                        >
                            N/A
                        </b-table-column>
                        <b-table-column field="amount"
                                        label="{{__('transaction.AMOUNT')}}">
                            @{{ props.row.amount |currency(props.row.account.bank.currency)}}
                        </b-table-column>


                    </template>
                    <template slot="empty">
                        <section class="section">
                            <div class="content has-text-grey has-text-centered">
                                <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
                                <p>No hay datos coincidentes.</p>
                            </div>
                        </section>
                    </template>
                </b-table>
            </section>
        </transactions>
    </section>
@endsection
