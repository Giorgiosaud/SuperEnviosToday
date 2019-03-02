<template>
    <div>
        <h1>Lista de Monedas</h1>
        <div class="table-responsive">
            <table class="table" v-if="currencies.length">
                <tr>
                    <th>Nombre</th>
                    <th>Identificador</th>
                    <th>Signo</th>
                </tr>
                <tr v-for="currency in currencies">
                    <td>{{currency.name}}</td>
                    <td>{{currency.identificator}}</td>
                    <td>{{currency.sign}}</td>
                </tr>
            </table>
            <h3 v-else>No Monedas Registradas</h3>
        </div>
        <h2>Agregar Moneda</h2>
        <form>
            <div class="col-12">
                <label for="name" class="label-base bg-white">Ingrese nombre de banco:</label>
                <input type="text" id="name" v-model="name" class="input-base">
            </div>
            <div class="col-12">
                <label for="identificator" class="label-base bg-white">Ingrese nombre de banco:</label>
                <input id="identificator" type="text" v-model="identificator" class="input-base">
            </div>
            <div class="col-12">
                <label for="sign" class="label-base bg-white">Ingrese nombre de banco:</label>
                <input id="sign" type="text" v-model="sign" class="input-base">
            </div>
            <div class="col-12">
                <button @click.prevent="createCurrency" class="btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        name: 'Currencies',
        data() {
            return {
                name: '',
                identificator: '',
                sign: '',
            }
        },
        computed: {
            currencies() {
                return this.$store.state.settings.currencies;
            },
        },
        methods: {
            createCurrency() {
                this.$store.dispatch('settings/CREATE_NEW_CURRENCY', {
                    name: this.name,
                    identificator: this.identificator,
                    sign: this.sign,
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
