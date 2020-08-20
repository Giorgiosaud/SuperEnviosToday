<script>
import newReceiverForm from './newReceiverForm.vue';
import editReceiverForm from './editReceiverForm.vue';
import newAccount from './newAccount.vue';
import editAccount from './editAccount.vue';

export default {
  name: 'ReceiverData',
  components: {
    newReceiverForm,
    editReceiverForm,
    newAccount,
    editAccount,
  },
  props: {
    client: {
      type: [Object, null],
      default: () => null,
    },
  },
  data: () => ({
    receiverSelected: null,
    receiverAccount: null,
    receivers: [],
    loadingReceivers: false,
    unlinkingReceiver: false,
    unlinkingAccount: false,
    activeForm: '',
    openModal: false,
  }),
  computed: {
    customComponentProps() {
      switch (this.activeForm) {
        case ('newReceiverForm'):
          return {
            client: this.client,

          };
        case ('editReceiverForm'):
          return {
            client: this.client,
            receiver: this.receiverSelected,
          };
        case 'newAccount':
          return {
            receiver: this.receiverSelected,
          };
        case 'editAccount':
          return {
            receiver: this.receiverSelected,
            account: this.receiverAccount,
          };
        default:
          return {};
      }
    },
    receiverAccounts() {
      if (this.receiverSelected) return this.receiverSelected.accounts;
      return [];
    },
  },
  watch: {
    receiverSelected() {
      this.receiverAccount = null;
    },
    client: {
      deep: true,
      async handler(value) {
        if (value) {
          await this.lookupForReceivers();
        }
      },
    },
  },

  methods: {
    openModalForm(form) {
      this.openModal = true;
      this.activeForm = form;
    },
    nextStep() {
      this.$emit('receiver-set', {
        receiver: this.receiverSelected,
        receiverAccount: this.receiverAccount,
      });
      this.$emit('next-step');
    },
    async lookupForReceivers() {
      this.loadingReceivers = true;
      try {
        const response = await $http.get(`/api/user/receivers/${this.client.id}`);
        this.receivers = await response.json();
        this.receiverSelected = null;
        this.receiverAccount = null;
      } catch (error) {
        console.log(error);
      } finally {
        this.loadingReceivers = false;
      }
    },
    async unlinkReceiver() {
      this.unlinkingReceiver = true;
      try {
        await $http.patch(`/api/account/${this.receiverAccount.id}/user/${this.receiverSelected.id}/unlink`);
      } catch (error) {
        console.log(error);
      } finally {
        await this.lookupForReceivers();
        this.unlinkingReceiver = false;
      }
    },
    async unlinkAccount() {
      this.unlinkingAccount = true;
      try {
        await $http.patch(`/api/account/${this.receiverAccount.id}/user/${this.receiverSelected.id}/unlink`);
      } catch (error) {
        console.log(error);
      } finally {
        await this.lookupForReceivers();
        this.unlinkingAccount = false;
      }
    },
  },
};
</script>

<style scoped>

</style>
