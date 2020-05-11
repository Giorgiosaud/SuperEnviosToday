<script>
    import currency from '../../../currency'
    import {Money} from 'v-money'
    import UppyUploader from '../../../UppyUploader';
    export default {
        name: "transactionData",
        filters:{
            currency
        },
        components:{
            Money,
            UppyUploader
        },
        data:()=>({
            transaction:'',
            selectedCurrency:null,
            selectedAccount:null,
            foreignCurrencies:[],
            accountsOfCurrency:[],
            exchangeRate:0,
            tryAnotherRate:false,
            newRate:'',
            amount:'',
            voucher:'',
            uploadFiles:[],
            bs: {
                decimal: ',',
                thousands: '.',
                prefix: 'Bs ',
                suffix: ' ',
                precision: 0,
                masked: false
            },
            clp: {
                decimal: ',',
                thousands: '.',
                prefix: '$ ',
                suffix: ' ',
                precision: 0,
                masked: false
            }
        }),
        async created(){
            const {data:foreignCurrencies} = await axios.get('/api/currency/foreign');

            this.foreignCurrencies = foreignCurrencies;
        },
        computed:{
            calcExchange(){
                if(this.tryAnotherRate){
                    return this.newRate*this.amount
                }
                return this.exchangeRate*this.amount
            }
        },
        watch:{
            async selectedCurrency(value,old){
                if(value!==old){
                    const {data:exchangeRate} = await axios.get(`/api/rate/${value.id}`);
                    const {data:accountsOfCurrency}=await axios.get(`/api/accounts/${value.id}`)
                    this.accountsOfCurrency=accountsOfCurrency;
                    this.exchangeRate=exchangeRate.amount;
                }
            }
        }


    }

</script>

<style scoped>

</style>
