<script>
import addCurrency from './addCurrency';

export default {
    name: "currenciesList",
    props: {
        'currenciesQuery': {
            type: Object,
            default: () => ({
                data: {}
            })
        },
    },
    components: {
        addCurrency
    },
    data: () => (
        {
            query: {},
            loading: false,
            filters: {},
            defaultOpenedDetails: [],
            savingCurrency: false,
            isOpenModal: false,
            removingCurrency: false
        }
    ),
    created() {
        this.query = this.currenciesQuery;
        this.page = this.query.current_page;

    },
    computed: {
        currencies() {
            return this.query.data;
        }
        ,
        currentPage() {
            return this.query.currentPage ? this.query.currentPage : 0;
        }
        ,
        lastPage() {
            return this.query.last_page ? this.query.last_page : 0;
        }
        ,
        path() {
            return this.query.path ? this.query.path : '';
        }
    }
    ,
    methods: {
        openAddCurrencyModal() {
            this.isOpenModal = true;
        },
        async removeCurrency(id) {
            this.$buefy.dialog.confirm({
                message: 'Continue on this task?',
                onConfirm: async () => {
                    this.removingCurrency = true
                    try {
                        await $http.delete(`api/currencies/${id}`)
                    } finally {
                        this.removingCurrency = false
                        await this.loadAsyncData();
                    }
                }
            })

        },
        async changeCurrency(id, name, identifier, sign) {
            this.savingCurrency = true
            try {
                await $http.patch(`api/currencies/${id}`, {
                    name,
                    identifier,
                    sign
                })
            } finally {
                this.savingCurrency = false;
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
            this.loading = true;
            const request = await $http.get('/api/currencies', {params: {...params}})
            this.query = await request.json();
            this.loading = false;

        },
    },
}
</script>
