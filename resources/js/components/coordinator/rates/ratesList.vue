<script>
import {GChart} from 'vue-google-charts';
import {format} from 'date-fns';
import addRate from './addRate.vue';
import currencyFilter from '../../../currency';
//TODO add date filter
export default {
    name: 'RatesList',
    components: {
        addRate,
        GChart,
    },
    filters: {
        rateCurrency: (value, selectedCurrency) => {
            const formatOptions = {
                precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
            };
            if (!selectedCurrency) {
                formatOptions.symbol = 'Bs/$ ';
            } else {
                formatOptions.symbol = `Bs/${selectedCurrency.sign} `;
            }
            return currencyFilter(value, formatOptions);
        },
        timeFormat: (value, dateFormat) => format(value, dateFormat),
    },
    props: {
        ratesQuery: {
            type: Object,
            default: () => ({
                data: {},
            }),
        },
        allCurrencies: {
            type: Array,
            default: () => ([]),
        },
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
                allowHtml: true,
            },
            chartEvents: {},
        }
    ),
    computed: {
        rates() {
            return this.query.data;
        },
        adjustedDataForGraph() {
            const orderedData = this.rates.map((rate) => {
                const newRate = rate;
                newRate.since = new Date(newRate.since);
                return newRate;
            })
                .sort((dataA, dataB) => new Date(dataA.since).valueOf() - new Date(dataB.since).valueOf());
            const differentCurrencies = [...new Set(this.rates.map((rate) => rate.currency.name))];
            const differentCurrenciesIds = [...new Set(this.rates.map((rate) => rate.currency_id))];
            const differentDates = [...new Set(orderedData.map((data) => data.since))];
            const titulos = ['Desde', ...differentCurrencies, ...differentCurrencies];
            const chartData = [titulos];

            differentDates.map((date) => {
                const graphData = [date];
                differentCurrenciesIds.map((currencyId) => {
                    const allDataOfCurrency = orderedData.filter((data) => data.currency_id === currencyId);
                    const dataToAppend = allDataOfCurrency.find((data) => date <= data.since);
                    const messageToAppend = allDataOfCurrency.find((data) => date === data.since);

                    graphData.push(dataToAppend ? dataToAppend.amount : allDataOfCurrency[allDataOfCurrency.length - 1].amount);
                    graphData.push(messageToAppend && messageToAppend.message ? messageToAppend.message : undefined);
                    return currencyId;
                });
                chartData.push(graphData);
                return date;
            });

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
        },
    },
    watch: {
        selectedCurrencies() {
            this.loadAsyncData();
        },
    },
    created() {
        this.query = this.ratesQuery;
        this.page = this.query.current_page;
        this.filteredCurrencies = this.allCurrencies;
    },
    methods: {
        getFilteredTags(text) {
            this.filteredCurrencies = this.allCurrencies
                .filter((currency) => currency.name
                    .toString()
                    .toLowerCase()
                    .indexOf(text.toLowerCase()) >= 0);
        },
        openAddRateModal() {
            this.isOpenModal = true;
        },
        async removeRate(id) {
            this.$buefy.dialog.confirm({
                message: '¿Estás seguro de borrar esta tasa de cambio?',
                onConfirm: async () => {
                    this.removingCurrency = true;
                    try {
                        await $http.delete(`api/rates/${id}`);
                    } finally {
                        this.removingCurrency = false;
                        await this.loadAsyncData();
                    }
                },
            });
        },
        // eslint-disable-next-line camelcase
        async changeRate(id, since, currency_id, amount, message) {
            this.savingSetting = true;
            try {
                await $http.patch(`api/rates/${id}`, {
                    since,
                    currency_id,
                    amount,
                    message,
                });
            } finally {
                this.savingSetting = false;
                await this.loadAsyncData();
            }
        },
        toggle(row) {
            this.$refs.currenciesTable.toggleDetails(row);
        },
        paramsToObject(entries) {
            const result = {};
            entries.forEach((entry) => { // each 'entry' is a [key, value] tupple
                const [key, value] = entry;
                result[key] = value;
            });
            return result;
        },
        changedPage(page) {
            this.page = page;
            this.loadAsyncData();
        },

        async loadAsyncData() {
            const params = this.getAllUrlParams(document.location.href)
            params.page = this.page;
            Object.assign(params, this.filters);
            if (this.selectedCurrencies.length) {
                params.currencies = this.selectedCurrencies.map((currency) => currency.id);
            }
            this.loading = true;
            const request = await $http.get('/api/rates', {params: {...params}});
            this.query = await request.json();
            this.loading = false;
        },
    },
};
</script>
