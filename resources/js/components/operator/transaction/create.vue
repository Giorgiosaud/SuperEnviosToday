<script>
import clientData from './clientData.vue';
import transactionData from './transactionData.vue';
import receiverData from './receiverData.vue';
import venezuelanOperatorData from './venezuelanOperatorData.vue';

export default {
  name: 'TransactionCreate',
  components: {
    clientData,
    transactionData,
    receiverData,
    venezuelanOperatorData,
  },
  props: {
    operator: {
      type: Object,
      default: () => ({}),
    },
  },
  data: () => ({
    clientData: null,
    actualStep: 0,
    transactionData: null,
    receiverData: null,
    venezuelanOperatorData: null,
  }),
  methods: {
    async clientDataSet(client) {
      this.clientData = client;
    },

    nextStep() {
      this.actualStep += 1;
    },
    clientReceiverSet(received) {
      this.receiverData = received;
    },
    transactionDataSet(transaction) {
      this.transactionData = transaction;
      this.nextStep();
    },
    venezuelanOperatorDataSet(operatorDataSet) {
      this.venezuelanOperatorData = operatorDataSet;
      this.nextStep();
    },
    executedTransaction() {

    },
  },

};

</script>

<style scoped>

</style>
