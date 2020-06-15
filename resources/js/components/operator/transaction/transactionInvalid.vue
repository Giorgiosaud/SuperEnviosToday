<template>
    <div>
        <div class="modal-card" >
            <header class="modal-card-head">
            <h1 class="modal-card-title">Transacción numero {{datosDeTransaccion.transaction_number }} ya existe</h1>
            </header>
            <section class="modal-card-body">
                Ya existe una transacción con este número el cliente que lo solicitó fue
                <strong>{{datosDeTransaccion.client.name}} {{datosDeTransaccion.client.last_name}}</strong>
                por un monto de
                <strong>
                    {{datosDeTransaccion.amount}} {{datosDeTransaccion.destination_account.bank.currency.name}}
                </strong> en el banco <strong>{{datosDeTransaccion.destination_account.bank.name}}</strong> el día
                <strong>{{datosDeTransaccion.created_at|date}}</strong> a las <strong>{{datosDeTransaccion.created_at|time}}</strong>
                y su status es <strong>{{datosDeTransaccion.status}}</strong>
                <div class="title-2">Adjuntos:</div>
                <div v-for="attachment in datosDeTransaccion.attachments">
                    <img :src="attachment.path" :alt="attachment.name">
                </div>
            </section>
            <footer class="modal-card-foot">
                <button type="button" class="button" @click="$parent.close()" @keyup.esc="$parent.close()">Close</button>
                <button class="button is-primary" @click="continuar" @keyup.right="continuar">Aceptar y Continuar</button>
            </footer>
        </div>
    </div>
</template>

<script>
    import {format} from 'date-fns'
    import esLocale  from 'date-fns/locale/es'

    export default {
        name:'transactionInvalid',
        filters:{
            date(value){
                return format(
                    new Date(value),
                    "dd 'de' MMMM 'de' yyyy",
                    {locale: esLocale}
                );
            },
            time(value){
                return format(
                    new Date(value),
                    "HH:mm:ss",
                    {locale: esLocale}
                );
            }
        },
        props:{
            datosDeTransaccion:{
                type: Object|null,
                default:null
            }
        },
        methods:{
            continuar(){
                this.$emit('continuar');
            }
        }
    }
</script>
