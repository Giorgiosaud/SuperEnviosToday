<script>
    import clientData from './clientData'
    import transactionData from './transactionData'
    export default {
        name: "transactionCreate",
        components:{
            clientData,
            transactionData
        },
        data:()=>({
            clientReady:true,
            client:{},
            actualStep:1
        }),
        methods:{
            clientDataSet(client){
                this.client=client
                this.actualStep=1
            },
            transactionDataSet(){

            }
        }

    }

</script>

<style scoped>

</style>
