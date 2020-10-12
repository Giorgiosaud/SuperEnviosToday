<template>
  <section class="section">
    <nav class="level">
      <div class="level-item has-text-centered">
        <div>
          <p class="heading" v-if="!tryAnotherRate">{{$t('transaction.EXCHANGE:RATE')}}</p>
          <p class="title" v-if="tryAnotherRate">
            {{$t('transaction.RATE:NEW')}}: {{ newRate|rateCurrency(selectedCurrency) }}
          </p>
          <p :class="{'sub-title':tryAnotherRate,'title':!tryAnotherRate}">
            <span v-if="tryAnotherRate">{{$t('transaction.EXCHANGE:RATE')}}:</span>
            {{ exchangeRate|rateCurrency(selectedCurrency) }}
          </p>
          <p class="sub-title" v-if="tryAnotherRate"
             :class="{'has-text-danger':newRate-exchangeRate>0,'has-text-success':newRate-exchangeRate<=0}">
            Diff: ({{ newRate-exchangeRate|currency }})</p>
        </div>
      </div>
      <div class="level-item has-text-centered">
        <div>
          <p class="heading">{{$t('transaction.RATE:CALC')}}</p>
          <p class="title">{{calcExchange|currency({
            symbol: 'Bs ', precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
          })}}</p>
        </div>
      </div>
    </nav>
    <validation-observer v-slot="{invalid}" ref="fields" slim>
      <section class="section is-paddingless">

        <div class="columns">
          <div class="column is-narrow">
            <validation-provider
              rules="required"
              v-slot="{ classes,errors,valid }"
              name="$t('transaction.CURRENCY')"
              tag="div"
              class="control">
              <label class="label" for="idn_type">{{$t('transaction.CURRENCY')}}</label>
              <div class="control has-icons-left has-icons-right">
                <div class="select"
                     :class="classes">
                  <select
                    id="idn_type"
                    name="idn_type"
                    v-model="selectedCurrency">
                    <option value="">{{$t('transaction.DEFAULT:CURRENCY')}}</option>
                    <option v-for="foreignCurrency in foreignCurrencies"
                            :key="foreignCurrency.id"
                            :value="foreignCurrency">{{foreignCurrency.name}}
                      ({{foreignCurrency.identifier}})
                    </option>
                  </select>
                  <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                </div>
                <span class="icon is-small is-left">
                            <font-awesome-icon icon="coins"></font-awesome-icon>
                        </span>
              </div>
              <strong
                v-if="errors[0]"
                class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
          </div>
          <div class="column is-narrow">
            <validation-provider
              rules="required"
              v-slot="{ classes,errors,valid }"
              name="$t('transaction.ACCOUNTS')"
              tag="div"
              vid="selectedAccount"
              class="control">
              <label class="label" for="idn_type2">{{$t('transaction.ACCOUNTS')}}</label>
              <div class="control has-icons-left has-icons-right">
                <div class="select"
                     :class="classes">
                  <select
                    :disabled="!accountsOfCurrency.length"
                    id="idn_type2"
                    name="idn_type2"
                    v-model="selectedAccount">
                    <option value="">{{$t('transaction.DEFAULT:ACCOUNT')}}</option>
                    <option v-for="account in accountsOfCurrency"
                            :key="account.id"
                            :value="account">{{account.bank.name}} ({{account.number}})
                    </option>
                  </select>
                  <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                </div>
                <span class="icon is-small is-left">
                            <font-awesome-icon icon="piggy-bank"></font-awesome-icon>
                        </span>
              </div>
              <strong
                v-if="errors[0]"
                class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
          </div>
          <div class="column is-narrow">
            <validation-provider
              :rules="`required|min_value:${minAmount}`"
              :name="$t('transaction.AMOUNT')"
              v-slot="{ classes,errors,valid}"
              tag="div"
              class="control">
              <label class="label" for="idn">{{$t('transaction.AMOUNT')}}</label>
              <div class="control has-icons-right">
                <money id="amount"
                       v-model="amount"
                       :disabled="!selectedAccount"
                       v-bind="clp"
                       name="amount"
                       class="input"
                       :class="classes"
                       type="text"
                       :placeholder="$t('transaction.AMOUNT')"
                       autocomplete="amount" autofocus></money>
                <span class="icon is-small has-text-warning is-right"
                      v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
              </div>
              <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
          </div>
        </div>
        <div class="columns">
          <validation-provider tag="div" class="column is-narrow" vid="tryAnotherRate">
            <label class="checkbox">
              <input type="checkbox" v-model="tryAnotherRate">
              {{$t('transaction.RATE:CHANGE')}}
            </label>
          </validation-provider>
        </div>
        <div class="columns" v-if="tryAnotherRate">
          <div class="column is-narrow">
            <validation-provider
              rules="required_if:tryAnotherRate,true|min_value:0.00000001"
              name="$t('transaction.RATE:NEW')"
              v-slot="{ classes,errors,valid}"
              tag="div"
              class="control">
              <label class="label" for="idn">{{$t('transaction.RATE:NEW')}}</label>
              <div class="control has-icons-right">
                <money id="idn"
                       v-model="newRate"
                       v-bind="clp"
                       name="newRate"
                       class="input"
                       :class="classes"
                       type="text"
                       :placeholder="$t('transaction.RATE:NEW')"
                >

                </money>
                <span class="icon is-small has-text-warning is-right"
                      v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
              </div>
              <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
          </div>
        </div>

      </section>
      <section class="section is-paddingless">
        <div class="columns" v-if="selectedAccount && selectedAccount.bank.name!=='Efectivo'">
          <div class="column">
            <validation-provider
              rules="required"
              name="$t('transaction.VOUCHER:NUMBER')"
              v-slot="{ classes,errors,valid}"
              tag="div"
              class="control">
              <label class="label" for="voucher">
                {{$t('transaction.VOUCHER:NUMBER')}}
              </label>
              <div class="control has-icons-right">
                <input id="voucher"
                       v-model="voucher"
                       name="voucher"
                       class="input"
                       :class="classes"
                       type="text"
                       :placeholder="$t('transaction.VOUCHER:NUMBER')"

                       autocomplete="idn" autofocus/>
                <span class="icon is-small has-text-warning is-right" v-if="errors[0]">
                                    <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                                </span>
                <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
              </div>
              <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
          </div>
        </div>
        <validation-provider
          rules="required|min:1"
          v-if="selectedAccount && selectedAccount.bank.name!=='Efectivo'"
          name="$t('transaction.VOUCHER:FILES')"
          v-slot="{ classes,errors}"
          tag="div"
          class="control">
          <uppy-uploader
            :class="classes"
            v-model="uploadedFiles"
            :max-file-size-in-bytes="1000000">
          </uppy-uploader>
          <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
        </validation-provider>

      </section>
      <div class="columns has-padding-top-5" v-if="selectedAccount && selectedAccount.bank.name!=='Efectivo'">
        <div class="column">
          <b-button size="is-big"
                    type="is-info"
                    :loading="verifyingTransaction"
                    icon-right="search-dollar"
                    @click="verifyTransactionNumberAsUnique">
            {{$t('transaction.VERIFY:BUTTON')}}
          </b-button>
        </div>
        <Validation-provider rules="required|is:ok" name="canGoOn" >
          <input type="hidden" v-model="canGoOn">
        </Validation-provider>
      </div>
      <div class="columns has-padding-top-5">
        <div class="column">
          <b-button size="is-big"
                    type="is-info"
                    :loading="savingTransaction"
                    :disabled="invalid"
                    icon-right="arrow-circle-right"
                    @click="nextStep">
            {{$t('transaction.NEXT:BUTTON')}}
          </b-button>
        </div>
      </div>
    </validation-observer>
    <b-modal :active.sync="isComponentModalActive"
             has-modal-card
             trap-focus
             :destroy-on-hide="false"
             aria-role="dialog"
             aria-modal>
      <transaction-invalid @continuar="continueWithThat" :datos-de-transaccion="datosDeTransaccionRepetida">
      </transaction-invalid>
    </b-modal>
  </section>
