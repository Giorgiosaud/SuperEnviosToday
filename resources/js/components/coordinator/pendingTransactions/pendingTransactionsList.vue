<script>
    import currency from "../../../currency";
    export default {
        name: "pendingTransactionList",
        filters:{
            currency
        },
        props: {
            pendingTransactionsQuery: {
                type: Object,
                default: () => ({
                    data: {}
                })
            },
        },
        data: () => (
            {
                query: {},
                loading: false,
                selected: {},
                filters: {},
                selectedRoles: [],
                filteredRoles:[],
                onChangeState:false,
            }
        ),
        created() {
            this.query = this.pendingTransactionsQuery;
            this.page = this.query.current_page;
        },
        computed: {
            pendingTransactions() {
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
            pendingTransactionClicked(pendingTransaction) {
                if (pendingTransaction.id === this.selected.id) {
                    this.selected = {}
                }
            }
            ,
            goToDetails() {
                window.location.href = `pendingTransaction/${this.selected.id}`;
            }
            ,
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
                if(this.selectedRoles.length){
                    params.roles=this.selectedRoles.map(role=>role.name_id)
                }
                console.log(params)
                this.loading = true;
                const request = await axios.get('api/pending-transactions', {params: {...params}})
                this.query = request.data;
                this.loading = false;

            },
            getFilteredTags(text) {
                this.filteredRoles = this.allRoles
                    .filter((role) => {
                        return role.name
                            .toString()
                            .toLowerCase()
                            .indexOf(text.toLowerCase()) >= 0
                    })
                console.log(this.filteredRoles);
            },
            approveTransation(transactionId) {
                this.onChangeState = true;
                axios.patch(`api/pending_transaction/${transactionId}`,{
                    'accept_transaction':true
                }).then(() => {
                    alert('ok');
                    this.getPendingTransactions();
                }).finally(() => {
                    this.onChangeState = false;
                });
            },
            rejectTransation(transactionId) {
                this.onChangeState = true;
                axios.patch(`api/pending_transaction/${transactionId}`,
                    {
                    'accept_transaction':false
                },{
                        withCredentials:true,
                    }).then(() => {
                    alert('ok');
                    this.getPendingTransactions();
                }).finally(() => {
                    this.onChangeState = false;
                });
            },
        },
        watch:{
            selectedRoles(){
                this.loadAsyncData()
            }
        }
    }
</script>
<style lang="stylus">
    .b-table .table td.is-sticky{
        color:#00c4a7
    }
</style>
