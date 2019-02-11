<script>
export default {

  name: 'mainMenu',
  data () {
    return {
      openMenu:false,
      showProfileSubMenu:false,
      coordinatorProfileSubMenu:false

    }
  },
  computed:{
    sreenWidth(){
      return this.$store.state.globals.screenWidth;
    }
  },
  mounted(){
      this.openMenu= window.innerWidth>768;
      window.addEventListener("resize", _.debounce(()=> {
      this.openMenu= window.innerWidth>768;
    },300));
  }
}
</script>

<style lang="stylus" scoped>
</style>