</template>
<script>
import { Money } from 'v-money';
import { v4 as uuidv4 } from 'uuid';
import currency from '../../../currency';
import UppyUploader from '../../../UppyUploader.vue';
import transactionInvalid from './transactionInvalid.vue';

export default {
  name: 'TransactionData',
  props: {
    minAmount: {
      type: Number,
      default: 10,
    },
  },
  filters: {
    currency,
    rateCurrency: (value, selectedCurrency) => {
      const formatOptions = {
        precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
      };
      if (!selectedCurrency) {
        formatOptions.symbol = 'Bs/$ ';
      } else {
        formatOptions.symbol = `Bs/${selectedCurrency.sign} `;
      }
      return currency(value, formatOptions);
    },
  },
  components: {
    Money,
    UppyUploader,
    transactionInvalid,
  },
  data: () => ({
    verifyingTransaction: false,
    datosDeTransaccionRepetida: null,
    canGoOn: '',
    isComponentModalActive: false,
    transaction: '',
    selectedCurrency: null,
    selectedAccount: null,
    foreignCurrencies: [],
    accountsOfCurrency: [],
    exchangeRate: 0,
    tryAnotherRate: false,
    savingTransaction: false,
    newRate: '',
    amount: '',
    voucher: '',
    uploadedFiles: [],
    tempUploadFiles: [],
    bs: {
      decimal: ',',
      thousands: '.',
      prefix: 'Bs ',
      suffix: ' ',
      precision: 0,
      masked: false,
    },
    clp: {
      decimal: ',',
      thousands: '.',
      prefix: '$ ',
      suffix: ' ',
      precision: 0,
      masked: false,
    },
  }),
  computed: {
    calcExchange() {
      if (this.tryAnotherRate) {
        return this.newRate * this.amount;
      }
      return this.exchangeRate * this.amount;
    },
  },
  watch: {
    voucher() {
      this.canGoOn = '';
    },
    selectedAccount(value) {
      if (value && value.bank && value.bank.name === 'Efectivo') {
        this.voucher = uuidv4();
        this.canGoOn = 'ok';
      } else {
        this.canGoOn = '';
        this.voucher = '';
      }
    },
    async selectedCurrency(value, old) {
      if (value !== old) {
        const response = await $http.get(`/api/rate/${value.id}`);
        const exchangeRate = await response.json();
        const response2 = await $http.get(`/api/accounts/${value.id}`);
        const accountsOfCurrency = await response2.json();
        this.accountsOfCurrency = accountsOfCurrency;
        this.exchangeRate = exchangeRate.amount;
      }
    },
  },
  async created() {
    const response = await $http.get('/api/currency/foreign');
    const foreignCurrencies = await response.json();
    this.foreignCurrencies = foreignCurrencies;
  },
  methods: {
    continueWithThat() {
      this.canGoOn = 'ok';
      this.$refs.fields.validate();
      this.isComponentModalActive = false;
    },
    nextStep() {
      this.$emit('transaction-set', {
        currency: this.selectedCurrency,
        foreignAccount: this.selectedAccount,
        amount: this.amount,
        bsAmount: this.calcExchange,
        voucher: this.voucher,
        exchangeRate: this.tryAnotherRate ? this.newRate : this.exchangeRate,
        tryAnotherRate: this.tryAnotherRate,
        uploadedFiles: this.uploadedFiles,
      });
    },
    async verifyTransactionNumberAsUnique() {
      try {
        const response = await $http.get(`/api/transaction/verify/${this.voucher}`);
        if (response.status === 204) {
          this.canGoOn = 'ok';
        } else {
          const data = await response.json();
          this.canGoOn = '';
          this.isComponentModalActive = true;
          this.datosDeTransaccionRepetida = data;
        }
      } catch (error) {
        this.$buefy.notification.open({
          message: error.message,
          type: 'is-warning',
          position: 'is-bottom-right',
          duration: 5000,
        });
      }
    },
  },

};

</script>

<style lang="scss" scoped>
    .is-success ::v-deep.uppy-Root{
        border:dashed 1px green;
        border-radius: 5px;
    }
    .is-danger ::v-deep.uppy-Root{
        border:dashed 1px red;
        border-radius: 5px;
    }
</style>
