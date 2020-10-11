<template>

</template>

<script>
import currencyFilter from '../../../currency';
import {Money} from 'v-money';

export default {
  name: "accountsDetails",
  filters: {
    currencyFilter,
  },
  components: {
    Money
  },
  data: () => ({
    transactionType: '',
    amount: '',
    comment:'',
    money: {
      decimal: ',',
      thousands: '.',
      precision: 2,
      masked: false
    }
  }),
  props: {
    account: {
      type: Object,
      default: () => ({})
    }
  },
  computed: {
    newBalance() {
      return this.transactionType === 'income' ?
        this.account.balance + parseFloat(this.amount) :
        this.account.balance - parseFloat(this.amount);
    },
    translateType() {
      return this.transactionType === 'income' ? 'Ingreso' : 'Egreso'
    }
  },
  methods: {
    async executeTransaction() {
      this.$buefy.dialog.confirm({
        message: `¿Desea ejecutar una transaccion de a <b>${this.translateType}</b> por <b>${this.$options.filters.currencyFilter(this.amount, {
          ...this.account.bank.currency,
          formatWithSymbol: this.account.bank.currency.format_with_symbol === 1
        })}</b> en la cuenta del banco <b>${this.account.bank.name}</b> numero <b>${this.account.number}</b> Su nuevo saldo será <b>${this.$options.filters.currencyFilter(this.newBalance, {
          ...this.account.bank.currency,
          formatWithSymbol: this.account.bank.currency.format_with_symbol === 1
        })}</b></b> ?`,
        onConfirm: async () => {
          try {
            await $http.post(`/api/transaction/${this.account.id}`, {
              amount: this.amount,
              type: this.transactionType,
              comment:this.comment,
            })
          } finally {
          }
        },
      });
    }
  }

}
</script>

<style scoped>
dl {
    display: flex;
    width: 100%;
}

dt {
    flex: 1;
}

dd {
    flex: 1;
}
</style>