<venezuelan-operator-data
    {{ $properties }}
    inline-template>
    <validation-observer tag="section" class="section is-paddingless" v-slot="{invalid}">
        <div class="columns">
            <div class="column">
                <b-button type="is-primary" @click="getBaseAccounts">Refrescar</b-button>
            </div>
        </div>
        <div class="columns is-overflow-auto">

            <validation-provider tag="div" rules="required" class="column">
                <input type="hidden" v-model="selectedAccount">
                <b-table
                    :data="venezuelanAccounts"
                    scrollable
                    :mobile-cards="false"
                    :loading="loadingAccounts"
                    :selected.sync="selectedAccount"
                    :striped="true"
                    aria-next-label="Next page"
                    aria-previous-label="Previous page">

                    <template slot-scope="props">
                        <b-table-column field="bank" label="{{__('transaction.BANK')}}">
                            @{{ props.row.bank.name }}/@{{ props.row.type }}
                        </b-table-column>
                        <b-table-column field="idn" label="{{__('transaction.NUMBER')}}">
                            @{{ props.row.number }}
                        </b-table-column>

                        <b-table-column field="balance"
                                        label="{{__('transaction.BALANCE')}}"
                        >
                            @{{ props.row.balance | currency}}
                        </b-table-column>
                    </template>
                    <template slot="empty">
                        <section class="section">
                            <div class="content has-text-grey has-text-centered">
                                <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
                                <p>{{__('transaction.NO:ACCOUNTS')}}</p>
                            </div>
                        </section>
                    </template>
                </b-table>
            </validation-provider>
            <validation-provider tag="div" rules="required" class="column">
                <input type="hidden" v-model="selectedOperator">
                <b-table
                    :mobile-cards="false"
                    scrollable
                    :data="owners"
                    :selected.sync="selectedOperator"
                    :striped="true"
                    aria-next-label="Next page"
                    aria-previous-label="Previous page">

                    <template slot-scope="props">
                        <b-table-column field="bank" label="{{__('pendingTransactions.FOREIGN_OPERATOR:NAME_AND_LAST_NAME')}}">
                            @{{ props.row.name }} @{{ props.row.last_name }}
                        </b-table-column>
                    </template>
                    <template slot="empty">
                        <section class="section">
                            <div class="content has-text-grey has-text-centered">
                                <font-awesome-icon class="is-size-1" icon="comment-dollar"></font-awesome-icon>
                                <p>{{__('transaction.NO:OPERATORS')}}</p>
                            </div>
                        </section>
                    </template>
                </b-table>
            </validation-provider>
        </div>
        <div class="columns has-padding-top-5">
            <div class="column">
                <b-button size="is-big"
                          type="is-info"
                          :disabled="invalid"
                          icon-right="arrow-circle-right"
                          @click="nextStep">
                    {{__('transaction.NEXT:BUTTON')}}
                </b-button>
            </div>
            .
        </div>
    </validation-observer>
</venezuelan-operator-data>
