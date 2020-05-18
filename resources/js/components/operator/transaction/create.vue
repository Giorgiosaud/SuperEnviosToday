<script>
    import clientData from './clientData'
    import transactionData from './transactionData'
    import receiverData from './receiverData'
    export default {
        name: "transactionCreate",
        components:{
            clientData,
            transactionData,
            receiverData
        },
        data:()=>({
            clientReady:true,
            client:{},
            actualStep:0,
            transaction:null,
        }),
        methods:{
            async clientDataSet(client){
                this.client=client
            },
            nextStep(){
                this.actualStep++
            },
            clientReceiverSet(receiverData){
            },
            transactionDataSet(transactionData){
                this.$set(this,'transaction',transactionData)

            }
        }

    }

</script>

<style scoped>

</style>
