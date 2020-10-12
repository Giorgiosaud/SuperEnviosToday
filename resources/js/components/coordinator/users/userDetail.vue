<template>
  <div>
    <a class="button field is-link"
       v-if="!editable"
       :href="editLink"
    >
      <span>{{$t('users.LIST')}}</span>
    </a>
    <button class="button field is-info"
            v-if="!editable"
            @click="editable = true">
      <span>{{$t('users.EDIT')}}</span>
    </button>

    <b-button size="is-big"
              type="is-info"
              :loading="sendingVerification"
              icon-left="retweet"
              v-if="!userData.email_verified_at"
              @click="resendVerification">
      {{$t('users.RESEND')}}
    </b-button>
    <b-button size="is-big"
              type="is-info"
              :loading="sendingVerification"
              icon-left="people-arrows"
              @click="loginAs">
      Inciar Sesion como {{ userData.name }} {{ userData.last_name }}
    </b-button>
    <div v-if="!editable">
      <div><strong>{{$t('auth.IDN_TYPE')}}:</strong> {{ userData.idn_type }}</div>
      <div><strong>{{$t('auth.IDN')}}:</strong> {{ userData.idn }}</div>
      <div><strong>{{$t('auth.NAME')}}:</strong> {{ userData.name }}</div>
      <div><strong>{{$t('auth.LAST_NAME')}}:</strong> {{ userData.last_name }}</div>
      <div><strong>{{$t('auth.EMAIL')}}:</strong> {{ userData.email }}</div>
      <div><strong>{{$t('auth.PHONE')}}:</strong> {{ userData.phone }}</div>
      <div><strong>{{$t('auth.ADDRESS')}}:</strong> {{ userData.address }}</div>
      <div><strong>{{$t('users.ROLES')}}:</strong> <span class="tag mx-1"
                                                         v-for="role in userData.roles"
                                                         :class="{
                                      'is-danger':role.name_id==='coordinator',
                                      'is-warning':role.name_id==='foreign_operator',
                                      'is-success':role.name_id==='receiver',
                                      'is-info':role.name_id==='venezuelan_operator',
                                      'is-primary':role.name_id==='client'
                                      }">
                                   {{ role.name}}
                                </span></div>
      <div class="columns">
        <div class="column">
          <h2 class="is-size-2 has-text-centered">Cuentas Asociadas</h2>

          <b-table :data="accounts">

            <b-table-column
              field="id"
              label="ID"
              numeric
              v-slot="{row:account}">
              {{ account.id }}
            </b-table-column>
            <b-table-column
              field="bank"
              label="Bank"
              v-slot="{row:account}">
              {{ account.bank.name }}
            </b-table-column>
            <b-table-column
              field="number"
              label="Number"
              v-slot="{row:account}">
              {{ account.number }}
            </b-table-column>

            <b-table-column
              field="balance"
              label="Saldo"
              v-slot="{row:account}">
              {{account.balance|currencyFilter({
              ...account.bank.currency,
              formatWithSymbol:account.bank.currency.format_with_symbol === 1
            }) }}
            </b-table-column>
            <b-table-column v-slot="{row:account}">
              <b-button type="is-danger" @click="unlinkAccount(user.id,account.id)">
                Desasociar Cuenta
              </b-button>
            </b-table-column>
          </b-table>
        </div>
      </div>
    </div>
    <div v-else>
      <validation-observer ref="form">
        <form method="POST"
              :action="formAction">
          <slot name="csrf"></slot>
          <input type="hidden" name="_method" value="PUT">
          <validation-provider
            rules="required"
            v-slot="{ classes,errors,valid }"
            :name="$t('users.ROLES')"
            tag="div"
            class="field">
            <label class="label">{{$t('users.ROLES')}}</label>
            <div class="control has-icons-right">
              <b-taginput
                v-model="userData.roles"
                :data="filteredRoles"
                autocomplete
                :allow-new="false"
                :open-on-focus="true"
                field="name"
                icon="label"
                :placeholder="$t('users.SELECT:ROLE')"
                @typing="getFilteredTags">
              </b-taginput>
            </div>

            <strong
              v-if="errors[0]"
              class="help is-danger">{{errors[0]}}</strong>
          </validation-provider>
          <input type="hidden"
                 name="roles"
                 v-model="roleNames">
          <validation-provider
            rules="required"
            v-slot="{ classes,errors,valid }"
            :name="$t('auth.IDN_TYPE')"
            tag="div"
            class="field">
            <label class="label" for="idn_type">{{$t('auth.IDN_TYPE')}}</label>
            <div class="control has-icons-left has-icons-right">
              <div class="select"
                   :class="classes">
                <select
                  id="idn_type"
                  name="idn_type"
                  v-model="userData.idn_type">
                  <option value="">{{$t('auth.DEFAULT:IDNTYPE')}}</option>
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
            :name="$t('auth.IDN')"
            v-slot="{ classes,errors,valid}"
            tag="div"
            class="field">
            <label class="label" for="idn">{{$t('auth.IDN')}}</label>
            <div class="control has-icons-right">
              <input id="idn"
                     v-model="userData.idn"
                     name="idn"
                     class="input"
                     :class="classes"
                     type="text"
                     :placeholder="$t('auth.IDN')"
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

            <label class="label" for="name">{{$t('auth.NAME')}}</label>
            <div class="control has-icons-right">
              <input id="name"
                     v-model="userData.name"
                     name="name"
                     class="input"
                     :class="classes"
                     type="text"
                     :placeholder="$t('auth.NAME')"
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
            :name="$t('auth.LAST_NAME')"
            rules="required"
            v-slot="{ classes,errors, valid }"
            tag="div"
            class="field">
            <label
              class="label"
              for="last_name">{{$t('auth.LAST_NAME')}}</label>
            <div class="control has-icons-right">
              <input id="last_name"
                     name="last_name"
                     v-model="userData.lastName"
                     class="input"
                     :class="classes"
                     type="text"
                     :placeholder="$t('auth.LAST_NAME')"
                     autocomplete="last_name" autofocus>
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
            :name="$t('auth.EMAIL')"
            rules="required|email"
            v-slot="{ classes,errors,valid }"
            tag="div"
            class="field">

            <label class="label" for="email">{{$t('auth.EMAIL')}}</label>
            <div class="control has-icons-left has-icons-right">
              <input id="email" name="email"
                     :class="classes"
                     class="input"
                     type="text"
                     v-model="userData.email"
                     :placeholder="$t('auth.EMAIL')"
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
            <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
          </validation-provider>
          <validation-provider
            :name="$t('auth.PHONE')"
            rules="alpha_dash"
            v-slot="{ classes,errors,valid }"
            tag="div"
            class="field">

            <label class="label" for="phone">{{$t('auth.PHONE')}}</label>
            <div class="control has-icons-left has-icons-right">
              <input id="phone" name="phone"
                     :class="classes"
                     class="input"
                     type="text"
                     v-model="userData.phone"
                     :placeholder="$t('auth.PHONE')"
                     autocomplete="phone" autofocus>
              <span class="icon is-small is-left">
                                <font-awesome-icon icon="phone"></font-awesome-icon>
                            </span>
              <span class="icon is-small has-text-warning	is-right" v-if="errors[0]">
                                <font-awesome-icon icon="exclamation-triangle"></font-awesome-icon>
                            </span>
              <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
            </div>
            <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
          </validation-provider>
          <validation-provider
            :name="$t('auth.ADDRESS')"
            rules="alpha_dash"
            v-slot="{ classes,errors,valid }"
            tag="div"
            class="field">

            <label class="label" for="address">{{$t('auth.ADDRESS')}}</label>
            <div class="control has-icons-right">
                            <textarea id="address"
                                      name="address"
                                      class="textarea"
                                      :class="classes"
                                      type="text"
                                      v-model="userData.address"
                                      rows="3"
                                      :placeholder="$t('auth.ADDRESS')"
                                      autocomplete="phone" autofocus></textarea>
              <span class="icon is-small has-text-success is-right" v-if="valid">
                                <font-awesome-icon icon="check"></font-awesome-icon>
                            </span>
            </div>
            <strong v-if="errors[0]" class="help is-danger">{{errors[0]}}</strong>
          </validation-provider>
          <div class="field is-grouped">
            <div class="control">
              <button type="submit" class="button is-primary">{{ $t('users.UPDATE') }}</button>
            </div>
            <div class="control">
              <button @click.prevent="cancel"
                      class="button is-danger">{{ $t('users.CANCEL') }}</button>
            </div>
          </div>
        </form>
      </validation-observer>
    </div>
  </div>
