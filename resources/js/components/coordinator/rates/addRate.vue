<template>
  <div>
    <div
      class="modal-card"
      style="width: auto">
      <header class="modal-card-head">
        <p class="modal-card-title">
          Agregue una Configuración
        </p>
      </header>
      <section class="modal-card-body">
        <b-field label="key">
          <b-input
            v-model="key"
            type="text"
            placeholder="Key"
            required />
        </b-field>
        <b-field label="value">
          <b-input
            v-model="value"
            type="text"
            placeholder="Valor"
            required />
        </b-field>
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
          @click="newSetting"
          @keypress.enter="newSetting">
          Guardar
        </button>
      </footer>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddRate',
  data: () => ({
    key: '',
    value: '',
    savingSetting: false,
  }),
  methods: {
    async newSetting() {
      this.savingSetting = true;

      try {
        await $http.post('api/settings', {
          key: this.key,
          value: this.value,
        });
      } finally {
        this.savingSetting = false;
        this.$parent.close();
        this.$emit('setting-created');
      }
    },
  },
};
</script>

<style scoped>

</style>
