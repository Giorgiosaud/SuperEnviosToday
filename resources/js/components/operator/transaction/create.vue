<script>
    export default {
        // TODO continuar revisando flujo de creacion de transaccion
        name: "transactionCreate",
        data:()=>({
            clientReady:false,
            searchButtonText:'Buscar',
            client:{
                idn:'',
                idn_type:'',
            },
            obtainedClient:{},
            searchingClient:false,
            showCreateClientForm:false,
            showClientData:false,
        }),
        methods:{
            async searchClient(){
                this.searchingClient=true;
                const response = await axios.get(`/api/user/${this.client.idn_type}/${this.client.idn}`)
                if (response.status === 204) {
                    this.showCreateClientForm=true
                }else{
                    this.$set(this,'obtainedClient',response.data)
                    this.showClientData=true;
                }

                this.searchingClient=false;
            }
        }
    }

</script>

<style scoped>

</style>
