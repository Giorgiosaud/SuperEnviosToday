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
            :transactions-query='@json($transactions)'>
            <section>
                <b-select v-model="statusFilter" placeholder="Seleccione estado">
                    <option value="">{{__('transaction.STATUS:ALL')}}</option>
                    <option value="pending">{{__('transaction.STATUS:PENDING')}}</option>
                    <option value="approved">{{__('transaction.STATUS:APPROVED')}}</option>
                    <option value="rejected">{{__('transaction.STATUS:REJECTED')}}</option>
                </b-select>
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
                        <b-table-column field="operator_id"
                                        label="{{__('transaction.OPERATOR')}}"
                                        width="200"
                        >
                            @{{ props.row.operator_id}}
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
                    <!--b-table-column field="client"
                                        label="{{__('transaction.CLIENT:NAME_AND_LAST_NAME')}}"
                                        >
                            <a title="{{__('transaction.Client:EXTRA_DATA')}}"
                               :href="`/users/${props.row.client.id}`"
                               target="_blank"
                            >@{{ props.row.client.name }} @{{ props.row.client.last_name }}
                            </a>

                        </b-table-column>

                        <b-table-column field="operator_account"
                                        label="{{__('transaction.FOREIGN_OPERATOR:NAME_AND_LAST_NAME')}}"
                                        >
                            <a title="{{__('transaction.FOREIGN_OPERATOR:EXTRA_DATA')}}"
                               :href="`/users/${props.row.local_operator_account.id}`"
                               target="_blank"
                            >@{{ props.row.foreign_operator.name }} @{{ props.row.foreign_operator.last_name }}
                            </a>

                        </b-table-column>

                        <b-table-column field="venezuelan_operator"
                                        label="{{__('transaction.VENEZUELAN:NAME_AND_LAST_NAME')}}"
                                        >
                            <a title="{{__('transaction.VENEZUELAN:EXTRA_DATA')}}"
                               :href="`/users/${props.row.venezuelan_operator.id}`"
                               target="_blank"
                            >@{{ props.row.venezuelan_operator.name }} @{{ props.row.venezuelan_operator.last_name }}
                            </a>

                        </b-table-column>
                        <b-table-column field="foreign_bank"
                                        label="{{__('transaction.FOREIGN:BANK')}}"
                                        >
                            <a title="{{__('transaction.VENEZUELAN:EXTRA_DATA')}}"
                               :href="`/accounts/${props.row.foreign_account.id}`"
                               target="_blank"
                            >@{{ props.row.foreign_account.bank.name }}</a>
                        </b-table-column>
                        <b-table-column field="venezuelan_bank_from"
                                        label="{{__('transaction.VENEZUELAN:BANK_FROM')}}"
                                        >
                            <a title="{{__('transaction.VENEZUELAN:EXTRA_DATA')}}"
                               :href="`/accounts/${props.row.local_operator_account.id}`"
                               target="_blank"
                            >
                            @{{ props.row.local_operator_account.bank.name }}
                            </a>
                        </b-table-column>
                        <b-table-column field="venezuelan_bank_to"
                                        label="{{__('transaction.VENEZUELAN:BANK_TO')}}"
                                        >
                            <a title="{{__('transaction.VENEZUELAN:EXTRA_DATA')}}"
                               :href="`/accounts/${props.row.receiver_account.id}`"
                               target="_blank"
                            >
                            @{{ props.row.receiver_account.bank.name }}
                            </a>
                        </b-table-column>
                        <b-table-column field="rate"
                                        label="{{__('transaction.SUGGESTED_RATE')}}"
                                        searchable>
                            @{{ props.row.rate|rateCurrency(props.row.foreign_account.bank.currency) }}
                        </b-table-column>
                        <b-table-column field="amount"
                                        label="{{__('transaction.AMOUNT')}}"
                                        searchable>
                            @{{ props.row.amount |currency(props.row.foreign_account.bank.currency)}}
                        </b-table-column>
                        <b-table-column field="status"
                                        label="{{__('transaction.STATUS')}}"
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
                                        {{__('transaction.STATUS:APPROVE')}}
                        </button>
                    </p>
                    <p class="control">
                        <button
                            class="button is-danger"
                            :disabled="onChangeState"
                            @click="rejectTransation(props.row)"
                        >
{{__('transaction.STATUS:REJECT')}}
                        </button>
                    </p>
                </div>

                <span v-else-if="props.row.status==='approved'">
{{__('transaction.STATUS:APPROVED')}}
                        </span>
                        <span v-else>
{{__('transaction.STATUS:REJECTED')}}
                        </span>



                    </b-table-column-->


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
