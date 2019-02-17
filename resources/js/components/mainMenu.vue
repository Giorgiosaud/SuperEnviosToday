<script>

    export default {
        name: 'mainMenu',
        data() {
            return {
                openMenu: false,
                showProfileSubMenu: false,
                coordinatorProfileSubMenu: false,
                chileanOperatorProfileSubMenu:false,
                venezuelanOperatorProfileSubMenu:false
            }
        },
        computed: {
            sreenWidth() {
                return this.$store.state.globals.screenWidth;
            }
        },
        methods:{
            logout(){
                sessionStorage.clear();
                localStorage.clear();
                axios.post('logout').then(()=>{
                    window.location.href="/";

                });
            },
            clickOutside(){
                this.showProfileSubMenu=false;
                this.coordinatorProfileSubMenu=false;
            }
        },
        mounted() {
            this.openMenu = window.innerWidth > 768;
            window.addEventListener("resize", _.debounce(() => {
                this.openMenu = window.innerWidth > 768;
            }, 300));
        },
    }
</script>
