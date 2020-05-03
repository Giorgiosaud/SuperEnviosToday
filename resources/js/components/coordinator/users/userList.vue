<script>
    export default {
        name: "userList",
        props: {
            'usersQuery': {
                type: Object,
                default: () => ({
                    data: {}
                })
            },
            'allRoles': {
                type: Array,
                default: () => ([])
            }
        },
        data: () => (
            {
                query: {},
                loading: false,
                selected: {},
                filters: {},
                selectedRoles: [],
                filteredRoles:[]
            }
        ),
        created() {
            this.query = this.usersQuery;
            this.page = this.query.current_page;
            this.filteredRoles = this.allRoles;
        },
        computed: {
            roles() {
                return this.allRoles.map(role => role.name)
            },
            users() {
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
            userClicked(user) {
                if (user.id === this.selected.id) {
                    this.selected = {}
                }
            }
            ,
            goToDetails() {
                window.location.href = `users/${this.selected.id}`;
            }
            ,
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
                console.log(filters)
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
                if(this.selectedRoles.length){
                    params.roles=this.selectedRoles.map(role=>role.name_id)
                }
                console.log(params)
                this.loading = true;
                const request = await axios.get('api/users', {params: {...params}})
                this.query = request.data;
                this.loading = false;

            },
            getFilteredTags(text) {
                this.filteredRoles = this.allRoles
                    .filter((role) => {
                        return role.name
                            .toString()
                            .toLowerCase()
                            .indexOf(text.toLowerCase()) >= 0
                    })
                console.log(this.filteredRoles);
            }
            /*  async changedPage(page = 1) {
                  const urlParams = new URLSearchParams(document.location.search.substring(1));
                  const entries = urlParams.entries();
                  const params = this.paramsToObject(entries);

                  if (Number.isInteger(page)) {
                      params.page = page
                  } else {
                      Object.assign( params,page)
                  }
                  for(const param in params){
                      urlParams.set(param,params[param])
                  }
                  this.loading = true
                  const url = `${window.location.pathname}?${urlParams.toString()}`
                  window.location.href=url
              },*/
        },
        watch:{
            selectedRoles(){
                this.loadAsyncData()
            }
        }
    }
</script>
