<script>
export default {
  name: 'UserDetail',
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    allRoles: {
      type: Array,
      default: () => ([]),
    },
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
      filteredRoles: [],
      sendingVerification: false,
    }
  ),
  computed: {
    roles() {
      return this.allRoles.map((role) => role.name);
    },
    roleNames() {
      if (this.userData.roles.length) {
        return this.userData.roles.map((role) => role.name_id);
      }
      return null;
    },

  },
  created() {
    this.userData = this.user;
    this.filteredRoles = this.allRoles;
  },
  mounted() {

  },
  methods: {
    loginAs() {
      window.location.href = `/users/login-as/${this.user.id}`;
    },
    async resendVerification() {
      this.sendingVerification = true;
      await $http.post('/api/user/verify_email', {
        id: this.user.id,
      });
      this.sendingVerification = false;
    },
    cancel() {
      window.location.reload();
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
