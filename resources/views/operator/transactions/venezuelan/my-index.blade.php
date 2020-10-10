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
        <my-venezuelan-transactions
                inline-template
                :transactions-query='@json($transactions)'
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
                    <div class="column is-narrow">
                        <b-button type="is-info" outlined @click="refreshData">{{__('transaction.REFRESH')}}</b-button>
                    </div>
                </div>

                <b-table
                        @click="transactionClicked"
                        :data="transactions"
                        :total="query.total"
                        :opened-detailed="defaultOpenedDetails"
                        detail-key="id"
                        custom-detail-row
                        detailed
                        :current-page="query.current_page"
                        :loading="loading"
                        :per-page="query.perPage"
                        paginated
                        backend-pagination
                        backend-filtering
                        :show-detail-icon="true"
                        :striped="true"
                        :scrollable="true"
                        @page-change="changedPage"
                        @filters-change="changedFilter"
                        aria-next-label="Next page"
                        aria-previous-label="Previous page"
                        @details-open="getDetailsData"
                >

                    <b-table-column field="id"
                                    label="ID"
                                    width="40"
                                    numeric
                                    sticky
                                    v-slot="props">
                        @{{ props.row.id }}
                    </b-table-column>
                    <b-table-column field="bank_reference"
                                    label="{{__('transaction.BANK:REFERENCE')}}"
                                    width="200"
                                    v-slot="props"
                    >
                        @{{ props.row.bank_reference}}
                    </b-table-column>
                    <b-table-column field="track_number"
                                    label="{{__('transaction.TRACKING:NUMBER')}}"
                                    width="200"
                                    v-slot="props"
                    >
                        @{{ props.row.track_number}}
                    </b-table-column>
                    <b-table-column field="operator"
                                    label="{{__('transaction.OPERATOR')}}"
                                    width="200"
                                    v-slot="props"
                    >
                        @{{ props.row.operator.name}} @{{ props.row.operator.last_name}}
                    </b-table-column>
                    <b-table-column field="client_id"
                                    label="{{__('transaction.CLIENT')}}"
                                    v-slot="props"
                                    width="200"


                    ><span v-if="props.row.client">
                            @{{ props.row.client.name}} @{{ props.row.client.last_name}}
                            </span>
                        <span v-else>N/A</span>
                    </b-table-column>
                    <b-table-column field="amount"
                                    label="{{__('transaction.AMOUNT')}}" v-slot="props">
                        @{{ props.row.amount |currency(props.row.account.bank.currency)}}
                    </b-table-column>
                    <b-table-column field="status"
                                    label="{{__('transaction.STATUS')}}"
                                    v-slot="props">
                        <span v-if="props.row.status=='pending'">{{__('transaction.STATUS:PENDING')}}</span>
                        <span v-else-if="props.row.status=='executed'">{{__('transaction.STATUS:APPROVED')}}</span>
                        <span
                                v-else-if="props.row.status=='in-progress'">{{__('transaction.STATUS:IN:PROGRESS')}}</span>
                    </b-table-column>

                    <template #detail="{row:transaction}">
                        <tr v-if="transaction.comment">
                            <td colspan="8">
                                <section class="section">
                                    @{{transaction.comment}}
                                    <br>
                                </section>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                <b-loading :is-full-page="false" v-model="isLoadingTransaction"
                                           :can-cancel="false"></b-loading>
                                <section
                                        v-if="!isLoadingTransaction && transaction.venezuelanRelated.status==='executed'"
                                        class="section">
                                    <div class="is-title">
                                        Datos de La Transacción
                                    </div>
                                    <div class='rows'>
                                        <div class="row is-full">
                                            <span class="is-size-4">{{__('transaction.BANK:REFERENCE')}}: @{{ transaction.venezuelanRelated.bank_reference}}</span>
                                        </div>
                                        <div class='row is-full'>
                                            Nombres: @{{transaction.venezuelanRelated.account.owners[0].name}}
                                        </div>
                                        <div class='row is-full'>
                                            Apellidos: @{{transaction.venezuelanRelated.account.owners[0].last_name}}
                                        </div>
                                        <div class='row is-full'>
                                            id:
                                            @{{transaction.venezuelanRelated.account.owners[0].idn_type}}-@{{transaction.venezuelanRelated.account.owners[0].idn}}
                                        </div>
                                        <div class='row is-full'>
                                            Teléfono: @{{transaction.venezuelanRelated.account.owners[0].phone}}
                                        </div>
                                        <div class='row is-full'>
                                            Email: @{{transaction.venezuelanRelated.account.owners[0].email}}
                                        </div>
                                        <div class='row is-full'>
                                            Banco: @{{transaction.venezuelanRelated.account.bank.name}}
                                        </div>
                                        <div class='row is-full'>
                                            Número de Cuenta: @{{transaction.venezuelanRelated.account.number|account}}
                                        </div>

                                        <div class="row is-full" v-if="transaction.attachments.length">
                                            <span class="is-size-1">{{__('transaction.ATTACHMENTS')}}:</span>
                                            <hr>
                                            <section v-for="attachment in transaction.attachments">
                                                <b-image

                                                        :key="attachment.id"
                                                        :src="`{{Config::get('app.url')}}${attachment.path}`"
                                                        :placeholder="attachment.updated_at"
                                                        ratio="2by1"
                                                ></b-image>
                                            </section>
                                        </div>
                                    </div>
                                </section>
                                <section
                                        v-if="!isLoadingTransaction && transaction.venezuelanRelated.status!=='executed'"
                                        class="section">
                                    <div class="is-title">
                                        Datos de La Transacción
                                    </div>
                                    <div class='rows'>
                                        <div class='row is-full'>
                                            Nombres: @{{transaction.venezuelanRelated.account.owners[0].name}}
                                        </div>
                                        <div class='row is-full'>
                                            Apellidos: @{{transaction.venezuelanRelated.account.owners[0].last_name}}
                                        </div>
                                        <div class='row is-full'>
                                            id:
                                            @{{transaction.venezuelanRelated.account.owners[0].idn_type}}-@{{transaction.venezuelanRelated.account.owners[0].idn}}
                                        </div>
                                        <div class='row is-full'>
                                            Teléfono: @{{transaction.venezuelanRelated.account.owners[0].phone}}
                                        </div>
                                        <div class='row is-full'>
                                            Email: @{{transaction.venezuelanRelated.account.owners[0].email}}
                                        </div>
                                        <div class='row is-full'>
                                            Banco: @{{transaction.venezuelanRelated.account.bank.name}}
                                        </div>
                                        <div class='row is-full'>
                                            Número de Cuenta: @{{transaction.venezuelanRelated.account.number|account}}
                                        </div>
                                        <validation-observer v-slot="{invalid}">
                                            <div class="row is-full">
                                                <b-field label="{{__('transaction.BANK:REFERENCE')}}">
                                                    <validation-provider
                                                            rules="required"
                                                            name="{{__('transaction.BANK:REFERENCE')}}"
                                                            v-slot="{ classes,errors,valid}">
                                                        <b-input
                                                                :class="classes"

                                                                v-model="transaction.venezuelanRelated.bank_reference"></b-input>
                                                    </validation-provider>
                                                </b-field>
                                            </div>
                                            <div class="row is-full">
                                                <validation-provider
                                                        rules="required|min:1"
                                                        name="{{__('transaction.VOUCHER:FILES')}}"
                                                        v-slot="{ classes,errors,valid}"
                                                        tag="div"
                                                        class="control">
                                                    <uppy-uploader
                                                            :class="classes"
                                                            v-model="transaction.venezuelanRelated.attachments"
                                                            :max-file-size-in-bytes="1000000">
                                                    </uppy-uploader>
                                                    <strong v-if="errors[0]"
                                                            class="help is-danger">@{{errors[0]}}</strong>
                                                </validation-provider>
                                            </div>
                                            <div class="row is-full">
                                                <b-button
                                                        type="is-primary"
                                                        :disabled="invalid"
                                                        :loading="isExecutingTransaction"
                                                        @click="executeTransaction(transaction.venezuelanRelated)"
                                                >Ejecutar
                                                </b-button>

                                            </div>
                                        </validation-observer>
                                    </div>
                                </section>
                            </td>
                        </tr>
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
        </my-venezuelan-transactions>
    </section>
@endsection
