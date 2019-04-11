<script>
import { debounce } from 'lodash';

export default {
  name: 'MainMenu',
  data() {
    return {
      openMenu: false,
      showProfileSubMenu: false,
      coordinatorSubMenu: false,
      chileanOperatorSubMenu: false,
      venezuelanOperatorSubMenu: false,
    };
  },
  computed: {
    screenWidth() {
      return this.$store.state.globals.screenWidth;
    },
  },
  mounted() {
    this.openMenu = window.innerWidth > 768;
    window.addEventListener('resize', debounce(() => {
      this.openMenu = window.innerWidth > 768;
    }, 300));
  },
  methods: {
    logout() {
      sessionStorage.clear();
      localStorage.clear();
      axios.post('logout').then(() => {
        window.location.href = '/';
      });
    },
    toggleSubMenuProfile() {
      this.showProfileSubMenu = !this.showProfileSubMenu;
      this.coordinatorSubMenu = false;
      this.chileanOperatorSubMenu = false;
      this.venezuelanOperatorSubMenu = false;
    },
    toggleSubMenuCoordinator() {
      this.showProfileSubMenu = false;
      this.coordinatorSubMenu = !this.coordinatorSubMenu;
      this.chileanOperatorSubMenu = false;
      this.venezuelanOperatorSubMenu = false;
    },
    toggleSubMenuChileanOperator() {
      this.showProfileSubMenu = false;
      this.coordinatorSubMenu = false;
      this.chileanOperatorSubMenu = !this.chileanOperatorSubMenu;
      this.venezuelanOperatorSubMenu = false;
    },
    toggleSubMenuVenezuelanOperator() {
      this.showProfileSubMenu = false;
      this.coordinatorSubMenu = false;
      this.chileanOperatorSubMenu = false;
      this.venezuelanOperatorSubMenu = !this.venezuelanOperatorSubMenu;
    },
    clickOutside() {

    },
  },
};
</script>
