<script>
import addBank from './addBank.vue';

export default {
    name: 'BankLists',
    components: {
        addBank,
    },
    props: {
        banksQuery: {
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
            selected: {},
            filters: {},
            selectedCurrencies: [],
            filteredCurrencies: [],
            defaultOpenedDetails: [5],
            savingName: false,
            isOpenModal: false,
            removingBank: false,
        }
    ),
    computed: {

        currencies() {
            return this.allCurrencies.map((currency) => currency.name);
        },
        banks() {
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
    watch: {
        selectedCurrencies() {
            this.loadAsyncData();
        },
    },
    created() {
        this.query = this.banksQuery;
        this.page = this.query.current_page;
        this.filteredCurrencies = this.allCurrencies;
    },
    methods: {
        openAddBankModal() {
            this.isOpenModal = true;
        },
        async removeBank(id) {
            this.$buefy.dialog.confirm({
                message: 'Continue on this task?',
                onConfirm: async () => {
                    this.removingBank = true;
                    try {
                        await $http.delete(`api/banks/${id}`);
                    } finally {
                        this.removingBank = false;
                        await this.loadAsyncData();
                    }
                },
            });
        },
        async changeName(id, name) {
            this.savingName = true;
            try {
                await $http.patch(`api/banks/${id}`, {
                    name,
                });
            } finally {
                await this.loadAsyncData();
                this.savingName = false;
            }
        },
        toggle(row) {
            this.$refs.banksTable.toggleDetails(row);
        },
        changedPage(page) {
            this.page = page;
            this.loadAsyncData();
        },
        getFilteredTags(text) {
            this.filteredCurrencies = this.allCurrencies
                .filter((currency) => currency.name
                    .toString()
                    .toLowerCase()
                    .indexOf(text.toLowerCase()) >= 0);
        },

        async loadAsyncData() {
            const params = this.getAllUrlParams(document.location.href)
            params.page = this.page;
            Object.assign(params, this.filters);
            if (this.selectedCurrencies.length) {
                params.currencies = this.selectedCurrencies.map((currency) => currency.id);
            }
            this.loading = true;
            const request = await $http.get('/api/banks', {params: {...params}});
            this.query = await request.json();
            this.loading = false;
        },
    },
};
</script>
