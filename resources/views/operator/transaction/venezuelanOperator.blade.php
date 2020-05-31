<venezuelan-operator-data
    {{ $properties }}
    inline-template>
    <validation-observer tag="section" class="section is-paddingless" v-slot="{invalid}">
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
                        <b-table-column field="bank" label="{{__('auth.IDN_TYPE')}}">
                            @{{ props.row.bank.name }}/@{{ props.row.bank.type }}
                        </b-table-column>
                        <b-table-column field="idn" label="{{__('auth.IDN')}}">
                            @{{ props.row.number }}
                        </b-table-column>

                        <b-table-column field="balance"
                                        label="{{__('auth.NAME')}}"
                        >
                            @{{ props.row.balance | currency}}
                        </b-table-column>
                    </template>
                    <template slot="empty">
                        <section class="section">
                            <div class="content has-text-grey has-text-centered">
                                <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
                                <p>{{__('receiver.NO:RECEIVERS')}}</p>
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
                                <p>{{__('receiver.NO:RECEIVERS:ACCOUNTS')}}</p>
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
