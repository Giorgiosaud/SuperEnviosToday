<template>
  <div class="container">
    <div class="col-12 text-center">
      <h1>Rate Setup</h1>
      <div id="graph">
        <vue-frappe
          id="test"
          ref="graph"
          :labels="labels"
          :height="300"
          :colors="['light-blue']"
          :data-sets="this.graphData"
          title="CLP Bs"
          type="axis-mixed"/>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-5">
            <label
              for="new-rate"
              class="label-base">Nueva Tasa</label>
            <input
              id="new-rate"
              v-model="newRate"
              type="text"
              class="input-base bg-white">
          </div>
          <div class="col-4">
            <label
              for="since"
              class="label-base bg-white">Aplicar desde:</label>
            <datetime
              id="since"
              :flow="['date', 'time']"
              v-model="since"
              class="input-base"
              type="datetime"
              format="dd-MM-yyyy hh:mm"/>

          </div>
          <div class="col-3">
            <label
              for="currency"
              class="label-base bg-white">Seleccione Moneda:</label>
            <v-select
              id="currency"
              :searchable="false"
              :options="currencies"
              v-model="selectedCurrency"
              label="name"
              class="input-base"/>

          </div>
        </div>
        <div class="row">
          <div class="col-12 py-3">
            <button
              :disabled="isDisabledSend"
              class="btn-primary btn-lg btn-block"
              @click="setNewRate">Guardar Tasa
            </button>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th
                  v-for="header in headers"
                  class="text-left">{{ header }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="rate in rates">

                <td
                  v-for="key in keysToShow"
                  class="text-left">
                  <span v-if="key==='since'">
                    {{ format(rate[key],'DD-MM-YYYY hh:mm:ss') }}
                  </span>
                  <span v-else-if="key==='amount'">
                    {{ rate[key]|currency }}
                  </span>
                  <span v-else>
                    {{ rate[key] }}
                  </span>
                </td>
                <td class="text-left">
                  <button
                    class="btn btn-danger btn-xs"
                    data-toggle="modal"
                    data-target="#user-modal"
                    @click="removeRate(rate)">Eliminar</button>
                  <button
                    class="btn btn-primary btn-xs"
                    data-toggle="modal"
                    data-target="#user-modal"
                    @click="editRate(rate)">Editar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import dateFns from 'date-fns';
import { format, parse } from 'date-fns';

export default {
  name: 'Rate',
  data() {
    return {
      rates: [],
      currencies: [],
      selectedCurrency: null,
      newRate: '',
      since: '',
      rateId: null,
      headers: ['Aplicar desde', 'Tasa', 'Accion'],
      keysToShow: ['since', 'amount'],
      selectedRate: null,
    };
  },
  computed: {

    isDisabledSend() {
      return this.selectedCurrency === null || this.since === '' || this.newRate === '';
    },
    rateValue() {
      const amount = this.newRate.replace(/,/g, '.');
      return parseFloat(amount, 10);
    },
    updatedData() {
      return {
        datasets: [{
          name: 'CLP',
          chartType: 'line',
          values: this.rates.map(rate => rate.amount).reverse(),
        }],
        labels: this.labels.map(lab => dateFns.parse(lab)).reverse(),
        tooltipOptions: {
          formatTooltipX: d => (`${d}`).toUpperCase(),
          formatTooltipY: d => `${d} Bs`,
        },
      };
    },
    graphData() {
      if (this.rates.length > 1) {

      } else {
        return [{
          name: 'CLP',
          chartType: 'line',
          values: [25, 40, 30, 35, 8, 52, 17, -4],
        }];
      }
    },
    labels() {
      if (this.rates.length > 1) {
        return this.rates.map(rate => rate.since);
      }
      return [
        '12am-3am', '3am-6am', '6am-9am', '9am-12pm',
        '12pm-3pm', '3pm-6pm', '6pm-9pm', '9pm-12am',
      ];
    },

  },
  watch: {
    updatedData() {
      this.$refs.graph.update(this.updatedData);
    },
  },
  created() {
    this.getRates();
    this.getCurrencies();
  },

  methods: {
    editRate(rate) {
      this.rateId = rate.id;
      this.newRate = rate.amount.toString().replace(',', '').replace('.', ',');
      this.since = format(rate.since, 'DD-MM-YYYY hh:mm');
      this.selectedCurrency = this.currencies.find(curr => curr.id === rate.currency_id);
    },
    removeRate(rate) {
      const $result = confirm('quieres borrar esta Tasa');
      if ($result) {
        window.axios.delete(`api/rate/${rate.id}`);
        const index = this.rates.findIndex(rat => rat.id === rate.id);
        this.rates.splice(index, 1);
      }
    },
    format(date, formato) {
      return format(date, formato);
    },
    setNewRate() {
      if (!this.rateId) {
        axios.post('api/rate', {
          since: this.since,
          amount: this.rateValue,
          currency: this.selectedCurrency,
        })
          .then((response) => {
            this.rates.unshift(response.data);
          })
          .catch(() => {
            alert('no pudo');
          });
      } else {
        axios.patch(`api/rate/${this.rateId}`, {
          since: this.since,
          amount: this.rateValue,
          currency: this.selectedCurrency,
        })
          .then((response) => {
              this.rates.shift();
            this.rates.unshift(response.data);
          })
          .catch(() => {
            alert('no pudo');
          });
      }
    },
    getRates() {
      window.axios.get('api/rates').then((response) => {
        this.rates = response.data.data;
      });
    },
    getCurrencies() {
      window.axios.get('api/currencies').then((response) => {
        this.currencies = response.data;
      });
    },
  },
};
</script>

<style scoped>
    .btn-primary:disabled {
        background-color: gray;
    }
</style>
