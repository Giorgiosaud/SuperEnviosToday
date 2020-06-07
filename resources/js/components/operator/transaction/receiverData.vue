<script>
    import newReceiverForm from './newReceiverForm'
    import newAccount from './newAccount'

    export default {
        name: "receiverData",
        props: {
            client: {
                type: Object | null,
                default: () => null
            },
        },
        components: {
            newReceiverForm,
            newAccount
        },
        data: () => ({
            receiverSelected: null,
            receiverAccount: null,
            receivers: [],
            loadingReceivers: false,
            activeForm: '',
            openModal: false,
        }),

        methods: {
            openModalForm(form) {
                this.openModal = true;
                this.activeForm = form;
            },
            receiverSet() {

            },
            nextStep(){
                this.$emit('receiver-set',{
                    receiver:this.receiverSelected,
                    receiverAccount:this.receiverAccount
                });
                this.$emit('next-step')
            },
            async receiverAdded() {
                return this.lookupForReceivers();
            },
            async accountAdded(){
                return this.lookupForReceivers();
            },
            async lookupForReceivers() {
                this.loadingReceivers = true;
                try {
                    const response = await $http.get(`/api/user/receivers/${this.client.id}`)
                    this.receivers = await response.json()
                } catch (error) {
                    console.log(error)
                } finally {
                    this.loadingReceivers = false;
                }
            }
        },
        computed: {
            customComponentProps() {
                switch (this.activeForm) {
                    case('newReceiverForm'):
                        return {
                            client: this.client

                        }
                    case "newAccount":
                        return{
                            receiver:this.receiverSelected
                        }
                    default:
                        return {}

                }
            },
            receiverAccounts() {
                if (this.receiverSelected)
                    return this.receiverSelected.accounts;
                return [];
            }
        },
        watch: {
            receiverSelected() {
                this.receiverAccount = null
            },
            client: {
                deep: true,
                async handler(value) {
                    if (value) {
                        await this.lookupForReceivers()
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
