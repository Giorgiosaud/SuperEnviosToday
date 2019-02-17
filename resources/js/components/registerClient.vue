<template>
    <div class="container mx-auto">
        <div class="w-full max-w-md mx-auto">
            <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <label for="idn_type" class="label-base">Tipo de Identificación</label>
                <v-select name="idn_type" class="mb-3" :class="{'border-red':hasInErrors('idn')}" :searchable="false"
                          :clearable="false" :options='idnTypes' v-model="person.idn_type"
                          @blur="cleanError('idn_type')"></v-select>
                <span v-if="hasInErrors('idn_type')" class="error-base" role="alert">
          <strong>{{ getError('idn_type') }}</strong>
        </span>
                <label for="roles" class="label-base">Tipo de Identificación</label>
                <v-select label="name" index="name_id" multiple id="roles" name="roles" class="mb-3"
                          :class="{'border-red':hasInErrors('role')}" :searchable="false" :clearable="false"
                          :options='roles' v-model="selectedRole" @blur="cleanError('role')"></v-select>
                <span v-if="hasInErrors('role')" class="error-base" role="alert">
          <strong>{{ getError('role') }}</strong>
        </span>
                <label for="idn" class="label-base">Numero de Identificación</label>

                <input id="idn" type="text" class="input-base" :class="{'border-red':hasInErrors('idn')}" name="idn"
                       v-model="person.idn" required autofocus @blur="cleanError('idn')">
                <span v-if="hasInErrors('idn')" class="error-base" role="alert">
          <strong>{{ getError('idn') }}</strong>
        </span>
                <label for="name" class="label-base">Nombre</label>

                <input id="name" type="text" class="input-base" @blur="cleanError('name')"
                       :class="{'border-red':hasInErrors('name')}" name="name" v-model="person.name" required autofocus>

                <span v-if="hasInErrors('name')" class="error-base" role="alert">
          <strong>{{ getError('name') }}</strong>
        </span>
                <label for="last_name" class="label-base">Apellido(s)</label>

                <input id="last_name" type="text" @blur="cleanError('last_name')"
                       :class="{'border-red':hasInErrors('last_name')}" class="input-base" name="last_name"
                       v-model="person.last_name" required autofocus>

                <span v-if="hasInErrors('last_name')" class="error-base" role="alert">
          <strong>{{ getError('last_name') }}</strong>
        </span>

                <label for="phone" class="label-base">Telefono</label>

                <input id="phone" type="text" @blur="cleanError('phone')" :class="{'border-red':hasInErrors('phone')}"
                       class="input-base" name="phone" v-model="person.phone" required autofocus>

                <span v-if="hasInErrors('phone')" class="error-base" role="alert">
          <strong>{{ getError('phone') }}</strong>
        </span>

                <label class="label-base" for="email">Email</label>
                <input class="input-base" :class="{'border-red':hasInErrors('last_name')}" type="email" id="email"
                       name="email" v-model="person.email" required autofocus>
                <span v-if="hasInErrors('email')" class="error-base" role="alert">
          <strong>{{ getError('email') }}</strong>
        </span>

                <label class="label-base" for="email_confirmation">Confirmaciond de Email</label>
                <input class="input-base" :class="{'border-red':hasInErrors('email_confirmation')}" type="email"
                       id="email_confirmation" name="email" v-model="person.email_confirmation" required autofocus>
                <span v-if="hasInErrors('email_confirmation')" class="error-base" role="alert">
          <strong>{{ getError('email_confirmation') }}</strong>
        </span>
                <label for="address" class="label-base">Dirección</label>

                <textarea id="address" class="input-base" :class="{'border-red':hasInErrors('address')}"
                          v-model="person.address" required autofocus name="address">
        </textarea>

                <span v-if="hasInErrors('address')" class="error-base" role="alert">
          <strong>{{ getError('address') }}</strong>
        </span>
                <label for="password" class="label-base">Clave</label>

                <input id="password" type="password" :class="{'border-red':hasInErrors('password')}" class="input-base"
                       name="password" v-model="person.password" required>
                <span v-if="hasInErrors('password')" class="error-base" role="alert">
          <strong>{{ getError('password') }}</strong>
        </span>

                <label for="password_confirmation" class="label-base">Confirmación de Clave</label>

                <input id="password_confirmation" v-model="person.password_confirmation" type="password"
                       :class="{'border-red':hasInErrors('password_confirmation')}" class="input-base"
                       name="password_confirmation" required>
                <span v-if="hasInErrors('password_confirmation')" class="error-base" role="alert">
          <strong>{{ getError('password_confirmation') }}</strong>
        </span>

                <button type="button" @click.prevent="registerPerson" class="btn btn-primary mt-2">
                    Registrar
                </button>
            </form>
        </div>
    </div>
</template>
<script>
    import Error from '../mixins/ErrorMixins';

    export default {
        name: 'registerClient',
        mixins: [Error],
        props: {
            authUser: {
                type: Object,
                default() {
                    return {}
                }
            },
            roles: {
                type: Array,
                default() {
                    return []
                }
            }
        },
        data() {

            return {
                errors: [],
                person: {
                    idn: '',
                    idn_type: '',
                    name: '',
                    last_name: '',
                    email: '',
                    email_confirmation: '',
                    password: '',
                    password_confirmation: '',
                    idn: '',
                    idn_type: '',
                    phone: '',
                    address: ''
                },
                idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT'],
                selectedRole: []
            }
        },
        methods: {

            registerPerson() {
                axios.post('/registerMember', {person: this.person, roles: this.selectedRole}).catch(error => {
                    this.errors = error.response.data.errors;
                });
            },
            hasRole(role) {
                return !!this.user.roles.filter(({name}) => name === role).length;
            },
        },
        mounted() {
            if (this.user) {
                this.selectedRole = this.user.roles;
                this.person = this.user;
            }
        }
    }
</script>

<style lang="css" scoped>
</style>
