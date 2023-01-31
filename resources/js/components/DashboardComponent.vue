<template>
    <div class="container container--dashboard">
      <div class="sb sb--l">
        <total :updateTotal="updateTotal"/>
        <calendar/>
          <a href="#" @click.prevent='logout'>Logout</a>
      </div>
      <div class="sb sb--r">
        <tasks/>
        <notes/>
      </div>
      <div class="main">
        <diary/>
        <regular/>
      </div>
    </div>
  </template>
<script>
    import axios from 'axios';
    import calendar from './blocks/Calendar.vue';
    import total from './blocks/Total.vue';
    import tasks from './blocks/Tasks.vue';
    import regular from './blocks/Regular.vue';
    import notes from './blocks/Notes.vue';
    import diary from './blocks/Diary.vue';

    export default {
      components: {
        calendar, total, tasks, regular, notes, diary
      },
      data(){
        return {
          user: null,
          updateTotal: 0
        }
      },
      methods: {
        logout(){
            localStorage.removeItem('token');
            this.$router.push('/login')
        },
        updateStat(){
          this.updateTotal++;
        }
      },
      mounted(){
        axios.get('/api/user').then((res)=>{
          this.user = res.data;
        });
      },
      name: 'DashboardComponent'
    }
</script>