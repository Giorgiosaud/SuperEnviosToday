<script>
import addSetting from './addSetting.vue';

export default {
    name: 'SettingsList',
    components: {
        addSetting,
    },
    props: {
        settingsQuery: {
            type: Object,
            default: () => ({
                data: {},
            }),
        },
    },
    data: () => (
        {
            query: {},
            loading: false,
            filters: {},
            defaultOpenedDetails: [],
            savingSetting: false,
            isOpenModal: false,
            removingSetting: false,
        }
    ),
    computed: {
        settings() {
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
    created() {
        this.query = this.settingsQuery;
        this.page = this.query.current_page;
    },
    methods: {
        openAddSettingModal() {
            this.isOpenModal = true;
        },
        async removeSetting(id) {
            this.$buefy.dialog.confirm({
                message: 'Continue on this task?',
                onConfirm: async () => {
                    this.removingSetting = true;
                    try {
                        await $http.delete(`api/settings/${id}`);
                    } finally {
                        this.removingSetting = false;
                        await this.loadAsyncData();
                    }
                },
            });
        },
        async changeSetting(id, key, value) {
            this.savingSetting = true;
            try {
                await $http.patch(`api/settings/${id}`, {
                    key,
                    value,
                });
            } finally {
                this.savingSetting = false;
                await this.loadAsyncData();
            }
        },
        toggle(row) {
            this.$refs.settingsTable.toggleDetails(row);
        },

        changedPage(page) {
            this.page = page;
            this.loadAsyncData();
        },

        async loadAsyncData() {
            const params = this.getAllUrlParams(document.location.href)
            params.page = this.page;
            Object.assign(params, this.filters);
            this.loading = true;
            const request = await $http.get('/api/settings', {params: {...params}});
            this.query = await request.json();
            this.loading = false;
        },
    },
};
</script>
