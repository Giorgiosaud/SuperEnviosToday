<script>
export default {
  name: 'RegisterForm',
  data: () => ({
    idnType: '',
    idn: '',
    name: '',
    lastName: '',
    email: '',
    phone: '',
    address: '',
    password: '',
    confirmation: '',
  }),
  mounted() {
    this.idn = this.$refs.idn.value;
    this.idnType = this.$refs.idn_type.value;
    this.name = this.$refs.name.value;
    this.lastName = this.$refs.lastName.value;
    this.email = this.$refs.email.value;
    this.password = this.$refs.password.value;
    this.phone = this.$refs.phone.value;
    this.address = this.$refs.address.value;
    if ([this.$refs.idn.value,
      this.$refs.idn_type.value,
      this.$refs.name.value,
      this.$refs.lastName.value,
      this.$refs.email.value,
      this.$refs.password.value].some((el) => el !== '')) {
      this.$nextTick(() => {
        this.$refs.form.validate();
      });
    }
  },
  methods: {
    onSubmit(val) {
      return val;
    },
  },
};
</script>
