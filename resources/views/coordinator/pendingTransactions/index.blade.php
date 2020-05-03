@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    {{ __('pendingTransactions.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('pendingTransactions.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
    <pending-transactions
        inline-template
        :pending-transactions-query='@json($pendingTransactions)'>
        <section>
                <b-field label="{{__('pendingTransactions.STATUS')}}">
                    <b-select v-model="statusFilter" placeholder="Seleccione estado">
                        <option value="">{{__('pendingTransactions.STATUS:ALL')}}</option>
                        <option value="pending">{{__('pendingTransactions.STATUS:PENDING')}}</option>
                        <option value="aprooved">{{__('pendingTransactions.STATUS:APPROVED')}}</option>
                        <option value="rejected">{{__('pendingTransactions.STATUS:REJECTED')}}</option>
                    </b-select>
                </b-field>
            <b-table
                :data="pendingTransactions"
                :total="query.total"
                :current-page="query.current_page"
                :loading="loading"
                :per-page="query.perPage"
                @click="pendingTransactionClicked"
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
                    <b-table-column field="date"
                                    label="{{__('pendingTransactions.DATE')}}"
                                    width="200"
                    >
                        @{{ props.row.created_at |date}}
                    </b-table-column>

                    <b-table-column field="client"
                                    label="{{__('pendingTransactions.CLIENT:NAME_AND_LAST_NAME')}}"
                                    >
                        <a title="{{__('pendingTransactions.Client:EXTRA_DATA')}}"
                           :href="`/users/${props.row.client.id}`"
                           target="_blank"
                        >@{{ props.row.client.name }} @{{ props.row.client.last_name }}
                        </a>

                    </b-table-column>
                    <b-table-column field="foreign_operator"
                                    label="{{__('pendingTransactions.FOREIGN_OPERATOR:NAME_AND_LAST_NAME')}}"
                                    >
                        <a title="{{__('pendingTransactions.FOREIGN_OPERATOR:EXTRA_DATA')}}"
                           :href="`/users/${props.row.foreign_operator.id}`"
                           target="_blank"
                        >@{{ props.row.foreign_operator.name }} @{{ props.row.foreign_operator.last_name }}
                        </a>

                    </b-table-column>
                    <b-table-column field="venezuelan_operator"
                                    label="{{__('pendingTransactions.VENEZUELAN:NAME_AND_LAST_NAME')}}"
                                    >
                        <a title="{{__('pendingTransactions.VENEZUELAN:EXTRA_DATA')}}"
                           :href="`/users/${props.row.venezuelan_operator.id}`"
                           target="_blank"
                        >@{{ props.row.venezuelan_operator.name }} @{{ props.row.venezuelan_operator.last_name }}
                        </a>

                    </b-table-column>
                    <b-table-column field="foreign_bank"
                                    label="{{__('pendingTransactions.FOREIGN:BANK')}}"
                                    >
                        <a title="{{__('pendingTransactions.VENEZUELAN:EXTRA_DATA')}}"
                           :href="`/accounts/${props.row.foreign_account.id}`"
                           target="_blank"
                        >@{{ props.row.foreign_account.bank.name }}</a>
                    </b-table-column>
                    <b-table-column field="venezuelan_bank_from"
                                    label="{{__('pendingTransactions.VENEZUELAN:BANK_FROM')}}"
                                    >
                        <a title="{{__('pendingTransactions.VENEZUELAN:EXTRA_DATA')}}"
                           :href="`/accounts/${props.row.operator_account.id}`"
                           target="_blank"
                        >
                        @{{ props.row.operator_account.bank.name }}
                        </a>
                    </b-table-column>
                    <b-table-column field="venezuelan_bank_to"
                                    label="{{__('pendingTransactions.VENEZUELAN:BANK_TO')}}"
                                    >
                        <a title="{{__('pendingTransactions.VENEZUELAN:EXTRA_DATA')}}"
                           :href="`/accounts/${props.row.receiver_account.id}`"
                           target="_blank"
                        >
                        @{{ props.row.receiver_account.bank.name }}
                        </a>
                    </b-table-column>
                    <b-table-column field="rate"
                                    label="{{__('pendingTransactions.SUGGESTED_RATE')}}"
                                    searchable>
                        @{{ props.row.rate|currency({
                        symbol: 'Bs/$', precision: 2, separator: ',', decimal: '.', formatWithSymbol: true,
                        }) }}
                    </b-table-column>
                    <b-table-column field="amount"
                                    label="{{__('pendingTransactions.AMOUNT')}}"
                                    searchable>
                        @{{ props.row.amount |currency}}
                    </b-table-column>
                    <b-table-column field="status"
                                    label="{{__('pendingTransactions.STATUS')}}"
                                    >
                        <div
                            v-if="props.row.status==='pending'"
                            class="field is-grouped">
                            <p class="control">
                                <button
                                    class="button is-info"
                                    :disabled="onChangeState"
                                    @click="approveTransaction(props.row)"
                                >
                                    {{__('pendingTransactions.STATUS:APPROVE')}}
                                </button>
                            </p>
                            <p class="control">
                                <button
                                    class="button is-danger"
                                    :disabled="onChangeState"
                                    @click="rejectTransation(props.row)"
                                >
                                    {{__('pendingTransactions.STATUS:REJECT')}}
                                </button>
                            </p>
                        </div>

                        <span v-else-if="props.row.status==='aprooved'">
                        {{__('pendingTransactions.STATUS:APPROVED')}}
                        </span>
                        <span v-else>
                            {{__('pendingTransactions.STATUS:REJECTED')}}
                        </span>



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

    </pending-transactions>
    </section>
@endsection
