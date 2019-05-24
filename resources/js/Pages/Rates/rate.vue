<template>
  <div class="container">
    <div class="col-12 text-center">
      <h1>Rate Setup</h1>
      <div
        id="graph"
        :class="{
          invisible:!selectedCurrency
        }"
      >
        <vue-frappe
          id="test"
          ref="graph"
          :labels="labels"
          :height="300"
          :colors="['light-blue']"
          :data-sets="graphData"
          title="CLP Bs"
          type="axis-mixed"
        />
      </div>
      <div class="container">
        <div class="row">
          <div class="col-12">
            <label
              for="currency"
              class="label-base bg-white"
            >Seleccione Moneda:</label>
            <v-select
              id="currency"
              v-model="selectedCurrency"
              :searchable="false"
              :options="currencies"
              label="name"
              class="input-base"
            />
          </div>
        </div>
        <div class="row">
          <div class="col-5">
            <label
              for="new-rate"
              class="label-base"
            >Nueva Tasa</label>
            <input
              id="new-rate"
              v-model="newRate"
              type="text"
              class="input-base bg-white"
            >
          </div>
          <div class="col-7">
            <label
              for="since"
              class="label-base bg-white"
            >Aplicar desde:</label>
            <input
              id="since"
              v-model="since"
              type="datetime-local"
              class="input-base"
            >
          </div>
        </div>
        <div class="row">
          <div class="col-12 py-3">
            <button
              :disabled="isDisabledSend"
              class="btn-primary btn-lg btn-block"
              @click="setNewRate"
            >
              Guardar Tasa
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
                  v-for="(header, headerId) in headers"
                  :key="headerId"
                  class="text-left"
                >
                  {{ header }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(rate,rateId) in selectedRates"
                :key="rateId"
              >
                <td
                  v-for="(key, keyId) in keysToShow"
                  :key="keyId"
                  class="text-left"
                >
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
                    @click="removeRate(rate)"
                  >
                    Eliminar
                  </button>
                  <button
                    class="btn btn-primary btn-xs"
                    data-toggle="modal"
                    data-target="#user-modal"
                    @click="editRate(rate)"
                  >
                    Editar
                  </button>
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
import { format, parse, subHours } from 'date-fns';

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
    selectedRates() {
      if (!this.selectedCurrency) {
        return null;
      }
      return this.rates.filter(rate => rate.currency_id === this.selectedCurrency.id);
    },
    isDisabledSend() {
      return this.selectedCurrency === null || this.since === '' || this.newRate === '';
    },
    rateValue() {
      const amount = this.newRate.replace(/,/g, '.');
      return parseFloat(amount, 10);
    },
    updatedData() {
      if (!this.selectedCurrency) {
        return {
          datasets: [{
            name: 'CLP',
            chartType: 'line',
            values: [1, 2, 4],
          }],
          labels: ['1', '2', '4'],
          tooltipOptions: {

            formatTooltipX: d => (`${d}`).toUpperCase(),
            formatTooltipY: d => `${d} Bs`,
          },
        };
      }
      return {
        datasets: [{
          name: this.selectedCurrency.name,
          chartType: 'line',
          values: this.selectedRates.map(rate => rate.amount).reverse(),
        }],
        labels: this.labels.map(lab => parse(lab)).reverse(),
        tooltipOptions: {
          formatTooltipX: d => (`${d}`).toUpperCase(),
          formatTooltipY: d => `${d} Bs`,
        },
      };
    },
    graphData() {
      if (this.selectedRates) {
        return [{
          name: this.selectedCurrency.name,
          chartType: 'line',
          values: this.selectedRates,
        }];
      }
      return [{
        name: 'CLP',
        chartType: 'line',
        values: [25, 40, 30, 35, 8, 52, 17, -4],
      }];
    },
    since2() {
      return subHours(parse(this.since), 4);
    },
    labels() {
      if (this.selectedRates) {
        return this.selectedRates.map(rate => rate.since);
      }
      return [
        '12am-3am', '3am-6am', '6am-9am', '9am-12pm',
        '12pm-3pm', '3pm-6pm', '6pm-9pm', '9pm-12am',
      ];
    },

  },
  updated() {
    if (this.$refs.graph) this.$refs.graph.update(this.updatedData);
  },
  created() {
    this.getRates();
    this.getCurrencies();
  },

  methods: {
    editRate(rate) {
      this.rateId = rate.id;
      this.newRate = rate.amount.toString().replace(',', '').replace('.', ',');
      this.since = parse(rate.since, 'DD-MM-YYYY hh:mm');
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
          since: this.since2,
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
          since: this.since2,
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
      window.axios.get('api/foreign_currencies').then((response) => {
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
