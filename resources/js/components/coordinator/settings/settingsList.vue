<script>
import addSetting from './addSetting';

export default {
    name: "settingsList",
    props: {
        'settingsQuery': {
            type: Object,
            default: () => ({
                data: {}
            })
        },
    },
    components: {
        addSetting
    },
    data: () => (
        {
            query: {},
            loading: false,
            filters: {},
            defaultOpenedDetails: [],
            savingSetting: false,
            isOpenModal: false,
            removingSetting: false
        }
    ),
    created() {
        this.query = this.settingsQuery;
        this.page = this.query.current_page;

    },
    computed: {
        settings() {
            return this.query.data;
        }
        ,
        currentPage() {
            return this.query.currentPage ? this.query.currentPage : 0;
        }
        ,
        lastPage() {
            return this.query.last_page ? this.query.last_page : 0;
        }
        ,
        path() {
            return this.query.path ? this.query.path : '';
        }
    }
    ,
    methods: {
        openAddSettingModal() {
            this.isOpenModal = true;
        },
        async removeSetting(id) {
            this.$buefy.dialog.confirm({
                message: 'Continue on this task?',
                onConfirm: async () => {
                    this.removingSetting = true
                    try {
                        await $http.delete(`api/settings/${id}`)
                    } finally {
                        this.removingSetting = false
                        await this.loadAsyncData();
                    }
                }
            })

        },
        async changeSetting(id, key, value) {
            this.savingSetting = true
            try {
                await $http.patch(`api/settings/${id}`, {
                    key,
                    value,
                })
            } finally {
                this.savingSetting = false;
                await this.loadAsyncData();
            }
        },
        toggle(row) {
            this.$refs.settingsTable.toggleDetails(row)
        },
        paramsToObject(entries) {
            let result = {}
            for (let entry of entries) { // each 'entry' is a [key, value] tupple
                const [key, value] = entry;
                result[key] = value;
            }
            return result;
        },
        changedPage(page) {
            this.page = page;
            this.loadAsyncData();
        },
        changedFilter(filters) {
            for (const filter in filters) {
                if (filters[filter] === '') {
                    delete filters[filter]
                }
            }
            this.filters = filters;
            this.loadAsyncData();
        },
        async loadAsyncData() {
            const urlParams = new URLSearchParams(document.location.search.substring(1));
            const entries = urlParams.entries();
            const params = this.paramsToObject(entries);
            params.page = this.page;
            Object.assign(params, this.filters);
            this.loading = true;
            const request = await $http.get('/api/settings', {params: {...params}})
            this.query = await request.json();
            this.loading = false;

        },
    },
}
</script>
