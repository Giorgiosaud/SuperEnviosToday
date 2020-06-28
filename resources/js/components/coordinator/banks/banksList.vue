<script>
import addBank from './addBank';

export default {
    name: "bankLists",
    props: {
        'banksQuery': {
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
        addBank
    },
    data: () => (
        {
            query: {},
            loading: false,
            selected: {},
            filters: {},
            selectedCurrencies: [],
            filteredCurrencies: [],
            defaultOpenedDetails: [],
            savingName: false,
            isOpenModal: false,
            removingBank: false
        }
    ),
    created() {
        this.query = this.banksQuery;
        this.page = this.query.current_page;
        this.filteredCurrencies = this.allCurrencies;

    },
    computed: {
        currencies() {
            return this.allCurrencies.map(currency => currency.name)
        },
        banks() {
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
        openAddBankModal() {
            this.isOpenModal = true;
        },
        async removeBank(id) {
            this.$buefy.dialog.confirm({
                message: 'Continue on this task?',
                onConfirm: async () => {
                    this.removingBank = true
                    try {
                        await $http.delete(`api/banks/${id}`)
                    } finally {
                        this.removingBank = false
                        await this.loadAsyncData();
                    }
                }
            })

        },
        async changeName(id, name) {
            this.savingName = true
            try {
                await $http.patch(`api/banks/${id}`, {
                    name
                })
            } finally {
                await this.loadAsyncData();
                this.savingName = false
            }
        },
        toggle(row) {
            this.$refs.banksTable.toggleDetails(row)
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
            console.log(filters)
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
            const request = await $http.get('/api/banks', {params: {...params}})
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
