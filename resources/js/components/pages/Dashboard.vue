<template>
    <div class="container container--dashboard">
      <div class="sb sb--r">
        <tasks/>
        <notes/>
      </div>
      <div class="sb sb--l">
        <stats :statsIsUpdate="statsIsUpdate"/>
        <calendar />
        <navigation/>
      </div>
      <div class="main">
        <diary/>
        <regular/>
      </div>
    </div>
  </template>
<script>
    import calendar from '../blocks/Dashboard/DashboardCalendar.vue';
    import stats from '../blocks/Dashboard/Stats.vue';
    import tasks from '../blocks/Dashboard/Tasks.vue';
    import regular from '../blocks/Dashboard/Regular.vue';
    import notes from '../blocks/Dashboard/Notes.vue';
    import diary from '../blocks/Dashboard/Diary.vue';
    import navigation from '../blocks/Navigation.vue';

    export default {
      props: {
        loaded: Number
      },
      components: {
        calendar, stats, tasks, regular, notes, diary, navigation
      },
      data(){
        return {
          user: null,
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