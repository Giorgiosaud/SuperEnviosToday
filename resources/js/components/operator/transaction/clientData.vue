<template>
  <section class="section">
    <section class="section is-paddingless">
      <validation-observer v-slot="{ invalid }">
        <div class="columns">
          <div class="column is-narrow">
            <validation-provider
              rules="required"
              v-slot="{ classes,errors,valid }"
              :name="$t('auth.IDN_TYPE')"
              tag="div"
              class="control">
              <label class="label" for="idn_type">{{$t('auth.IDN_TYPE')}}</label>
              <div class="control has-icons-left has-icons-right" :class="{'no-arrow':valid}">
                <div class="select"
                     :class="classes">
                  <select
                    id="idn_type"
                    name="idn_type"
                    v-model="client.idn_type">
                    <option value="">{{$t('auth.DEFAULT:IDNTYPE')}}</option>
                    <option value="CI">Cédula Venezolana</option>
                    <option value="PASSPORT">Pasaporte</option>
                    <option value="RUT">RUT</option>
                    <option value="DNI">DNI</option>
                    <option value="RIF">RIF</option>
                  </select>
                  <span class="icon is-small has-text-success is-right mr-3" v-if="valid">
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
          </div>
          <div class="column">
            <validation-provider
              rules="required"
              :name="$t('auth.IDN')"
              v-slot="{ classes,errors,valid}"
              tag="div"
              class="control">
              <label class="label" for="idn">{{$t('auth.IDN')}}</label>
              <div class="control has-icons-right">
                <input id="idn"
                       v-model="client.idn"
                       name="idn"
                       class="input"
                       @keydown.enter="searchClient"
                       :class="classes"
                       type="text"
                       :placeholder="$t('auth.IDN')"
                       value="123"
                       autocomplete="idn" autofocus>
                <span class="icon is-small has-text-warning is-right"
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
          <div class="column is-narrow">
            <div class="control">
              <label for="idn" class="label">{{$t('transaction.SEARCH:LABEL')}}</label>
              <b-button size="is-big"
                        type="is-info"
                        :loading="searchingClient"
                        :disabled="invalid"
                        icon-left="search"
                        @click="searchClient">
                {{$t('transaction.SEARCH:BUTTON')}}
              </b-button>
            </div>
          </div>
        </div>
      </validation-observer>
    </section>
    <section class="section is-paddingless" v-if="searchExecuted">
      <validation-observer v-slot="{ invalid }" slim ref="secondObserver  ">
        <validation-provider
          rules="required"
          tag="div"
          class="field">
          <input
            v-model="client.idn_type"
            id="idn_type"
            type="hidden"
            name="idn_type"/>
        </validation-provider>
        <validation-provider

          rules="required"
          tag="div"
          class="field">
          <input
            v-model="client.idn"
            id="idn_type"
            type="hidden"
            name="idn_type"/>
        </validation-provider>
        <div class="columns">
          <div class="column">
            <validation-provider

              rules="required"
              v-slot="{ classes,errors, valid }"
              tag="div"
              class="field">

              <label class="label" for="name">{{$t('auth.NAME')}}</label>
              <div v-if="clientReady">
                {{client.name}}
              </div>
              <div class="control has-icons-right" v-else>
                <input id="name"
                       v-model="client.name"
                       name="name"
                       class="input"
                       :class="classes"
                       type="text"
                       :placeholder="$t('auth.NAME')"
                       autocomplete="name" autofocus>
                <span class="icon is-small has-text-warning is-right"
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
          </div>
          <div class="column">
            <validation-provider
              :name="$t('auth.LAST_NAME')"
              rules="required"
              v-slot="{ classes,errors, valid }"
              tag="div"
              class="field">
              <label
                class="label"
                for="last_name">{{$t('auth.LAST_NAME')}}</label>
              <div v-if="clientReady">
                {{client.last_name}}
              </div>
              <div class="control has-icons-right" v-else>
                <input   t id="last_name"
                         name="last_name"
                         v-model="client.last_name"
                         class="input"
                         :class="classes"
                         type="text"
                         :placeholder="$t('auth.LAST_NAME')"
                         autocomplete="last_name" autofocus>
                <span class="icon is-small has-text-warning is-right"
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
          </div>
          <div class="column">
            <validation-provider
              :name="$t('auth.EMAIL')"
              rules="required|email"
              v-slot="{ classes,errors,valid }"
              tag="div"
              class="field">

              <label class="label" for="email">{{$t('auth.EMAIL')}}</label>
              <div v-if="clientReady">
                {{client.email}}
              </div>
              <div class="control has-icons-left has-icons-right" v-else>
                <input id="email" name="email"
                       :class="classes"
                       class="input"
                       type="text"
                       v-model="client.email"
                       :placeholder="$t('auth.EMAIL')"
                       autocomplete="email" autofocus>
                <span class="icon is-small is-left">
                                <font-awesome-icon icon="envelope"></font-awesome-icon>
                            </span>
                <span class="icon is-small has-text-warning is-right" v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
              </div>
              <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
          </div>
          <div class="column">
            <validation-provider
              :name="$t('auth.PHONE')"
              rules="alpha_dash"
              v-slot="{ classes,errors,valid }"
              tag="div"
              class="field">

              <label class="label" for="email">{{$t('auth.PHONE')}}</label>
              <div v-if="clientReady">
                {{client.phone}}
              </div>
              <div class="control has-icons-left has-icons-right" v-else>
                <input id="phone" name="phone"
                       :aria-label="$t('auth.PHONE')"
                       :class="classes"
                       class="input"
                       type="text"
                       v-model="client.phone"
                       :placeholder="$t('auth.PHONE')"
                       autocomplete="phone" autofocus>
                <span class="icon is-small is-left">
                                <font-awesome-icon icon="phone"></font-awesome-icon>
                            </span>
                <span class="icon is-small has-text-warning is-right" v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
                <span class="icon is-small has-text-success is-right" v-if="valid">
                            <font-awesome-icon icon="check"></font-awesome-icon>
                        </span>
              </div>
              <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
            </validation-provider>
          </div>
          <div class="column is-narrow" v-if="!clientReady">
            <label for="idn" class="label">{{$t('transaction.SAVE:LABEL')}}</label>
            <b-button size="is-big"
                      type="is-info"
                      :loading="savingClient"
                      :disabled="invalid"
                      icon-left="save"
                      @click="saveClient">
              {{$t('transaction.SAVE:BUTTON')}}
            </b-button>
          </div>
        </div>
        <div class="columns">
          <div class="column">
            <b-button size="is-big"
                      type="is-info"
                      :loading="savingClient"
                      :disabled="client.id===''"
                      icon-right="arrow-circle-right"
                      @click="nextStep">
              {{$t('transaction.NEXT:BUTTON')}}
            </b-button>
          </div>                .
        </div>                .

      </validation-observer>
    </section>
    <b-modal :active.sync="isComponentModalActive"
             has-modal-card
             trap-focus
             :destroy-on-hide="false"
             aria-role="dialog"
             aria-modal>
      <div class="card">
        <header class="card-header">
          <p class="card-header-title">
            {{$t('transaction.CLIENT:DIFFERENT_IDN:EXIST')}}
            {{$t('transaction.CLIENT:DIFFERENT_IDN:EXIST:SUB')}}
          </p>
        </header>
        <div class="card-content">
          <div class="content">
            {{possibleClient.idn_type}}
            {{possibleClient.idn}}
            {{possibleClient.name}}
            {{possibleClient.last_name}}
            {{possibleClient.email}}
            {{possibleClient.phone}}
            {{possibleClient.id}}
          </div>
        </div>
        <footer class="card-footer">
          <a @click="usePossibleClient" class="card-footer-item">Utilizar el Sugerido</a>
          <a @click="createNew" class="card-footer-item">Crear Nuevo</a>
        </footer>
      </div>
    </b-modal>
  </section>
