<script>
    export default {
        // TODO continuar revisando flujo de creacion de transaccion
        name: "clientData",
        data:()=>({
            searchButtonText:'Buscar',
            client:{
                idn:'',
                idn_type:'',
                name:'',
                last_name:'',
                email:'',
                phone:''
            },
            possibleClient:{
                id:'',
                idn:'',
                idn_type:'',
                name:'',
                last_name:'',
                email:'',
                phone:''
            },
            clientReady:false,
            searchingClient:false,
            searchExecuted:false,
            savingClient:false,
            isComponentModalActive:false
        }),
        methods:{
            nextStep(){
                this.$emit('next-step')
            },
            setClient(data) {
                const {id, idn, idn_type, last_name, phone, email, name} = data;

                this.client.id = id
                this.client.idn = idn
                this.client.idn_type = idn_type
                this.client.name = name
                this.client.last_name = last_name
                this.client.email = email
                this.client.phone = phone
                this.clientReady = true;
                this.$emit('client-data-set', this.client)
            },
            setPossibleClient(data) {
                const {id, idn, idn_type, last_name, phone, email, name} = data;
                this.possibleClient.id = id
                this.possibleClient.idn = idn
                this.possibleClient.idn_type = idn_type
                this.possibleClient.name = name
                this.possibleClient.last_name = last_name
                this.possibleClient.email = email
                this.possibleClient.phone = phone
                this.clientReady = true;
            },
            async searchClient(){
                if(this.client.idn==='' || this.client.idn_type==='') {
                    return;
                }
                this.searchingClient=true;
                const response = await axios.get(`/api/user/${this.client.idn_type}/${this.client.idn}`)
                if (response.status === 204) {
                    this.client.id = ''
                    this.client.name = ''
                    this.client.last_name = ''
                    this.client.email = ''
                    this.client.phone = ''
                    this.clientReady = false;
                }else {
                    if(response.data.status==='OK') {
                        this.setClient(response.data.user);
                    }else{
                        this.setPossibleClient(response.data.user)
                        this.isComponentModalActive=true
                    }
                }
                this.searchExecuted = true;
                this.searchingClient = false;
            },
            usePossibleClient(){
                this.setClient(this.possibleClient)
                this.isComponentModalActive=false;
            },
            createNew(){
                this.clientReady = false;
                this.isComponentModalActive=false;
            },
            async saveClient(){
                this.savingClient=true;
                const response=await axios.post(`/api/user/`,{
                    ...this.client
                })
                this.setClient(response.data.user);
                this.savingClient=false;
            }
        }
    }
</script>

<style scoped>

</style>
