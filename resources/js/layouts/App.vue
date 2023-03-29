<template>
    <router-view></router-view>
    <loader 
      v-if="loading.components > loading.loaded"
      :loading="loading" 
    />
</template>
<script>
    import loader from '../components/blocks/Loader.vue';
    export default {
      data(){
        return {
            loading: {
                components: 1,
                lastKey: 1000,
                loaded: 0
            },
        }
      },
      components: { loader },
      watch: {
        $route (){
            this.loading.loaded = 0;
        }
      },
      name: 'Dashboard',
    }
</script>