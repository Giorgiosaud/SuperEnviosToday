<template>
    <div>
        <h1>Status de sistema</h1>
        <form>
            <div class="mb-2">
                <div class="form-switch inline-block align-middle">
                    <input type="checkbox" name="1" id="1" class="form-switch-checkbox"/>
                    <label class="form-switch-label m-0" for="1"></label>
                </div>
                <label class="text-xs text-grey-dark" for="1">Estado del Sistema</label>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        name: 'Banks',
        data() {
            return {
                bankName: '',
                selectedCurrency: '',
            }
        },
        computed: {
            banks() {
                return this.$store.state.settings.banks;
            },
            currencies() {
                return this.$store.state.settings.currencies;
            },
        },
        methods: {
            createBank() {
                this.$store.dispatch('settings/CREATE_NEW_BANK', {
                    name: this.bankName,
                    currency_id: this.selectedCurrency.id
                }).then(() => {
                        this.bankName = '';
                        this.selectedCurrency = '';
                    }
                );
            }
        }
    };
</script>

<style scoped>

</style>
