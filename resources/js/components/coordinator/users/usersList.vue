<script>
export default {
    name: 'UserList',
    props: {
        usersQuery: {
            type: Object,
            default: () => ({
                data: {},
            }),
        },
        allRoles: {
            type: Array,
            default: () => ([]),
        },
    },
    data: () => (
        {
            query: {},
            loading: false,
            selected: {},
            filters: {},
            selectedRoles: [],
            filteredRoles: [],
        }
    ),
    computed: {
        roles() {
            return this.allRoles.map((role) => role.name);
        },
        users() {
            return this.query.data;
        },
        currentPage() {
            return this.query.currentPage ? this.query.currentPage : 0;
        },
        lastPage() {
            return this.query.last_page ? this.query.last_page : 0;
        },
        path() {
            return this.query.path ? this.query.path : '';
        },
    },
    watch: {
        selectedRoles() {
            this.loadAsyncData();
        },
    },
    created() {
        this.query = this.usersQuery;
        this.page = this.query.current_page;
        this.filteredRoles = this.allRoles;
    },
    methods: {
        userClicked(user) {
            if (user.id === this.selected.id) {
                this.selected = {};
            }
        },
        goToDetails() {
            window.location.href = `users/${this.selected.id}`;
        },
        changedPage(page) {
            this.page = page;
            this.loadAsyncData();
        },
        async loadAsyncData() {
            const params =this.getAllUrlParams(document.location.href)
            params.page = this.page;
            Object.assign(params, this.filters);
            if (this.selectedRoles.length) {
                params.roles = this.selectedRoles.map((role) => role.name_id);
            }
            this.loading = true;
            const request = await $http.get('/api/user', {params: {...params}});
            this.query = await request.json();
            this.loading = false;
        },
        getFilteredTags(text) {
            this.filteredRoles = this.allRoles
                .filter((role) => role.name
                    .toString()
                    .toLowerCase()
                    .indexOf(text.toLowerCase()) >= 0);
        },
    },
};
</script>
