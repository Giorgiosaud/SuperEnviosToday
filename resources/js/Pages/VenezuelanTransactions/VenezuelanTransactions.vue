<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-3">
                    <h1>
                        Mis Transacciones
                    </h1>
                </div>
            </div>
            <div class="row">
                <div
                    class="w-100 d-flex align-center justify-content-center"
                    v-if="loading"
                >
                    <div class="loading">
                        <div/>
                        <div/>
                        <div/>
                        <div/>
                    </div>
                </div>
                <div
                    class="w-100 d-flex align-center justify-content-center"
                    v-else-if="empty"
                >
                    <h2>no hay operaciones pendientes</h2>
                </div>
                <div
                    class="container"
                    v-else
                >
                    <div
                        class="table-responsive"
                    >
                        <table class="table">
                            <thead>
                            <tr>
                                <th
                                    :key="headerIndex"
                                    class="text-left"
                                    v-for="(header, headerIndex) in headers"
                                >
                                    {{ header }}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                :key="transactionKey"
                                v-for="(transaction, transactionKey) in venezuelanTransactions"
                            >
                                <td
                                    :key="keyIndex"
                                    v-for="(key, keyIndex) in keysToShow"
                                >
                    <span v-if="key==='idn'">
                      {{ transaction.client.idn_type }} - {{ transaction.client.idn }}
                    </span>
                                    <span v-else>
                      {{ transaction[key] }}
                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'VenezuelanTransactions',
        data() {
            return {
                loading: true,
            };
        },
        created() {
            this.getVenezuelanTransactions();
        },
        methods: {
            getVenezuelanTransactions() {
                this.loading = true;
                axios.get('api/my-venezuelan-transactions').then((response) => {
                    this.venezuelanTransactions = response.data.data;
                }).finally(() => {
                    this.loading = false;
                });
            },
        },
        // TODO make show list of transactions and modal to accomplish transactions
    };
</script>
<style scoped>

</style>
