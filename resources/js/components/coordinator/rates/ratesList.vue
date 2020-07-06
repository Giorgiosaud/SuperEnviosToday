<script>
import addRate from './addRate';
import currencyFilter from '../../../currency';
import {GChart} from 'vue-google-charts'
import {format} from 'date-fns';

export default {
    name: "ratesList",
    props: {
        'ratesQuery': {
            type: Object,
            default: () => ({
                data: {}
            })
        },
        'allCurrencies': {
            type: Array,
            default: () => ([])
        }
    },
    components: {
        addRate,
        GChart
    },
    data: () => (
        {
            query: {},
            loading: false,
            filters: {},
            selectedCurrencies: [],
            filteredCurrencies: [],
            defaultOpenedDetails: [],
            savingRate: false,
            isOpenModal: false,
            removingRate: false,
            chartOptions: {
                allowHtml:true
            },
            chartEvents: {}
        }
    ),
    filters: {
        rateCurrency: (value, selectedCurrency) => {
            const formatOptions = {
                precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
            }
            if (!selectedCurrency) {
                formatOptions.symbol = 'Bs/$ ';
            } else {
                formatOptions.symbol = `Bs/${selectedCurrency.sign} `;
            }
            return currencyFilter(value, formatOptions)
        },
        timeFormat: (value, dateFormat) => {
            return format(value, dateFormat)
        }
    },
    created() {
        this.query = this.ratesQuery;
        this.page = this.query.current_page;
        this.filteredCurrencies = this.allCurrencies;

    },
    computed: {
        rates() {
            return this.query.data;
        },
        adjustedDataForGraph() {
            const orderedData = this.rates.map(rate => {
                const newRate = rate;
                newRate.since = new Date(newRate.since);
                return newRate;
            })
                .sort((dataA, dataB) => {
                    return new Date(dataA.since).valueOf() - new Date(dataB.since).valueOf()
                });
            const differentCurrencies = [...new Set(this.rates.map(rate => rate.currency.name))]
            const differentCurrenciesIds = [...new Set(this.rates.map(rate => rate.currency_id))]
            const differentDates = [...new Set(orderedData.map(data => data.since))];
            const titulos = ['Desde', ...differentCurrencies,...differentCurrencies];
            const chartData = [titulos]

            differentDates.map(date=>{
                const graphData=[date]
                differentCurrenciesIds.map(currencyId=>{
                     const allDataOfCurrency=orderedData.filter(data=>{
                        return data.currency_id==currencyId
                    });
                    const dataToAppend=allDataOfCurrency.find(data=>{
                        return date<=data.since;
                    })
                    const messageToAppend=allDataOfCurrency.find(data=>{
                        return date==data.since;
                    })

                    graphData.push(dataToAppend?dataToAppend.amount:allDataOfCurrency[allDataOfCurrency.length-1].amount)
                    graphData.push(messageToAppend&&messageToAppend.message?messageToAppend.message:undefined);

                })
                chartData.push(graphData);
            })

            return chartData;
        },
        currentPage() {
            return this.query.currentPage ? this.query.currentPage : 0;
        },
        lastPage() {
            return this.query.last_page ? this.query.last_page : 0;
        },
        path() {
            return this.query.path ? this.query.path : '';
        }
    }
    ,
    methods: {
        getFilteredTags(text) {
            this.filteredCurrencies = this.allCurrencies
                .filter((currency) => {
                    return currency.name
                        .toString()
                        .toLowerCase()
                        .indexOf(text.toLowerCase()) >= 0
                })
        },
        openAddRateModal() {
            this.isOpenModal = true;
        },
        async removeRate(id) {
            this.$buefy.dialog.confirm({
                message: '¿Estás seguro de borrar esta tasa de cambio?',
                onConfirm: async () => {
                    this.removingCurrency = true
                    try {
                        await $http.delete(`api/rates/${id}`)
                    } finally {
                        this.removingCurrency = false
                        await this.loadAsyncData();
                    }
                }
            })

        },
        async changeRate(id, since, currency_id, amount,message) {
            this.savingSetting = true
            try {
                await $http.patch(`api/rates/${id}`, {
                    since,
                    currency_id,
                    amount,
                    message
                })
            } finally {
                this.savingSetting = false;
                await this.loadAsyncData();
            }
        },
        toggle(row) {
            this.$refs.currenciesTable.toggleDetails(row)
        },
        paramsToObject(entries) {
            let result = {}
            for (let entry of entries) { // each 'entry' is a [key, value] tupple
                const [key, value] = entry;
                result[key] = value;
            }
            return result;
        },
        changedPage(page) {
            this.page = page;
            this.loadAsyncData();
        },
        changedFilter(filters) {
            for (const filter in filters) {
                if (filters[filter] === '') {
                    delete filters[filter]
                }
            }
            this.filters = filters;
            this.loadAsyncData();
        },
        async loadAsyncData() {
            const urlParams = new URLSearchParams(document.location.search.substring(1));
            const entries = urlParams.entries();
            const params = this.paramsToObject(entries);
            params.page = this.page;
            Object.assign(params, this.filters);
            if (this.selectedCurrencies.length) {
                params.currencies = this.selectedCurrencies.map(currency => currency.id)
            }
            this.loading = true;
            const request = await $http.get('/api/rates', {params: {...params}})
            this.query = await request.json();
            this.loading = false;

        },
    },
    watch: {
        selectedCurrencies() {
            this.loadAsyncData()
        }
    }
}
</script>
