<template>
  <article>
    <div class="columns">
      <div class="column">
        <a href="/accounts" class="is-link" v-text="`<< Ir al listado de cuentas`"></a>
      </div>
    </div>
    <validation-observer v-slot="{invalid}">
      <div class="columns">
        <div class="column">
          {{$t('accounts.BANK:NAME')}}
        </div>
        <div class="column">
          {{ account.bank.name }}
        </div>
      </div>
      <div class="columns">
        <div class="column">
          {{$t('accounts.NUMBER')}}
        </div>
        <div class="column">
          {{ account.number }}
        </div>
      </div>
      <div class="columns">
        <div class="column">
          {{$t('accounts.BALANCE')}}
        </div>
        <div class="column">
          {{ account.balance | currencyFilter({
          ...account.bank.currency,
          formatWithSymbol:account.bank.currency.format_with_symbol === 1
        }) }}
        </div>
      </div>
      <div class="columns">
        <div class="column">
          <validation-provider rules="required" v-slot="{ errors }"
                               :name="$t('transaction.TYPE')"
          >
            <label for="transaction-type">{{$t('transaction.TYPE')}}</label>
            <b-select id="transaction-type" v-model="transactionType"
                      :placeholder="$t('transaction.TYPE')"
                      expanded>
              <option value="income">{{$t('transaction.INCOME')}}</option>
              <option value="outcome">{{$t('transaction.OUTCOME')}}</option>
            </b-select>
            <span id="is-error">{{ errors[0] }}</span>

          </validation-provider>
        </div>
      </div>
      <div class="columns">
        <div class="column">
          <validation-provider :rules="`required|decimals:2|balance:${newBalance}`"
                               v-slot="{ errors,classes }"
                               :name="$t('transaction.AMOUNT')">
            <b-field :label="$t('transaction.AMOUNT')"
                     :type="classes"
                     :message="errors[0]">
              <b-input
                id="amount"
                expanded
                :custom-class="classes['is-danger']?'is-danger':''"
                v-model="amount"
                :disabled="!transactionType"
                type="text"
                :placeholder="$t('transaction.AMOUNT')"
              ></b-input>
            </b-field>
          </validation-provider>
        </div>
      </div>
      <div class="columns">
        <div class="column">
          <div class="field">
            <label for="comments"></label>
            <quill-editor class="textarea"

                          id="comments"
                          v-model="comment"></quill-editor>
          </div>
        </div>
      </div>
      <div class="columns">
        <div class="column">
          <strong>{{$t('accounts.NEW:BALANCE')}}</strong>
        </div>
        <div class="column">
          {{ newBalance | currencyFilter({
          ...account.bank.currency,
          formatWithSymbol:account.bank.currency.format_with_symbol === 1
        }) }}
        </div>
      </div>
      <div class="columns">
        <b-button expanded
                  :disabled="invalid"
                  @click="executeTransaction"
                  type="is-info">Ejecutar
        </b-button>
      </div>
    </validation-observer>
  </article>
</template>

<script>
import currencyFilter from '../../../currency';

export default {
  name: 'accountsDetails',
  filters: {
    currencyFilter,
  },
  components: {
  },
  data: () => ({
    transactionType: '',
    amount: '',
    comment: '',

  }),
  props: {
    account: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    newBalance() {
      return this.transactionType === 'income'
        ? this.account.balance + parseFloat(this.amount)
        : this.account.balance - parseFloat(this.amount);
    },
    translateType() {
      return this.transactionType === 'income' ? 'Ingreso' : 'Egreso';
    },
  },
  methods: {
    async executeTransaction() {
      this.$buefy.dialog.confirm({
        message: `¿Desea ejecutar una transaccion de a <b>${this.translateType}</b>
por <b>${this.$options.filters.currencyFilter(this.amount, {
    ...this.account.bank.currency,
    formatWithSymbol: this.account.bank.currency.format_with_symbol === 1,
  })}</b> en la cuenta del banco <b>${this.account.bank.name}</b> numero <b>${this.account.number}</b>
Su nuevo saldo será <b>${this.$options.filters.currencyFilter(this.newBalance, {
    ...this.account.bank.currency,
    formatWithSymbol: this.account.bank.currency.format_with_symbol === 1,
  })}</b></b> ?`,
        onConfirm: async () => {
          await $http.post(`/api/transaction/${this.account.id}`, {
            amount: this.amount,
            type: this.transactionType,
            comment: this.comment,
          });
        },
      });
    },
  },

};
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
