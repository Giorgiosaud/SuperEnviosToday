<template>
    <validation-observer tag="div" class="modal-card" v-slot="{invalid}" style="width: auto">
        <header class="modal-card-head">
            <h2 class="modal-card-title">Nueva Cuenta</h2>
        </header>
        <div class="modal-card-body">
            <validation-provider
                rules="required"
                v-slot="{ classes,errors,valid }"
                name="bank"
                tag="div"
                class="control">
                <label class="label">Banco</label>
                <div class="control has-icons-left has-icons-right">
                    <div class="select"
                         :class="classes">
                        <select
                            id="bank"
                            name="bank"
                            v-model="selectedBank">
                            <option value="">Seleccione el Banco</option>
                            <option
                                v-for="bank in venezuelanBanks"
                                :key="bank.id"
                                :value="bank"
                            >
                                {{bank.name}}
                            </option>
                        </select>
                        <span class="icon is-small has-text-success is-right" v-if="valid">
                            <font-awesome-icon icon="check"></font-awesome-icon>
                        </span>
                    </div>
                    <span class="icon is-small is-left">
                        <font-awesome-icon icon="passport"></font-awesome-icon>
                    </span>
                </div>
                <strong
                    v-if="errors[0]"
                    class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
            <validation-provider
                rules="required"
                v-slot="{ classes,errors,valid }"
                name="type"
                tag="div"
                class="control">
                <label class="label">Tipo de Cuenta</label>
                <div class="control has-icons-left has-icons-right">
                    <div class="select"
                         :class="classes">
                        <select
                            id="type"
                            name="type"
                            v-model="selectedType">
                            <option value="">Seleccione el Tipo de cuenta</option>
                            <option
                                v-for="type in types"
                                :key="type.id"
                                :value="type.value"
                            >
                                {{type.name}}
                            </option>
                        </select>
                        <span class="icon is-small has-text-success is-right" v-if="valid">
                            <font-awesome-icon icon="check"></font-awesome-icon>
                        </span>
                    </div>
                    <span class="icon is-small is-left">
                        <font-awesome-icon icon="piggy-bank"></font-awesome-icon>
                    </span>
                </div>
                <strong
                    v-if="errors[0]"
                    class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
            <validation-provider
                rules="required|length:20"
                name="Numero de cuenta"
                v-slot="{ classes,errors,valid}"
                tag="div"
                class="control">
                <label class="label" for="account-number">Numero de cuenta</label>
                <div class="control has-icons-right">
                    <the-mask id="account-number"
                              v-model="accountNumber"
                              name="accountNumber"
                              class="input"
                              :class="classes"
                              type="text"
                              mask="####-####-####-####-####"
                              placeholder="Número de Cuenta"
                              autofocus></the-mask>
                    <span class="icon is-small has-text-warning	is-right"
                          v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                    <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
                </div>
                <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
        </div>
        <footer class="modal-card-foot">
            <b-button :loading="savingData" class="button is-primary" :disabled="invalid" @click="saveAccount">Guardar</b-button>
            <button class="button" type="button" @click="cleanAndClose">Cancelar</button>
        </footer>
    </validation-observer>
</template>
<script>
    export default {
        name: 'newAccount',
        props: {
            receiver: {
                type: Object | null,
                default: () => null
            }
        },
        data: () => ({
            venezuelanBanks: [],
            types: [{name: 'Corriente', value: 'corriente'}, {name: 'Ahorro', value: 'ahorro'}],
            selectedBank: '',
            selectedType: '',
            savingData:false,
            accountNumber: ''
        }),
        async created() {
            const response = await axios.get('/api/banks/base')
            this.venezuelanBanks = response.data.sort(bank => bank.name)
        },
        methods: {
            async saveAccount() {
                this.savingData = true
                try {
                    const response = await axios.post(`/api/accounts/${this.receiver.id}`, {
                        bank_id: this.selectedBank.id,
                        type: this.selectedType,
                        number: this.accountNumber,
                    })
                    this.$emit('account-added');
                    this.cleanAndClose()
                } catch (error) {
                    this.$buefy.notification.open({
                        message: error.message,
                        type: 'is-danger'
                    })
                } finally {
                    this.savingData = false
                }
            },
            cleanData() {
                this.selectedBank = '';
                this.selectedType = '';
                this.accountNumber = '';
            },
            cleanAndClose() {
                this.cleanData()
                this.$parent.close()
            }
        },
        watch: {}


    }
</script>
