<script>
import addAccount from './addAccount.vue';
import currencyFilter from '../../../currency';

export default {
    name: 'AccountsLists',
    filters: {
        currencyFilter,
    },
    components: {
        addAccount,
    },
    props: {
        accountsQuery: {
            type: Object,
            default: () => ({
                data: {},
            }),
        },
        allCurrencies: {
            type: Array,
            default: () => ([]),
        },
        allBanks: {
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
            selectedBanks: [],
            filteredCurrencies: [],
            filteredBanks: [],
            defaultOpenedDetails: [5],
            savingName: false,
            isOpenModal: false,
            removingAccount: false,
        }
    ),
    computed: {

        currencies() {
            return this.allCurrencies.map((currency) => currency.name);
        },
        accounts() {
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
        selectedBanks() {
            this.loadAsyncData();
        },
    },
    created() {
        this.query = this.accountsQuery;
        this.page = this.query.current_page;
        this.filteredCurrencies = this.allCurrencies;
        this.filteredBanks = this.allBanks;
    },
    methods: {
        deleteAccount(id){
            console.log(id)
        },
        openAddAccountModal() {
            this.isOpenModal = true;
        },
        async removeAccount(id) {
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
                await $http.patch(`api/accounts/${id}`, {
                    name,
                });
            } finally {
                await this.loadAsyncData();
                this.savingName = false;
            }
        },
        toggle(row) {
            this.$refs.accountsTable.toggleDetails(row);
        },

        changedPage(page) {
            this.page = page;
            this.loadAsyncData();
        },
        getFilteredCurrenciesTags(text) {
            this.filteredCurrencies = this.allCurrencies
                .filter((currency) => currency.name
                    .toString()
                    .toLowerCase()
                    .indexOf(text.toLowerCase()) >= 0);
        },
        getFilteredBanksTags(text) {
            this.filteredBanks = this.allBanks
                .filter((bank) => bank.name
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
            if (this.selectedBanks.length) {
                params.banks = this.selectedBanks.map((bank) => bank.id);
            }
            this.loading = true;
            const request = await $http.get('/api/accounts', {params: {...params}});
            this.query = await request.json();
            this.loading = false;
        },
    },
};
</script>
