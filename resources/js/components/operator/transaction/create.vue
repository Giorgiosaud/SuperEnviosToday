<script>
    import clientData from './clientData'
    import transactionData from './transactionData'
    import receiverData from './receiverData'
    import venezuelanOperatorData from './venezuelanOperatorData'
    export default {
        name: "transactionCreate",
        components:{
            clientData,
            transactionData,
            receiverData,
            venezuelanOperatorData
        },
        data:()=>({
            clientData:null,
            actualStep:0,
            transactionData:null,
            receiverData:null,
            venezuelanOperatorData:null,
        }),
        methods:{
            async clientDataSet(client){
                this.clientData=client
            },

            nextStep(){
                this.actualStep++
            },
            clientReceiverSet(receiverData){
                this.receiverData=receiverData;
            },
            transactionDataSet(transactionData){
                this.transactionData=transactionData;
            },
            venezuelanOperatorDataSet(transactionData){
                this.transactionData=transactionData;
            }
        }

    }

</script>

<style scoped>

</style>
