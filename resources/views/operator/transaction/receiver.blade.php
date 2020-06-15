<receiver-data
    {{ $properties }}

    inline-template>
    <div>
    <validation-observer tag="section" class="section is-paddingless" v-slot="{invalid}">
        <div class="columns is-overflow-auto">
            <validation-provider tag="div" rules="required" class="column">
                <input type="hidden" v-model="receiverSelected">
                <b-table
                    :data="receivers"
                    scrollable
                    :mobile-cards="false"
                    :loading="loadingReceivers"
                    :selected.sync="receiverSelected"
                    :striped="true"
                    aria-next-label="Next page"
                    aria-previous-label="Previous page">

                    <template slot-scope="props">
                        <b-table-column field="idn_type" label="{{__('auth.IDN_TYPE')}}">
                            @{{ props.row.idn_type }}
                        </b-table-column>
                        <b-table-column field="idn" label="{{__('auth.IDN')}}">
                            @{{ props.row.idn }}
                        </b-table-column>

                        <b-table-column field="name"
                                        label="{{__('auth.NAME')}}"
                        >
                            @{{ props.row.name }}
                        </b-table-column>

                        <b-table-column field="last_name" label="{{__('auth.LAST_NAME')}}">
                            @{{ props.row.last_name }}
                        </b-table-column>
                        <b-table-column field="email" label="{{__('auth.EMAIL')}}">
                            @{{ props.row.email }}
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
                <div class="buttons">
                <b-button
                    size="is-medium"
                    @click="openModalForm('newReceiverForm')"
                    type="is-info"
                    icon-left="user-plus">
                    {{__('receiver.NEW:RECEIVER')}}
                </b-button>
                <b-button
                    size="is-medium"
                    :disabled="!receiverSelected"
                    @click="openModalForm('editReceiverForm')"
                    type="is-info"
                    icon-left="user-plus">
                    {{__('receiver.EDIT:RECEIVER')}}
                </b-button>
                <b-button
                    size="is-medium"
                    :disabled="!receiverSelected"
                    :loading="unlinkingReceiver"
                    @click="unlinkReceiver"
                    type="is-info"
                    icon-left="user-plus">
                    {{__('receiver.UNLINK:RECEIVER')}}
                </b-button>
                </div>
            </validation-provider>
            <validation-provider tag="div" rules="required" class="column">
                <input type="hidden" v-model="receiverAccount">
                <b-table
                    :mobile-cards="false"
                    scrollable
                    :data="receiverAccounts"
                    :loading="loadingReceivers"
                    :selected.sync="receiverAccount"
                    :striped="true"
                    aria-next-label="Next page"
                    aria-previous-label="Previous page">

                    <template slot-scope="props">
                        <b-table-column field="bank" label="{{__('receiver.BANK')}}">
                            @{{ props.row.bank.name }}
                        </b-table-column>
                        <b-table-column field="type"
                                        label="{{__('receiver.TYPE')}}"
                        >
                            @{{ props.row.type }}
                        </b-table-column>
                        <b-table-column field="number" label="{{__('receiver.NUMBER')}}">
                            @{{ props.row.number }}
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
                <div class="buttons">
                <b-button
                    size="is-medium"
                    type="is-info"
                    :disabled="!receiverSelected"
                    @click="openModalForm('newAccount')"
                    icon-left="plus-circle">
                    {{__('receiver.NEW:ACCOUNT')}}
                </b-button>
                <b-button
                    size="is-medium"
                    @click="openModalForm('editAccount')"
                    :disabled="!receiverAccount"
                    type="is-info"
                    icon-left="user-plus">
                    {{__('receiver.EDIT:ACCOUNT')}}
                </b-button>
                <b-button
                    size="is-medium"
                    :loading="unlinkingAccount"
                    :disabled="!receiverAccount"
                    @click="unlinkAccount"
                    type="is-info"
                    icon-left="user-plus">
                    {{__('receiver.UNLINK:ACCOUNT')}}
                </b-button>
                </div>
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
    <b-modal :active.sync="openModal"
             has-modal-card
             trap-focus
             :destroy-on-hide="false"
             aria-role="dialog"
             aria-modal>
        <component
            :is="activeForm"
            v-bind="customComponentProps"
            @receiver-added="lookupForReceivers"
            @receiver-edited="lookupForReceivers"
            @account-added="lookupForReceivers"
            @account-edited="lookupForReceivers">
    </b-modal>
    </div>
</receiver-data>
