<script>
    import currency from '../../../currency'
    import {Money} from 'v-money'
    import UppyUploader from '../../../UppyUploader';
    import {v4 as uuidv4} from 'uuid';
    import transactionInvalid from './transactionInvalid'

    export default {
        name: "transactionData",
        filters: {
            currency,
            rateCurrency:(value,selectedCurrency)=>{
                const formatOptions={
                    precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
                }
                if(!selectedCurrency){
                    formatOptions.symbol='Bs/$ ';
                }else {
                    formatOptions.symbol=`Bs/${selectedCurrency.sign} `;
                }
                return currency(value,formatOptions)
            }
        },
        components: {
            Money,
            UppyUploader,
            transactionInvalid
        },
        data: () => ({
            verifyingTransaction:false,
            datosDeTransaccionRepetida:null,
            canGoOn:'',
            isComponentModalActive:false,
            transaction: '',
            selectedCurrency: null,
            selectedAccount: null,
            foreignCurrencies: [],
            accountsOfCurrency: [],
            exchangeRate: 0,
            tryAnotherRate: false,
            savingTransaction: false,
            newRate: '',
            amount: '',
            voucher: '',
            uploadedFiles: [],
            tempUploadFiles: [],
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
        async created() {
            const response = await $http.get('/api/currency/foreign');
            const foreignCurrencies= await response.json()
            this.foreignCurrencies = foreignCurrencies;

        },
        computed: {
            calcExchange() {
                if (this.tryAnotherRate) {
                    return this.newRate * this.amount
                }
                return this.exchangeRate * this.amount
            }
        },
        methods: {
            continueWithThat(){
                this.canGoOn = 'ok';
                this.$refs.fields.validate()
                this.isComponentModalActive=false;
            },
            nextStep() {

                this.$emit('transaction-set', {
                    currency: this.selectedCurrency,
                    foreignAccount: this.selectedAccount,
                    amount: this.amount,
                    bsAmount:this.calcExchange,
                    voucher: this.voucher,
                    exchangeRate:this.tryAnotherRate?this.newRate:this.exchangeRate,
                    tryAnotherRate:this.tryAnotherRate,
                    uploadedFiles: this.uploadedFiles,
                })
            },
            async verifyTransactionNumberAsUnique(){
                try {
                    const response = await $http.get(`/api/transaction/verify/${this.voucher}`)

                    if (response.status === 204) {
                        this.canGoOn = 'ok'
                    } else {
                        this.canGoOn = ''
                        this.isComponentModalActive=true;
                        this.datosDeTransaccionRepetida=response.data;
                    }
                }catch(error){
                    this.$buefy.notification('asd')
                }
            },
        },
        watch: {
            selectedAccount(value) {
                if (value && value.bank && value.bank.name === 'Efectivo') {
                    this.voucher = uuidv4()
                    this.canGoOn='ok'
                } else {
                    this.canGoOn=''
                    this.voucher = ''
                }
            },
            async selectedCurrency(value, old) {
                if (value !== old) {
                    const response = await $http.get(`/api/rate/${value.id}`);
                    const  exchangeRate=await response.json()
                    const response2=await $http.get(`/api/accounts/${value.id}`)
                    const accountsOfCurrency = await response2.json()
                    this.accountsOfCurrency = accountsOfCurrency;
                    this.exchangeRate = exchangeRate.amount;
                }
            }
        }


    }

</script>

<style lang="scss" scoped>
    .is-success ::v-deep.uppy-Root{
        border:dashed 1px green;
        border-radius: 5px;
    }
    .is-danger ::v-deep.uppy-Root{
        border:dashed 1px red;
        border-radius: 5px;
    }
</style>
