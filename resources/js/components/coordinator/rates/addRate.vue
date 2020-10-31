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
       <section>
            <b-field label="Select datetime">
              <b-datetimepicker
                v-model="since"
                mobile-native
                placeholder="Click to select..."
                icon="calendar-today"
                readonly
                :datepicker="{ showWeekNumber:true }"
                :timepicker="{ enableSeconds:true }">
                <template slot="left">
                  <button class="button is-primary"
                          @click="since = new Date()">
                    <b-icon icon="clock"></b-icon>
                    <span>Now</span>
                  </button>
                </template>
              </b-datetimepicker>
            </b-field>
            <b-field label="Amount">
              <b-input v-model="amount"></b-input>
            </b-field>
            <b-field label="Tipo de moneda">
              <b-select placeholder="Tipo de moneda" v-model="currency_id" expanded>
                <option v-for="currency in currencies" :value="currency.id" :key="currency.id">
                  {{currency.name}}
                </option>
              </b-select>
            </b-field>
            <b-field label="Comentario">
              <quill-editor

                            id="comment"
                            v-model.lazy="message"></quill-editor>

            </b-field>

          </section>
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
          @click="newRate"
          @keypress.enter="newRate">
          Guardar
        </button>
      </footer>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddRate',
  props: {
    currencies: {
      type: Array,
      default: () => ([]),
    },
  },
  data: () => ({
    since: new Date(),
    currency_id: '',
    amount: '',
    message: '',
    savingRate: false,
  }),
  methods: {
    async newRate() {
      this.savingRate = true;
      try {
        await $http.post('api/rates', {
          since: this.since,
          currency_id: this.currency_id,
          amount: this.amount,
          message: this.message,
        });
        this.$emit('currency-created');
      } finally {
        this.savingRate = false;
        this.since = new Date();
        this.currency_id = '';
        this.amount = '';
        this.message = '';
        this.savingRate = false;
        this.$parent.close();
      }
    },
  },
};
</script>

<style scoped>

</style>
