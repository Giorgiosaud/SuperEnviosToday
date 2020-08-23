<script>
import addCurrency from './addCurrency.vue';

export default {
    name: 'CurrenciesList',
    components: {
        addCurrency,
    },
    props: {
        currenciesQuery: {
            type: Object,
            default: () => ({
                data: {},
            }),
        },
    },
    data: () => (
        {
            query: {},
            loading: false,
            filters: {},
            defaultOpenedDetails: [],
            savingCurrency: false,
            isOpenModal: false,
            removingCurrency: false,
        }
    ),
    computed: {
        currencies() {
            return this.query.data;
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
    created() {
        this.query = this.currenciesQuery;
        this.page = this.query.current_page;
    },
    methods: {
        openAddCurrencyModal() {
            this.isOpenModal = true;
        },
        async removeCurrency(id) {
            this.$buefy.dialog.confirm({
                message: 'Continue on this task?',
                onConfirm: async () => {
                    this.removingCurrency = true;
                    try {
                        await $http.delete(`api/currencies/${id}`);
                    } finally {
                        this.removingCurrency = false;
                        await this.loadAsyncData();
                    }
                },
            });
        },
        async changeCurrency(id, name, identifier, sign) {
            this.savingCurrency = true;
            try {
                await $http.patch(`api/currencies/${id}`, {
                    name,
                    identifier,
                    sign,
                });
            } finally {
                this.savingCurrency = false;
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
            this.loading = true;
            const request = await $http.get('/api/currencies', {params: {...params}});
            this.query = await request.json();
            this.loading = false;
        },
    },
};
</script>
