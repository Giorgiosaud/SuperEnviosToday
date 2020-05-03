<script>
    export default {
        name: "userDetail",
        props: {
            'user': {
                type: Object,
                default: () => ({})
            },
            'allRoles': {
                type: Array,
                default: () => ([])
            }
        },
        data: () => (
            {
                editable: false,
                userData: {
                    idn_type: '',
                    idn: '',
                    name: '',
                    last_name: '',
                    email: '',
                    email_verified_at: '',
                    phone: '',
                    address: '',
                    roles: [],
                },
                filteredRoles:[]
            }
        ),
        created() {
            this.userData = this.user;
            this.filteredRoles = this.allRoles;
        },
        mounted() {

        },
        computed: {
            roles() {
                return this.allRoles.map(role => role.name)
            },
            roleNames() {
                if(this.userData.roles.length) {
                 return this.userData.roles.map(role => role.name_id)
                }
            }

        },
        methods: {
            cancel() {
                window.location.reload()
            },
            getFilteredTags(text) {
                this.filteredRoles = this.allRoles
                    .filter((role) => {
                        return role.name
                            .toString()
                            .toLowerCase()
                            .indexOf(text.toLowerCase()) >= 0
                    })
            }
        }
    }
</script>
