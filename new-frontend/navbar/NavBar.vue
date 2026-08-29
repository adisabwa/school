<!-- vue-apps/shared/components/TheNavbar.vue -->
<template>
  <div class="fixed w-screen h-fit">
    <component :is="Component"/>
  </div>
</template>

<script>
  import { useDataStore } from '@2/shared/config/stores/dataStore';
  import { useAuthStore } from '@2/shared/config/stores/authStore';
  import pinia from '@2/shared/config/stores/index';
  import MainLayout from '@navbar/pages/MainLayout.vue';
  import PublicLayout from '@navbar/pages/PublicLayout.vue';
  import BlankLayout from '@navbar/pages/BlankLayout.vue';
  import { mapState } from 'pinia';
    
  export default {
    components:{
      MainLayout,
      PublicLayout,
      BlankLayout,
    },
    computed:{
      ...mapState(useDataStore,{
        template: 'template',
        layout: 'layout',
      }),
      ...mapState(useAuthStore,{
        route: 'route',
      }),
      Component(){
        console.log('Template', this.template)
        if (this.template == 'main' )
          return 'MainLayout'
        else if (this.template == 'public' )
          return 'PublicLayout'
        else
          return 'BlankLayout'
      },
    },
    watch: {
      layout(val){
        console.log('Layout', val)
      }
    },
    data() {
      return {
        currentLayout: '',
        currentTemplate:'',
      }
    },
    mounted(){
      let data = useDataStore()
      // window.addEventListener('layout-changed', (e) => {
      //   // Update state Navbar secara manual agar sinkron
      //   console.log(e)
      //   if (e.detail.template)
      //     data.template = e.detail.template;
      //   if (e.detail.layout)
      //     data.layout = e.detail.layout;
      // });
    },
    created() {
      const store = useDataStore(pinia);
      this.currentLayout = store.layout;
      this.currentTemplate = store.template;
      console.log(this.currentLayout, this.currentTemplate)
      // Subscribe mendengarkan setiap perubahan yang terjadi di store ini
      // dari manapun asalnya (termasuk dari Main App Instance)
      store.$subscribe((mutation, state) => {
        console.log('Store berubah dari instance lain:', state.layout);
        this.$forceUpdate(); // Memaksa Navbar render ulang jika reaktivitas terputus
      });
    }
  }
</script>