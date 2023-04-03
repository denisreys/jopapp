<template>
    <div class="container container--dashboard">
      <div class="sb sb--r">
        <targets/>
        <notes/>
      </div>
      <div class="sb sb--l">
        <shortstats :statsIsUpdate="statsIsUpdate"/>
        <calendar />
        <navigation/>
      </div>
      <div class="main">
        <todoes/>
        <groups/>
      </div>
    </div>
  </template>
<script>
    import calendar from '../blocks/Dashboard/DashboardCalendar.vue';
    import shortstats from '../blocks/Dashboard/ShortStats.vue';
    import targets from '../blocks/Dashboard/Targets.vue';
    import groups from '../blocks/Dashboard/Groups.vue';
    import notes from '../blocks/Dashboard/Notes.vue';
    import todoes from '../blocks/Dashboard/Todoes.vue';
    import navigation from '../blocks/Navigation.vue';

    export default {
      components: {
        calendar, shortstats, targets, groups, notes, todoes, navigation
      },
      data(){
        return {
          statsIsUpdate: 0,
          components: 6,
        }
      },
      methods: {
        updateStats(){
          this.statsIsUpdate++;
        }
      },
      mounted(){
        this.$root.loading.components = this.components;
      },
      name: 'Dashboard',
    }
</script>
<style lang="scss">
    @import './resources/sass/_variables.scss';

</style>