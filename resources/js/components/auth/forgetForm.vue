<script>
export default {
  name: 'RegisterForm',
  data: () => ({
    idnType: '',
    idn: '',
    email: '',
  }),
  mounted() {
    this.idn = this.$refs.idn.value;
    this.idnType = this.$refs.idn_type.value;
    this.email = this.$refs.email.value;
    if ([this.$refs.idn.value,
      this.$refs.idn_type.value,
      this.$refs.email.value,
    ].some((el) => el !== '')) {
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
