<template>
    <div class="container">
        <currencies></currencies>
        <hr>
        <bank></bank>
        <hr>
        <status></status>
    </div>
</template>

<script>
    import Bank from './Bank';
    import Currencies from './Currencies';
    import Status from './Status';

    export default {
        name: 'Settings',
        components: {
            Bank,
            Currencies,
            Status,
        },
        created() {
            this.getCurrencies();
            this.getBanks();
        },
        methods: {
            getCurrencies() {
                this.$store.dispatch('settings/GET_CURRENCIES');
            },
            getBanks(){
                this.$store.dispatch('settings/GET_BANKS');
            }
        }
    };
</script>

<style scoped>

</style>
