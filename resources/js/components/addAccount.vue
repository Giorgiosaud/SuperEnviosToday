<template>
    <div>
        <label for="currencyId">Seleccione el tipo de moneda</label>
        <v-select
            id="currencyId"
            v-model="currency_id"
            :options="currencies"
            index="id"
            label="name"
        />
        <label for="bankId">Seleccione el Banco</label>
        <v-select
            id="bankId"
            v-model="account_bank_id"
            :options="banksRelatedToCurrencies"
            index="id"
            label="name"
        />
        <label for="account_type">Seleccione el Tipo de Cuenta</label>
        <v-select
            id="account_type"
            v-model="account_type"
            :options="account_types"
        />
        <!--TODO agregar tipo de cuenta para cuentas de venezuela (RECEPTOR)-->
        <label for="number">Número de Cuenta</label>
        <input
            id="number"
            v-model="number"
            type="text"
            class="input-base"
        <!--TODO Validar 20 digitos en cuenta venezolana-->
        >

        <button
            type="button"
            class="btn btn-primary mt-2"
            @click.prevent="addAccount"
        >
            Registrar Cuenta
        </button>
    </div>
</template>

<script>
    export default {
        name: 'AddAccount',
        props: {
            client: {
                type: Object,
                default() {
                    return {};
                },
            },
        },
        data() {
            return {
                account_bank_id: '',
                currency_id: '',
                currencies: [],
                banks: [],
                number: '',
                account_type: '',
                account_types: [
                    {
                        label: 'Corriente',
                        index: 'corriente'
                    },
                    {
                        label: 'Ahorro',
                        index: 'ahorro'
                    }
                ]
            };
        },
        computed: {
            banksRelatedToCurrencies() {
                return this.banks.filter(bank => bank.currency_id === this.currency_id);
            },
        },
        created() {
            axios.get('api/banks').then((response) => {
                this.banks = response.data;
            });
            axios.get('api/currencies').then((response) => {
                this.currencies = response.data;
            });
        },
        methods: {
            addAccount() {
                axios.post('api/accounts', {
                    user_id: this.client.id,
                    bank_id: this.account_bank_id,
                    number: this.number,
                    is_operator_account: false,
                })
                    .then(() => {
                        this.$emit('registered');
                    });
            },
        },


    };
</script>

<style scoped>

</style>
