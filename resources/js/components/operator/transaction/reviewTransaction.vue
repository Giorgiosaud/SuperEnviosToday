<template>
    <div>
        <div class="card">
            <header class="card-header">
                <h1 class="card-header-title">
                    Resumen de Datos
                </h1>
            </header>
            <div class="card-content" >
                <div class="space client-data" v-if="clientData">
                    <h2 class="title">Datos del cliente</h2>
                    <div class="level">
                        <div class="level-left">Id de sistema:</div>
                        <div class="level-right">{{clientData.id}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Número de Identificación:</div>
                        <div class="level-right">{{clientData.idn_type}}-{{clientData.idn}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Nombres:</div>
                        <div class="level-right">{{clientData.name}} {{clientData.last_name}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Telefono:</div>
                        <div class="level-right">{{clientData.phone}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Email:</div>
                        <div class="level-right">{{clientData.email}}</div>
                    </div>
                </div>
                <div class="space receiver-data" v-if="receiverData">
                    <h2 class="title">Datos del receptor</h2>
                    <div class="level">
                        <div class="level-left">Número de Identificción:</div>
                        <div class="level-right">{{receiverData.receiver.idn_type}}-{{receiverData.receiver.idn}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Nombre:</div>
                        <div class="level-right">{{receiverData.receiver.name}} {{receiverData.receiver.last_name}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Teléfono:</div>
                        <div class="level-right">{{receiverData.receiver.phone}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Email:</div>
                        <div class="level-right">{{receiverData.receiver.email}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Banco a transferir:</div>
                        <div class="level-right">{{receiverData.receiverAccount.bank.name}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Cuenta a transferir:</div>
                        <div class="level-right">{{receiverData.receiverAccount.number|formatAccount}}</div>
                    </div>
                </div>
                <div class="space transaction-data" v-if="transactionData">
                    <h2 class="title">Datos de la transacción</h2>
                    <div class="level">
                        <div class="level-left">Moneda:</div>
                        <div class="level-right">{{transactionData.currency.name}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Recibido en:</div>
                        <div class="level-right">{{transactionData.foreignAccount.bank.name}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Numero de cuenta en:</div>
                        <div class="level-right">{{transactionData.foreignAccount.number|formatAccount}} / {{transactionData.foreignAccount.type}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Numero de transaccion:</div>
                        <div class="level-right">{{transactionData.voucher}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Recibido por:</div>
                        <div class="level-right">{{operator.name}} {{operator.last_name}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Monto:</div>
                        <div class="level-right">{{transactionData.amount|currency(transactionData.currency)}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Tasa de cambio
                            <span v-if="transactionData.tryAnotherRate">
                                &nbsp;propuesta:
                            </span>
                            <span v-else>
                               &nbsp;pactada:
                            </span>
                        </div>
                        <div class="level-right">{{transactionData.exchangeRate|rateCurrency(transactionData.currency)}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Monto a recibir <span v-if="transactionData.tryAnotherRate">si se aprueba la transacción</span>en Bs:</div>
                        <div class="level-right">{{transactionData.bsAmount|currency({sign:'Bs '})}}</div>
                    </div>
                    <h2 class="title">Adjuntos:</h2>
                    <figure class="image is-fullwidth" v-for="image in transactionData.uploadedFiles">
                        <img :src="image.path">
                    </figure>
                </div>
                <div class="space venezuelan-operator-data" v-if="venezuelanOperatorData">
                    <h2 class="title">Datos de Transacción en Venezuela</h2>
                    <div class="level">
                        <div class="level-left">Nombre y Apellido:</div>
                        <div class="level-right">{{venezuelanOperatorData.operator.name}} {{venezuelanOperatorData.operator.last_name}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">email:</div>
                        <div class="level-right">{{venezuelanOperatorData.operator.email}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">telefono:</div>
                        <div class="level-right">{{venezuelanOperatorData.operator.phone}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Banco desde el que se transfiere:</div>
                        <div class="level-right">{{venezuelanOperatorData.selectedAccount.bank.name}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Cuenta desde la que se transfiere:</div>
                        <div class="level-right">{{venezuelanOperatorData.selectedAccount.number|formatAccount}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Balance de la cuenta antes de la transaccion:</div>
                        <div class="level-right">{{venezuelanOperatorData.selectedAccount.balance|currency({sign:'Bs'})}}</div>
                    </div>
                    <div class="level">
                        <div class="level-left">Balance de la cuenta despues de la transaccion:</div>
                        <div class="level-right">{{venezuelanOperatorData.selectedAccount.balance-transactionData.bsAmount|currency({sign:'Bs'})}}</div>
                    </div>
                </div>
            </div>
            <footer class="card-footer" v-if="transactionData">

                <b-button
                          :loading="executingTransaction"
                          tag="a"
                          class="card-footer-item"
                          @click="enviar">
                    <span v-if="transactionData.tryAnotherRate">Solicitar Aprobación</span>
                    <span v-else>Ejecutar Transacción</span>
                </b-button>
                <b-button class="card-footer-item" @click="cancelar">Cancelar</b-button>
            </footer>
        </div>


    </div>
</template>
<script>
    import currencyFilter from '../../../currency';
    const bsFormat=    {symbol: 'Bs ', precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,}
    import { SnackbarProgrammatic as Snackbar } from 'buefy'

    export default {
        name: "reviewTransaction",
        filters:{
            currency(value,selectedCurrency){
                const formatOptions={
                    precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
                }
                if(!selectedCurrency){
                    formatOptions.symbol='$ ';
                }else {
                    formatOptions.symbol=`${selectedCurrency.sign} `;
                }
                return currencyFilter(value,formatOptions)
            },
            rateCurrency:(value,selectedCurrency)=>{
                const formatOptions={
                    precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
                }
                if(!selectedCurrency){
                    formatOptions.symbol='Bs/$ ';
                }else {
                    formatOptions.symbol=`Bs/${selectedCurrency.sign} `;
                }
                return currencyFilter(value,formatOptions)
            },
            formatAccount(value){
                return value.replace(/(\d)(?=(\d{4})+(?!\d))/gi,'$1-')
            }
        },
        props: {
            clientData:{
                type:Object|null,
                default:null
            },
            operator:{
                type:Object|null,
                default:null
            },
            receiverData:{
                type:Object|null,
                default:null
                },
            transactionData:{
                type:Object|null,
                default:null
            },
            venezuelanOperatorData: {
                type:Object|null,
                default:null
            },
        },
        components: {
        },
        data: () => ({
            executingTransaction:false
        }),
        async created(){
        },
        methods: {
            cancelar(){
                window.location.reload()
            },
            async enviar(){
                this.executingTransaction=true;
                const transaction={
                    'client_id'                             :this.clientData.id,
                    'operator_id'                            :this.operator.id,
                    'operator_account_id'                    :this.transactionData.foreignAccount.id,
                    'received_transaction_attachment_ids' :this.transactionData.uploadedFiles.map(files=>files.id),
                    'receiver_account_id'                   :this.receiverData.receiverAccount.id,
                    'venezuelan_operator_account_id'        :this.venezuelanOperatorData.selectedAccount.id,
                    'venezuelan_operator_id'                :this.venezuelanOperatorData.operator.id,
                    'transaction_number'                    :this.transactionData.voucher,
                    'receiver_id'                      :this.receiverData.receiver.id,
                    'rate'                                  :this.transactionData.exchangeRate,
                    'amount'                                :this.transactionData.amount
                };
                try {
                    const response=await $http.post('/api/transaction/execute', transaction)
                    if(response.status==200) {
                        this.$buefy.dialog.alert({
                            title:'Solicitud completada',
                            message:'Ahora debemos esperar que el coordinador apruebe',
                            onConfirm:() => window.location.reload()
                        });
                    }else if(response.status==201){
                        this.$buefy.dialog.alert({
                            title:'Transacción completada',
                            message: 'Transaccion ejecutada',
                            onConfirm:() => window.location.reload()
                        });

                    }
                }catch(error){
                    if(error.response.status===424){
                        this.$buefy.snackbar.open({
                            message:error.response.data.message,
                            type: 'is-warning',
                            indefinite:true,
                            onAction(){
                                window.location.reload()
                            }
                        })

                    }
                }finally{
                    this.executingTransaction=false
                }
            }
        },
        computed: {
        },
        watch: {
        }
    }
</script>

<style scoped>

</style>
