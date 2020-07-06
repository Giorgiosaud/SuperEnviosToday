<template>
    <div>
        <div class="modal-card" style="width: auto">
            <header class="modal-card-head">
                <p class="modal-card-title">Agregue un Banco</p>
            </header>
            <section class="modal-card-body">
                <b-field label="Nombre">
                    <b-input
                        type="name"
                        v-model="name"
                        placeholder="Nombre de Banco"
                        required>
                    </b-input>
                </b-field>

                <b-select placeholder="Seleccione una moneda" v-model="currency">
                    <option
                        v-for="option in currencies"
                        :value="option.id"
                        :key="option.id">
                        {{ option.name }}
                    </option>
                </b-select>

            </section>
            <footer class="modal-card-foot">
                <button class="button" type="button" @click="$parent.close()" @keydown.esc="$parent.close()">Cerrar</button>
                <button class="button is-primary" @click="newBank" @keypress.enter="newBank">Guardar</button>
            </footer>
        </div>
    </div>
</template>

<script>
export default {
    name: "addBank",
    props: {
        currencies: {
            type: Array,
            default: () => ([])
        }
    },
    data: () => ({
        name: '',
        currency: '',
        savingBank: false,
    }),
    methods: {
        async newBank() {
            this.savingBank = true;

            try {
                await $http.post('api/banks', {
                    name: this.name,
                    currency: this.currency
                })
            } finally {
                this.savingBank = false;
                this.$parent.close();
                this.$emit('bank-saved')
            }
        }
    }
}
</script>

<style scoped>

</style>
