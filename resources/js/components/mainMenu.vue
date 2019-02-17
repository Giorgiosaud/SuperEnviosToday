<script>

    export default {
        name: 'mainMenu',
        data() {
            return {
                openMenu: false,
                showProfileSubMenu: false,
                coordinatorSubMenu: false,
                chileanOperatorSubMenu:false,
                venezuelanOperatorSubMenu:false
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
            tooggleSubMenuProfile(){
                this.showProfileSubMenu= !this.showProfileSubMenu;
                this.coordinatorSubMenu= false;
                this.chileanOperatorSubMenu=false;
                this.venezuelanOperatorSubMenu=false;
            },
            tooggleSubMenuCoordinador(){
                this.showProfileSubMenu=false;
                this.coordinatorSubMenu= !this.coordinatorSubMenu;
                this.chileanOperatorSubMenu=false;
                this.venezuelanOperatorSubMenu=false;
            },
            tooggleSubMenuChileanOperator(){
                this.showProfileSubMenu=false;
                this.coordinatorSubMenu= false;
                this.chileanOperatorSubMenu= !this.chileanOperatorSubMenu;
                this.venezuelanOperatorSubMenu=false;
            },
            tooggleSubMenuVenezuelanOperator(){
                this.showProfileSubMenu=false;
                this.coordinatorSubMenu= false;
                this.chileanOperatorSubMenu=false;
                this.venezuelanOperatorSubMenu= !this.venezuelanOperatorSubMenu;
            },
            clickOutside(){

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
