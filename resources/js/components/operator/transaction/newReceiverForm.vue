<template>
    <validation-observer tag="div" class="modal-card" style="width: auto">
        <header class="modal-card-head">
            <h2 class="modal-card-title">Nuevo Receptor</h2>
        </header>
        <div class="modal-card-body">
            <validation-provider
                rules="required"
                v-slot="{ classes,errors,valid }"
                name="idn_type"
                tag="div"
                class="control">
                <label class="label">Tipo de Documento</label>
                <div class="control has-icons-left has-icons-right">
                    <div class="select"
                         :class="classes">
                        <select
                            id="idn_type"
                            name="idn_type"
                            v-model="idn_type">
                            <option value="">Seleccione el tipo de documento</option>
                            <option value="CI">Cédula Venezolana</option>
                            <option value="PASSPORT">Pasaporte</option>
                            <option value="RUT">RUT</option>
                            <option value="DNI">DNI</option>
                            <option value="RIF">RIF</option>
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
                name="ID"
                v-slot="{ classes,errors,valid}"
                tag="div"
                class="control">
                <label class="label" for="idn">ID</label>
                <div class="control has-icons-right">
                    <input id="idn"
                           v-model="idn"
                           name="idn"
                           class="input"
                           :class="classes"
                           type="text"
                           placeholder="ID"
                           value="123"
                           autocomplete="idn" autofocus>
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
            <validation-provider
                rules="required"
                v-slot="{ classes,errors, valid }"
                tag="div"
                class="field">

                <label class="label" for="name">Nombre</label>
                <div class="control has-icons-right">
                    <input id="name"
                           v-model="name"
                           name="name"
                           class="input"
                           :class="classes"
                           type="text"
                           placeholder="Nombre"
                           autocomplete="name" autofocus>
                    <span class="icon is-small has-text-warning	is-right"
                          v-if="errors[0]">
                                    <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                                </span>
                    <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                </div>
                <strong
                    v-if="errors[0]"
                    class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
            <validation-provider
                rules="required"
                v-slot="{ classes,errors, valid }"
                tag="div"
                class="field">

                <label class="label" for="last_name">Apellido</label>
                <div class="control has-icons-right">
                    <input id="last_name"
                           v-model="last_name"
                           name="name"
                           class="input"
                           :class="classes"
                           type="text"
                           placeholder="Apellido"
                           autocomplete="name" autofocus>
                    <span class="icon is-small has-text-warning	is-right"
                          v-if="errors[0]">
                                    <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                                </span>
                    <span class="icon is-small has-text-success is-right" v-if="valid">
                                    <font-awesome-icon icon="check"></font-awesome-icon>
                                </span>
                </div>
                <strong
                    v-if="errors[0]"
                    class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
            <validation-provider tag="div" class="column is-narrow" vid="sameEmail" v-slot="x">
                <label class="checkbox">
                    <input type="checkbox" v-model="sameEmail">
                    Usar Mismo Correo que Cliente
                </label>
            </validation-provider>
            <validation-provider
                name="email"
                v-if="!sameEmail"
                rules="required|email"
                v-slot="{ classes,errors,valid }"
                tag="div"
                class="field">

                <label class="label" for="email">Email</label>
                <div class="control has-icons-left has-icons-right">
                    <input id="email" name="email"
                           :class="classes"
                           class="input"
                           type="text"
                           v-model="email"
                           placeholder="email"
                           autocomplete="email" autofocus>
                    <span class="icon is-small is-left">
                        <font-awesome-icon icon="envelope"></font-awesome-icon>
                    </span>
                    <span class="icon is-small has-text-warning	is-right" v-if="errors[0]">
                        <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                    </span>
                    <span class="icon is-small has-text-success is-right" v-if="valid">
                        <font-awesome-icon icon="check"></font-awesome-icon>
                    </span>
                </div>
                <strong v-if="errors[0]" class="help is-danger">@{{errors[0]}}</strong>
            </validation-provider>
        </div>
        <footer class="modal-card-foot">
            <b-button :loading="savingData" class="button is-primary" @click="saveReceiver">Guardar</b-button>
            <button class="button" type="button" @click="cleanAndClose">Cancelar</button>
        </footer>
    </validation-observer>
</template>
<script>
    export default {
        name: 'newReceiverForm',
        props: {
            client: {
                type: Object | null,
                default: () => null
            }
        },
        data: () => ({
            idn: '',
            idn_type: '',
            name: '',
            last_name: '',
            sameEmail: true,
            email: '',
            savingData: false
        }),
        created() {
            this.email = this.client.email;
        },
        methods: {
            cleanData() {
                this.idn = '';
                this.idn_type = '';
                this.name = '';
                this.last_name = '';
                this.sameEmail = true;
                this.email = this.client.email;
            },
            async saveReceiver() {
                this.savingData = true
                try {
                    const response = await $http.post(`/api/user/${this.client.id}/receiver`, {
                        idn: this.idn,
                        idn_type: this.idn_type,
                        name: this.name,
                        last_name: this.last_name,
                        email: this.email,
                    })
                    this.$emit('receiver-added');
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
            cleanAndClose(){
                this.cleanData()
                this.$parent.close()
            },
        },
        watch: {
            sameEmail(value) {
                if (value) {
                    this.email = this.client.email;
                } else {
                    this.email = '';
                }
            }
        }


    }
</script>
