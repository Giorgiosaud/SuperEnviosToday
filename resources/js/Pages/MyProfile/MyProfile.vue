<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1>{{ user.name }} {{ user.last_name }} </h1>
      </div>
      <div class="col-12">
        <h3 class="text-muted">{{ user.idn_type }}-{{ user.idn }}</h3>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-12">
        <h1>Mis Datos</h1>
      </div>
      <div class="col-12">
        <label
          for="name"
          class="label-base">Nombre</label>
        <input
          id="name"
          v-model="user.name"
          type="text"
          class="input-base">
      </div>
      <div class="col-12">
        <label
          for="last_name"
          class="label-base">Apellido</label>
        <input
          id="last_name"
          v-model="user.last_name"
          type="text"
          class="input-base">
      </div>
      <div class="col-12">
        <label
          for="last-name"
          class="label-base">Email</label>
        <input
          id="last-name"
          v-model="user.email"
          type="text"
          class="input-base">
      </div>
      <div class="col-12">
        <label
          for="phone"
          class="label-base">Telefono</label>
        <input
          id="phone"
          v-model="user.phone"
          type="text"
          class="input-base">
      </div>
      <div class="col-12">
        <label
          for="address"
          class="label-base">Dirección</label>
        <input
          id="address"
          v-model="user.address"
          type="text"
          class="input-base">
      </div>
      <div class="col-12">
        <button
          class="btn-primary btn"
          @click="updateData">Actualizar
        </button>
      </div>
      <!--div class="col-12">
                    <label
                      for="password"
                      class="label-base">Clave</label>
                    <input
                      id="password"
                      v-model="password"
                      type="password"
                      class="input-base">
                  </div>
                  <div class="col-12">
                    <label
                      for="password_confirmation"
                      class="label-base">Confirmacion de Clave</label>
                    <input
                      id="password_confirmation"
                      v-model="password_confirmation"
                      type="text"
                      class="input-base">
                  </div-->
    </div>
    <hr>
    <div
      v-if="user.roles.length>1"
      class="row">
      <h1 class="col-12">Añadir Cuenta Personal</h1>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MyProfile',
  data() {
    return {
      user: '',
      password_confirmation: '',
      password: '',
    };
  },
  created() {
    window.axios.get('api/my_info').then((response) => {
      this.user = response.data;
    });
  },
  methods: {
    updateData() {
      window.axios.patch('api/my_info', this.user);
    },
  },
};
</script>

<style scoped>

</style>
