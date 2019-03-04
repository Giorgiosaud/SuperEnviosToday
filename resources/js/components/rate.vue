<template>
    <div class="container">
        <div class="col-12 text-center">
            <h1>Rate Setup</h1>
            <div id="graph">
                <vue-frappe
                    id="test"
                    ref="graph"
                    :labels="labels"
                    title="CLP Bs"
                    type="axis-mixed"
                    :height="300"
                    :colors="['light-blue']"
                    :dataSets="this.graphData">
                </vue-frappe>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-5">
                        <label for="new-rate" class="label-base">Nueva Tasa</label>
                        <input type="text" id="new-rate" class="input-base bg-white" v-model="newRate">
                    </div>
                    <div class="col-4">
                        <label for="since" class="label-base bg-white">Aplicar desde:</label>
                        <datetime id="since" class="input-base" type="datetime" :flow="['date', 'time']"
                                  format="dd-MM-yyyy hh:mm"
                                  v-model="since"></datetime>

                    </div>
                    <div class="col-3">
                        <label for="currency" class="label-base bg-white">Seleccione Moneda:</label>
                        <v-select id="currency" :searchable="false" :options="currencies" label="name"
                                  class="input-base"
                                  v-model="selectedCurrency"></v-select>

                    </div>
                </div>
                <div class="row">
                    <div class="col-12 py-3">
                        <button class="btn-primary btn-lg btn-block"
                                :disabled="isDisabledSend" @click="setNewRate">Guardar Tasa
                        </button>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-left" v-for="header in headers">{{header}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="rate in rates">

                            <td v-for="key in keysToShow">
                                <span v-if="key==='amount'">
                                    {{rate[key]|currency}}
                                </span>
                                <span v-else>
                                    {{rate[key]}}
                                </span>
                            </td>
                            <td>
                            <span class="cursor-pointer" @click="removeRate(rate)" data-toggle="modal"
                                  data-target="#user-modal">Eliminar</span>
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

    export default {
        name: "rate",
        data() {
            return {
                rates: [],
                currencies: [],
                selectedCurrency: null,
                newRate: '',
                since: '',
                headers: ['Aplicar desde', 'Tasa'],
                keysToShow: ['since', 'amount'],
                selectedRate: null,
            }
        },
        created() {

            this.getRates();
            this.getCurrencies();
        },
        computed: {
            isDisabledSend() {
                return this.selectedCurrency === null || this.since === '' || this.newRate === '';
            },
            rateValue() {
                let amount = this.newRate.replace(/,/g, '.');
                return parseFloat(amount,10);
            },
            updatedData() {
                return {
                    datasets: [{
                        name: 'CLP',
                        chartType: 'line',
                        values: this.rates.map(rate => rate.amount).reverse()
                    }],
                    labels: this.labels.map(lab=>dateFns.parse(lab)).reverse(),
                    tooltipOptions: {
                        formatTooltipX: d => (d + '').toUpperCase(),
                        formatTooltipY: d => d + ' Bs',
                    }
                }
            },
            graphData() {
                if (this.rates.length > 1) {

                } else {
                    return [{
                        name: "CLP",
                        chartType: 'line',
                        values: [25, 40, 30, 35, 8, 52, 17, -4]
                    }]
                }
            },
            labels() {
                if (this.rates.length > 1) {
                    return this.rates.map(rate => rate.since)
                }
                return [
                    '12am-3am', '3am-6am', '6am-9am', '9am-12pm',
                    '12pm-3pm', '3pm-6pm', '6pm-9pm', '9pm-12am'
                ]
            }

        },
        watch: {
            updatedData() {
                this.$refs.graph.update(this.updatedData);
            }
        },

        methods: {
            setNewRate() {

                axios.post('api/rate', {
                    since: this.since,
                    amount: this.rateValue,
                    currency: this.selectedCurrency
                })
                    .then((response) => {
                        this.rates.unshift(response.data);
                    })
                    .catch(() => {
                        alert('no pudo');
                    });
            },
            getRates() {
                window.axios.get('api/rates').then((response) => {
                    this.rates = response.data.data
                })
            },
            getCurrencies() {
                window.axios.get('api/currencies').then((response) => {
                    this.currencies = response.data;
                })
            }
        }
    }
</script>

<style scoped>
    .btn-primary:disabled {
        background-color: gray;
    }
</style>
