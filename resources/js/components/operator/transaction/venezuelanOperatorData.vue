<script>

    export default {
        name: "venezuelanOperatorData",
        props: {
        },
        components: {
        },
        data: () => ({
        }),
        async created(){
            await axios.get('/api/accounts/base')
        },
        methods: {
        },
        computed: {
        },
        watch: {
        }
    }
</script>

<style scoped>

</style>