</template>
<script>
export default {
  // TODO continuar revisando flujo de creacion de transaccion
  name: 'ClientData',
  data: () => ({
    searchButtonText: 'Buscar',
    client: {
      idn: '',
      idn_type: '',
      name: '',
      last_name: '',
      email: '',
      phone: '',
    },
    possibleClient: {
      id: '',
      idn: '',
      idn_type: '',
      name: '',
      last_name: '',
      email: '',
      phone: '',
    },
    clientReady: false,
    searchingClient: false,
    searchExecuted: false,
    savingClient: false,
    isComponentModalActive: false,
  }),
  methods: {
    nextStep() {
      this.$emit('next-step');
    },
    setClient(data) {
      /*eslint-disable*/
      const {
        id, idn, idn_type, last_name, phone, email, name,
      } = data;

      this.client.id = id;
      this.client.idn = idn;

      this.client.idn_type = idn_type;
      this.client.name = name;
      this.client.last_name = last_name;
      this.client.email = email;
      this.client.phone = phone;
      this.clientReady = true;
      this.$emit('client-data-set', this.client);
    },
    setPossibleClient(data) {
      const {
        id, idn, idn_type, last_name, phone, email, name,
      } = data;
      this.possibleClient.id = id;
      this.possibleClient.idn = idn;
      this.possibleClient.idn_type = idn_type;
      this.possibleClient.name = name;
      this.possibleClient.last_name = last_name;
      this.possibleClient.email = email;
      this.possibleClient.phone = phone;
      this.clientReady = true;
      /* eslint-enable */
    },
    async searchClient() {
      if (this.client.idn === '' || this.client.idn_type === '') {
        return;
      }
      this.searchingClient = true;
      const response = await $http.get(`/api/user/${this.client.idn_type}/${this.client.idn}`);
      if (response.status === 204) {
        this.client.id = '';
        this.client.name = '';
        this.client.last_name = '';
        this.client.email = '';
        this.client.phone = '';
        this.clientReady = false;
      } else {
        const UserData = await response.json();
        if (UserData.status === 'OK') {
          this.setClient(UserData.user);
        } else {
          this.setPossibleClient(UserData.user);
          this.isComponentModalActive = true;
        }
      }
      this.searchExecuted = true;
      this.searchingClient = false;
    },
    usePossibleClient() {
      this.setClient(this.possibleClient);
      this.isComponentModalActive = false;
    },
    createNew() {
      this.clientReady = false;
      this.isComponentModalActive = false;
    },
    async saveClient() {
      this.savingClient = true;
      try {
        const response = await $http.post('/api/user/save', {
          ...this.client,
        });
        const userData = await response.json();
        debugger;
        this.setClient(userData.user);
      } catch (error) {
        debugger;
      } finally {
        this.savingClient = false;
      }
    },
  },
};
</script>

<style scoped>

</style>
