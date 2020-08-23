<script>
export default {
  name: 'LoginForm',
  data: () => ({
    idnType: '',
    idn: '',
    name: '',
    lastName: '',
    email: '',
    password: '',
    confirmation: '',
  }),
  mounted() {
    this.idn = this.$refs.idn.value;
    this.idnType = this.$refs.idn_type.value;
    if ([this.$refs.idn.value,
      this.$refs.idn_type.value].some((el) => el !== '')) {
      this.$nextTick(() => {
        this.$refs.form.validate();
      });
    }
  },
  methods: {
    onSubmit(val) {
      console.log(val);
    },
  },
};
</script>
