<template>
  <validation-observer tag="section" class="section is-paddingless" v-slot="{invalid}">
    <nav class="level">
      <div class="level-item has-text-centered">
        <div>
          <p class="heading">{{$t('transaction.AMOUNT')}}</p>
          <p class="title" v-if="transactionData">{{ transactionData.bsAmount|currency }}</p>
        </div>
      </div>
      <div class="level-item has-text-centered">
        <div v-if="selectedAccount">
          <p class="heading">{{$t('transaction.FINAL:BALANCE')}}</p>
          <p class="title">{{ selectedAccount.balance-transactionData.bsAmount|currency }}</p>
        </div>
        <div v-else>
          <p class="heading">{{$t('transaction.FINAL:BALANCE')}}</p>
          <p class="title">Seleccione una Cuenta</p>
        </div>
      </div>
    </nav>
    <div class="columns">
      <div class="column">
        <b-button type="is-primary" @click="getBaseAccounts">Refrescar</b-button>
      </div>
    </div>
    <div class="columns is-overflow-auto" v-if="transactionData">

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
          :is-row-selectable="(row) => row.balance>= transactionData.bsAmount"
          :row-class="(row, index) => row.balance< transactionData.bsAmount?'is-unselectable':'is-selectable'"
          aria-previous-label="Previous page">

          <b-table-column field="bank"
                          :label="$t('transaction.BANK')"
                          v-slot="props"
          >
            {{ props.row.bank.name }}/{{ props.row.type }}
          </b-table-column>
          <b-table-column field="idn"
                          :label="$t('transaction.NUMBER')"
                          v-slot="props">
            {{ props.row.number }}
          </b-table-column>

          <b-table-column field="balance"
                          :label="$t('transaction.BALANCE')"
                          v-slot="props"
          >
            {{ props.row.balance | currency}}
          </b-table-column>
          <template slot="empty">
            <section class="section">
              <div class="content has-text-grey has-text-centered">
                <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
                <p>{{$t('transaction.NO:ACCOUNTS')}}</p>
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
          :row-class="(row, index) => 'is-selectable'"
          aria-previous-label="Previous page">

          <b-table-column field="bank"
                          :label="$t('pendingTransactions.FOREIGN_OPERATOR:NAME_AND_LAST_NAME')"
                          v-slot="props">
            {{ props.row.name }} {{ props.row.last_name }}
          </b-table-column>

          <template slot="empty">
            <section class="section">
              <div class="content has-text-grey has-text-centered">
                <font-awesome-icon class="is-size-1" icon="comment-dollar"></font-awesome-icon>
                <p>{{$t('transaction.NO:OPERATORS')}}</p>
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
          {{$t('transaction.NEXT:BUTTON')}}
        </b-button>
      </div>
      .
    </div>
  </validation-observer>
</template>
<script>
import currencyFilter from '../../../currency';

const bsFormat = {
  symbol: 'Bs ', precision: 8, separator: '.', decimal: ',', formatWithSymbol: true,
};

export default {
  name: 'VenezuelanOperatorData',
  filters: {
    currency(value) {
      return currencyFilter(value, bsFormat);
    },
  },
  components: {
  },
  props: {
    transactionData: {
      type: [Object, null],
      default: null,
    },
  },
  data: () => ({
    venezuelanAccounts: [],
    selectedAccount: null,
    selectedOperator: null,
    loadingAccounts: true,
  }),
  computed: {
    owners() {
      if (this.selectedAccount) {
        return this.selectedAccount.owners;
      }
      return [];
    },
  },
  watch: {
    selectedAccount() {
      this.selectedOperator = null;
    },
  },
  async created() {
    await this.getBaseAccounts();
  },
  methods: {
    async getBaseAccounts() {
      this.loadingAccounts = true;
      const response = await $http.get('/api/accounts/base');
      this.venezuelanAccounts = await response.json();
      this.loadingAccounts = false;
    },
    selectAccount(account) {
      this.selectedAccount = account;
    },
    nextStep() {
      this.$emit('operator-set', {
        operator: this.selectedOperator,
        selectedAccount: this.selectedAccount,
      });
    },
  },
};
</script>

<style scoped>

</style>
