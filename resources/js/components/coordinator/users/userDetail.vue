<script>
import currencyFilter from '../../../currency';

export default {
  name: 'UserDetail',
  filters: {
    currencyFilter
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    accounts: {
      type: Array,
      default: () => ([]),
    },
    allRoles: {
      type: Array,
      default: () => ([]),
    },
  },
  data: () => (
    {
      isToggling: false,
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
      unlinkingAccount:false,
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
    async toggleOperatorState(accountId) {
      try {
        this.isToggling = true
        await $http.patch(`/api/account/${accountId}/toggle-operator-state`)
      } finally {
        window.location.reload()
        console.log('asd')
      }
    },
    async unlinkAccount(userId,accountId) {
      this.unlinkingAccount = true;
      try {
        await $http.patch(`/api/account/${accountId}/user/${userId}/unlink`);
      } catch (error) {
        console.log(error);
      } finally {
        window.location.reload()
      }
    },
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
