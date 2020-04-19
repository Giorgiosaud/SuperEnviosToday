<script>
    export default {
        name: 'resetPassword',
        data: () => ({
            idnType: '',
            idn: '',
            email: '',
            password: '',
            confirmation: '',
        }),
        mounted() {
            this.idn = this.$refs.idn.value
            this.idnType = this.$refs.idn_type.value
            this.email = this.$refs.email.value
            this.password = this.$refs.password.value
            if ([this.$refs.idn.value,
                this.$refs.idn_type.value,
                this.$refs.email.value,
                this.$refs.password.value].some(el => el !== '')) {
                this.$nextTick(() => {
                    this.$refs.form.validate()
                })
            }
        },
        methods: {
            onSubmit(val) {
                console.log(val)
            }
        }
    }
</script>