</template>
<script>
import currencyFilter from '../../../currency';

export default {
  name: 'UserDetail',
  filters: {
    currencyFilter
  },

  props: {
    editLink:{
      type:String,
      default:'#'
    },
    formAction:{
      type:String,
      default:'#'
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    accounts: {
      type: Array,
      default: () => ([]),
    },
    allRoles: {
      type: Array,
      default: () => ([]),
    },
  },
  data: () => (
    {
      isToggling: false,
      editable: false,
      userData: {
        idn_type: '',
        idn: '',
        name: '',
        last_name: '',
        email: '',
        email_verified_at: '',
        phone: '',
        address: '',
        roles: [],
      },
      unlinkingAccount:false,
      filteredRoles: [],
      sendingVerification: false,
    }
  ),
  computed: {
    roles() {
      return this.allRoles.map((role) => role.name);
    },
    roleNames() {
      if (this.userData.roles.length) {
        return this.userData.roles.map((role) => role.name_id);
      }
      return null;
    },

  },
  created() {
    this.userData = this.user;
    this.filteredRoles = this.allRoles;
  },
  mounted() {

  },
  methods: {
    async unlinkAccount(userId,accountId) {
      this.unlinkingAccount = true;
      try {
        await $http.patch(`/api/account/${accountId}/user/${userId}/unlink`);
      } catch (error) {
        console.log(error);
      } finally {
        window.location.reload()
      }
    },
    loginAs() {
      window.location.href = `/users/login-as/${this.user.id}`;
    },
    async resendVerification() {
      this.sendingVerification = true;
      await $http.post('/api/user/verify_email', {
        id: this.user.id,
      });
      this.sendingVerification = false;
    },
    cancel() {
      window.location.reload();
    },
    getFilteredTags(text) {
      this.filteredRoles = this.allRoles
        .filter((role) => role.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0);
    },
  },
};
</script>
