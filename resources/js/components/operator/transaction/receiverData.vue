<template>
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
            :row-class="(row, index) => 'is-selectable'"
            aria-previous-label="Previous page">


            <b-table-column
              field="idn_type"
              :label="$t('auth.IDN_TYPE')"
              v-slot="props"
            >
              {{ props.row.idn_type }}
            </b-table-column>
            <b-table-column field="idn"
                            :label="$t('auth.IDN')"
                            v-slot="props">
              {{ props.row.idn }}
            </b-table-column>

            <b-table-column field="name"
                            :label="$t('auth.NAME')"
                            v-slot="props"
            >
              {{ props.row.name }}
            </b-table-column>

            <b-table-column field="last_name"
                            :label="$t('auth.LAST_NAME')"
                            v-slot="props">
              {{ props.row.last_name }}
            </b-table-column>
            <b-table-column field="email"
                            :label="$t('auth.EMAIL')"
                            v-slot="props">
              {{ props.row.email }}
            </b-table-column>
            <template slot="empty">
              <section class="section">
                <div class="content has-text-grey has-text-centered">
                  <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
                  <p>{{$t('receiver.NO:RECEIVERS')}}</p>
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
              {{$t('receiver.NEW:RECEIVER')}}
            </b-button>
            <b-button
              size="is-medium"
              :disabled="!receiverSelected"
              @click="openModalForm('editReceiverForm')"
              type="is-info"
              icon-left="user-plus">
              {{$t('receiver.EDIT:RECEIVER')}}
            </b-button>
            <b-button
              size="is-medium"
              :disabled="!receiverSelected"
              :loading="unlinkingReceiver"
              @click="unlinkReceiver"
              type="is-info"
              icon-left="user-plus">
              {{$t('receiver.UNLINK:RECEIVER')}}
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
            :row-class="(row, index) => 'is-selectable'"
            aria-previous-label="Previous page">


            <b-table-column field="bank"
                            :label="$t('receiver.BANK')"
                            v-slot="props">
              {{ props.row.bank.name }}
            </b-table-column>
            <b-table-column field="type"
                            :label="$t('receiver.TYPE')"
                            v-slot="props"
            >
              {{ props.row.type }}
            </b-table-column>
            <b-table-column field="number"
                            :label="$t('receiver.NUMBER')"
                            v-slot="props">
              {{ props.row.number }}
            </b-table-column>
            <template slot="empty">
              <section class="section">
                <div class="content has-text-grey has-text-centered">
                  <font-awesome-icon class="is-size-1" icon="comment-dollar"></font-awesome-icon>
                  <p>{{$t('receiver.NO:RECEIVERS:ACCOUNTS')}}</p>
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
              {{$t('receiver.NEW:ACCOUNT')}}
            </b-button>
            <b-button
              size="is-medium"
              @click="openModalForm('editAccount')"
              :disabled="!receiverAccount"
              type="is-info"
              icon-left="user-plus">
              {{$t('receiver.EDIT:ACCOUNT')}}
            </b-button>
            <b-button
              size="is-medium"
              :loading="unlinkingAccount"
              :disabled="!receiverAccount"
              @click="unlinkAccount"
              type="is-info"
              icon-left="user-plus">
              {{$t('receiver.UNLINK:ACCOUNT')}}
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
            {{$t('transaction.NEXT:BUTTON')}}
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
        @account-edited="lookupForReceivers"></component>
    </b-modal>
  </div>
</template>
<script>
import newReceiverForm from './newReceiverForm.vue';
import editReceiverForm from './editReceiverForm.vue';
import newAccount from './newAccount.vue';
import editAccount from './editAccount.vue';

export default {
  name: 'ReceiverData',
  components: {
    newReceiverForm,
    editReceiverForm,
    newAccount,
    editAccount,
  },
  props: {
    client: {
      type: [Object, null],
      default: () => null,
    },
  },
  data: () => ({
    receiverSelected: null,
    receiverAccount: null,
    receivers: [],
    loadingReceivers: false,
    unlinkingReceiver: false,
    unlinkingAccount: false,
    activeForm: '',
    openModal: false,
  }),
  computed: {
    customComponentProps() {
      switch (this.activeForm) {
        case ('newReceiverForm'):
          return {
            client: this.client,

          };
        case ('editReceiverForm'):
          return {
            client: this.client,
            receiver: this.receiverSelected,
          };
        case 'newAccount':
          return {
            receiver: this.receiverSelected,
          };
        case 'editAccount':
          return {
            receiver: this.receiverSelected,
            account: this.receiverAccount,
          };
        default:
          return {};
      }
    },
    receiverAccounts() {
      if (this.receiverSelected) return this.receiverSelected.accounts;
      return [];
    },
  },
  watch: {
    receiverSelected() {
      this.receiverAccount = null;
    },
    client: {
      deep: true,
      async handler(value) {
        if (value) {
          await this.lookupForReceivers();
        }
      },
    },
  },

  methods: {
    openModalForm(form) {
      this.openModal = true;
      this.activeForm = form;
    },
    nextStep() {
      this.$emit('receiver-set', {
        receiver: this.receiverSelected,
        receiverAccount: this.receiverAccount,
      });
      this.$emit('next-step');
    },
    async lookupForReceivers() {
      this.loadingReceivers = true;
      try {
        const response = await $http.get(`/api/user/receivers/${this.client.id}`);
        this.receivers = await response.json();
        this.receiverSelected = null;
        this.receiverAccount = null;
      } catch (error) {
        console.log(error);
      } finally {
        this.loadingReceivers = false;
      }
    },
    async unlinkReceiver() {
      this.unlinkingReceiver = true;
      try {
        await $http.patch(`/api/account/${this.receiverAccount.id}/user/${this.receiverSelected.id}/unlink`);
      } catch (error) {
        console.log(error);
      } finally {
        await this.lookupForReceivers();
        this.unlinkingReceiver = false;
      }
    },
    async unlinkAccount() {
      this.unlinkingAccount = true;
      try {
        await $http.patch(`/api/account/${this.receiverAccount.id}/user/${this.receiverSelected.id}/unlink`);
      } catch (error) {
        console.log(error);
      } finally {
        await this.lookupForReceivers();
        this.unlinkingAccount = false;
      }
    },
  },
};
</script>

<style scoped>

</style>
