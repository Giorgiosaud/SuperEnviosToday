<template>
    <div>
        <h1>Lista de Bancos</h1>
        <div class="table-responsive">
            <table class="table" v-if="banks.length">
                <tr>
                    <th>Nombre</th>
                    <th>Moneda</th>
                </tr>
                <tr v-for="bank in banks">
                    <td>{{bank.name}}</td>
                    <td>{{bank.currency.name}}</td>
                </tr>
            </table>
            <h3 v-else>No posee Bancos Registrados</h3>
        </div>
        <h2>Agregar Bancos</h2>
        <form>
            <div class="col-12">
                <label for="bank-name" class="label-base bg-white">Ingrese nombre de banco:</label>
                <input type="text" id="bank-name" v-model="bankName" class="input-base">
            </div>
            <div class="col-12">
                <label for="currency" class="label-base bg-white">Seleccione Moneda:</label>
                <v-select id="currency" :searchable="false" :options="currencies" label="name"
                          class="input-base"
                          v-model="selectedCurrency"></v-select>

            </div>
            <div class="col-12">
                <button @click.prevent="createBank" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        name: 'Banks',
        data() {
            return {
                bankName: '',
                selectedCurrency: '',
            }
        },
        computed: {
            banks() {
                return this.$store.state.settings.banks;
            },
            currencies() {
                return this.$store.state.settings.currencies;
            },
        },
        methods: {
            createBank() {
                this.$store.dispatch('settings/CREATE_NEW_BANK', {
                    name: this.bankName,
                    currency_id: this.selectedCurrency.id
                }).then(() => {
                        this.bankName = '';
                        this.selectedCurrency = '';
                    }
                );
            }
        }
    };
</script>

<style scoped>

</style>
