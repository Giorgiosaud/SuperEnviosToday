<template>
    <div>
        <validation-observer v-slot="{invalid}" ref="fields" slim>
            <div
                class="modal-card"
                style="width: auto">
                <header class="modal-card-head">
                    <p class="modal-card-title">
                        Agregue una Cuenta
                    </p>
                </header>
                <section class="modal-card-body">
                    <validation-provider
                        rules="required"
                        v-slot="{ classes,errors,valid }"
                        name="Currency"
                        tag="div"
                        class="control">
                        <b-select
                            v-model="currency"
                            class="has-padding-bottom-10"
                            placeholder="Seleccione una moneda"
                            expanded>
                            <option
                                v-for="option in currencies"
                                :key="option.id"
                                :value="option.id">
                                {{ option.name }}
                            </option>
                        </b-select>
                    </validation-provider>
                    <validation-provider
                        rules="required"
                        v-slot="{ classes,errors,valid }"
                        name="Bank"
                        tag="div"
                        class="control">
                        <b-select
                            class="has-padding-bottom-10 w-100"
                            v-model="bank"
                            :disabled="!currency"
                            placeholder="Seleccione el Banco"
                            expanded>
                            <option
                                v-for="option in selectedBank"
                                :key="option.id"
                                :value="option.id">
                                {{ option.name }}
                            </option>
                        </b-select>
                    </validation-provider>

                        <b-select
                            class="has-padding-bottom-10 w-100"
                            v-model="type"
                            placeholder="Seleccione el Tipo de cuenta"
                            :disabled="!bank"
                            expanded>
                            <option
                                v-for="option in accountType"
                                :key="option.name"
                                :value="option.value">
                                {{ option.name }}
                            </option>
                        </b-select>
                    <validation-provider
                        rules="required|numeric|max:20|min:5"
                        v-slot="{ classes,errors,valid }"
                        name="Account number"
                        tag="div"
                        class="control">
                        <b-field label="Numero">
                            <b-input
                                v-model="number"
                                maxlength="20"
                                type="text"
                                placeholder="Numero de cuenta"
                                required/>
                        </b-field>

                    </validation-provider>
                </section>
                <footer class="modal-card-foot">
                    <button
                        class="button"
                        type="button"
                        @click="$parent.close()"
                        @keypress.esc="$parent.close()">
                        Cerrar
                    </button>
                    <button
                        class="button is-primary"
                        @click="newAccount"
                        :disabled="invalid"
                        @keypress.enter="newBank">
                        Guardar
                    </button>
                </footer>
            </div>
        </validation-observer>
    </div>
</template>

<script>
export default {
    name: 'AddAccount',
    props: {
        currencies: {
            type: Array,
            default: () => ([]),
        },
        banks: {
            type: Array,
            default: () => ([]),
        },
    },
    data: () => ({
        number: '',
        currency: null,
        bank: null,
        savingAccount: false,
        accountType:[{name:'No Aplica',value:null},{name:'corriente',value:'corriente'},{name:'ahorro',value:'ahorro'}],
        type:null,
    }),
    computed: {
        selectedBank() {
            return this.currency ? this.banks.filter(bank => bank.currency_id === this.currency) : [];
        }
    },
    watch: {
        currency() {
            this.bank = null
        }
    },
    methods: {
        async newAccount() {
            this.savingAccount = true;

            try {
                await $http.post('api/accounts', {
                    bank: this.bank,
                    currency: this.currency,
                    number: this.number,
                    type:this.type
                });
            } finally {
                this.savingAccount = false;
                this.$parent.close();
                this.$emit('bank-saved');
            }
        },
    },
};
</script>

<style scoped>

</style>
