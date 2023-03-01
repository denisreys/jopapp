<template>
    <div class="container container--dashboard">
      
      <div class="sb sb--r">
        <tasks/>
        <notes/>
      </div>
      <div class="sb sb--l">
        <stats :statsIsUpdate="statsIsUpdate"/>
        <calendar/>
        <navigation/>
      </div>
      <div class="main">
        <diary/>
        <regular/>
      </div>
    </div>
  </template>
<script>
    import axios from 'axios';
    import calendar from '../blocks/Dashboard/DashboardCalendar.vue';
    import stats from '../blocks/Dashboard/Stats.vue';
    import tasks from '../blocks/Dashboard/Tasks.vue';
    import regular from '../blocks/Dashboard/Regular.vue';
    import notes from '../blocks/Dashboard/Notes.vue';
    import diary from '../blocks/Dashboard/Diary.vue';
    import navigation from '../blocks/Navigation.vue';

    export default {
      components: {
        calendar, stats, tasks, regular, notes, diary, navigation
      },
      data(){
        return {
          user: null,
          statsIsUpdate: 0
        }
      },
      methods: {
        updateStats(){
          this.statsIsUpdate++;
        }
      },
      mounted(){
        axios.get('/user').then((res)=>{
          this.user = res.data;
        });
      },
      name: 'Dashboard'
    }
</script>