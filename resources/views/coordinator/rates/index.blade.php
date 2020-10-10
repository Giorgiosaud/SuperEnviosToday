@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="super-title">
                    {{ __('rates.WELCOME') }}
                </h1>
                <h2 class="subtitle">
                    {{ __('rates.MESSAGE',[ 'app'=>config('app.name')]) }}
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <rates-list :rates-query='@json($rates)' :all-currencies='@json($currencies)' inline-template v-cloak>
            <div>
                <section class="section">
                    <g-chart
                            :settings="{ packages: ['annotationchart','corechart', 'table', 'map'], language: 'ES-es' }"
                            type="AnnotationChart"
                            :data="adjustedDataForGraph"
                            :options="chartOptions"
                            :events="chartEvents"></g-chart>
                </section>
                <section class="section">

                    <div class="columns">
                        <div class="column">
                            <button class="button field is-info"
                                    @click="openAddRateModal">
                                {{__('rates.ADD_CURRENCY')}}

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
                            :data="rates"
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

                        <b-table-column field="id"
                                        label="ID"
                                        width="40"
                                        numeric
                                        v-slot="props">
                            @{{ props.row.id }}
                        </b-table-column>

                        <b-table-column field="since"
                                        label="{{__('rates.SINCE')}}"
                                        v-slot="props">
                            @{{ props.row.since | timeFormat("dd-MM-yyyy 'a las' h:mm a")}}
                        </b-table-column>

                        <b-table-column field="amount"
                                        label="{{__('rates.AMOUNT')}}"
                                        v-slot="props">
                            @{{ props.row.amount |rateCurrency(props.row.currency) }}
                        </b-table-column>
                        <b-table-column field="curerncy"
                                        label="{{__('rates.CURRENCY')}}"
                                        v-slot="props">
                            @{{ props.row.currency.name }}
                        </b-table-column>
                        <b-table-column field="message"
                                        label="{{__('rates.MESSAGE:FIELD')}}"
                                        v-slot="props"
                        >
                            <div v-html="props.row.message"></div>

                        </b-table-column>

                        <template slot="detail" slot-scope="props">
                            <section>
                                <b-field label="Select datetime">
                                    <b-datetimepicker
                                            v-model="props.row.since"
                                            mobile-native
                                            placeholder="Click to select..."
                                            icon="calendar-today"
                                            readonly
                                            :datepicker="{ showWeekNumber:true }"
                                            :timepicker="{ enableSeconds:true }">
                                        <template slot="left">
                                            <button class="button is-primary"
                                                    @click="props.row.since = new Date()">
                                                <b-icon icon="clock"></b-icon>
                                                <span>Now</span>
                                            </button>
                                        </template>
                                    </b-datetimepicker>
                                </b-field>
                                <b-field label="Amount">
                                    <b-input v-model="props.row.amount"></b-input>
                                </b-field>
                                <b-field label="Tipo de moneda">
                                    <b-select placeholder="Tipo de moneda" v-model="props.row.currency_id" expanded>
                                        <option v-for="currency in allCurrencies" :value="currency.id">
                                            @{{currency.name}}
                                        </option>
                                    </b-select>
                                </b-field>
                                <b-field label="Comentario">
                                    <quill-editor class="textarea"

                                                  :id="`comment-${props.row.id}`"
                                                  v-model.lazy="props.row.message"></quill-editor>

                                </b-field>
                                <div class="buttons">
                                    <b-button
                                            @click="changeRate(props.row.id,props.row.since,props.row.currency_id,props.row.amount,props.row.message)"
                                            :loading="savingRate"
                                            type="is-info">
                                        Guardar
                                    </b-button>
                                    <b-button
                                            @click="removeRate(props.row.id)"
                                            :loading="removingRate"
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
                    <add-rate @currency-created="loadAsyncData"></add-rate>
                </b-modal>
            </div>
        </rates-list>
    </section>
@endsection
