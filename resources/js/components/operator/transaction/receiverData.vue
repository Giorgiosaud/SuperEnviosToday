<script>
    import newReceiverForm from './newReceiverForm'
    import editReceiverForm from './editReceiverForm'
    import newAccount from './newAccount'
    import editAccount from './editAccount'

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
            editReceiverForm,
            newAccount,
            editAccount
        },
        data: () => ({
            receiverSelected: null,
            receiverAccount: null,
            receivers: [],
            loadingReceivers: false,
            unlinkingReceiver:false,
            unlinkingAccount:false,
            activeForm: '',
            openModal: false,
        }),

        methods: {
            openModalForm(form) {
                this.openModal = true;
                this.activeForm = form;
            },
            nextStep(){
                this.$emit('receiver-set',{
                    receiver:this.receiverSelected,
                    receiverAccount:this.receiverAccount
                });
                this.$emit('next-step')
            },
            async lookupForReceivers() {
                this.loadingReceivers = true;
                try {
                    const response = await $http.get(`/api/user/receivers/${this.client.id}`)
                    this.receivers = await response.json()
                    this.receiverSelected=null;
                    this.receiverAccount=null;
                } catch (error) {
                    console.log(error)
                } finally {
                    this.loadingReceivers = false;
                }
            },
            async unlinkReceiver(){
                this.unlinkingReceiver = true;
                try {
                    const response = await $http.patch(`/api/account/${this.receiverAccount.id}/user/${this.receiverSelected.id}/unlink`)

                } catch (error) {
                    console.log(error)
                } finally {
                    await this.lookupForReceivers();
                    this.unlinkingReceiver = false;
                }
            },
            async unlinkAccount(){
                this.unlinkingAccount = true;
                try {
                    const response = await $http.patch(`/api/account/${this.receiverAccount.id}/user/${this.receiverSelected.id}/unlink`)

                } catch (error) {
                    console.log(error)
                } finally {
                    await this.lookupForReceivers();
                    this.unlinkingAccount = false;
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
                    case('editReceiverForm'):
                        return {
                            client: this.client,
                            receiver: this.receiverSelected
                        }
                    case "newAccount":
                        return{
                            receiver:this.receiverSelected
                        }
                    case "editAccount":
                        return{
                            receiver:this.receiverSelected,
                            account:this.receiverAccount
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
