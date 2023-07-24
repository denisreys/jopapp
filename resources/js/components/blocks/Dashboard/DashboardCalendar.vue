<template>
  <div class="calendar calendar--dashboard" >
    <div class="calendar__header">
      <div class="calendar__actions">
        <div class="calendar__actions__arrow" :class="{'calendar_actions__arrow--disable' : !calendar.canISwitchLast}" @click="calendarSwitch('last')"><i class="fal fa-angle-left"></i></div>
        <div class="calendar__actions__arrow" :class="{'calendar_actions__arrow--disable' : !calendar.canISwitchNext}" @click="calendarSwitch('next')"><i class="fal fa-angle-right"></i></div>
      </div>
      <div class="calendar__header__value">
        {{calendar.data.month_name}}
        <template v-if="data.year != calendar.data.year">
          {{ calendar.data.year }}
        </template>
      </div>
    </div>
    <div class="calendar__tb">
      <div class="calendar__tb__head">
        <div v-for="(heading, index) in headings" class="calendar__tb__head__cell calendar__cell" :class="{ 'calendar__weekend' : index > 4}">
          <div class="calendar__cell__value">{{heading}}</div>
        </div>
      </div>
      <div class="calendar__tb__main">
        <div class="calendar__cell" v-if="calendar.data.offset > 0" v-for="index in calendar.data.offset" :key="index"></div>
        <template v-for="day in calendar.data.days">
          <div class="calendar__item calendar__cell" 
              :title="'Points: '+day.points" 
              :class="[
                {'calendar__cell--sucess' : day.points > 0}, 
                {'calendar__cell--now' : this.calendar.data.year == data.year && 
                                          this.calendar.data.month == data.month && 
                                          day.day == data.today
                                        }
              ]">
            <div class="calendar__cell__value">{{day.day}}</div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  
  export default {
    data () {
      return {
        headings: ['M','T','W','T','F','S','S'],
        data: [],
        calendar: {
          canISwitchNext: '',
          canISwitchLast: '',
          data: []
        }
      }
    },
    methods: {
      calendarUpdate(){
        axios.post('/getstatistics', {page: 'dashboard'})
        .then((response) => {
          this.$root.loading.loaded++;
          this.data = response.data;
          this.calendar.data = this.data.calendar[this.data.year]['months'][this.data.month];
          this.canISwitch();
        });
      },
      calendarSwitch(action){
        var newYear = Number(this.calendar.data.year), newMonth = Number(this.calendar.data.month);

        if(action === 'next'){
          if(!this.calendar.canISwitchNext) return;

          if(this.calendar.data.month < 12) {
            newMonth++;
          }  
          else {
            newMonth = 1;
            newYear++;
          }
        }
        else if(action === 'last'){
          if(!this.calendar.canISwitchLast) return;

          if(this.calendar.data.month > 1){
            newMonth--;
          }  
          else {
            newMonth = 12;
            newYear--;
          }
        }

        this.calendar.data = this.data.calendar[newYear]['months'][newMonth];
        this.canISwitch();
      },
      canISwitch(){
        var newNextYear = Number(this.calendar.data.year), 
            newNextMonth = Number(this.calendar.data.month),
            newLastYear = Number(this.calendar.data.year), 
            newLastMonth = Number(this.calendar.data.month);

        if(this.calendar.data.month < 12){
            newNextMonth++;
        }  
        else {
          newNextMonth = 1;
          newNextYear++;
        }
        
        if(this.calendar.data.month > 1){
            newLastMonth--;
          }  
          else {
            newLastMonth = 12;
            newLastYear--;
        }

        this.calendar.canISwitchNext = false;
        this.calendar.canISwitchLast = false;

        if(newNextYear in this.data.calendar)
          if(newNextMonth in this.data.calendar[newNextYear]['months'])
            this.calendar.canISwitchNext = true;
        if(newLastYear in this.data.calendar)
          if(newLastMonth in this.data.calendar[newLastYear]['months'])
            this.calendar.canISwitchLast = true;
      }
    },
    created() {
      this.calendarUpdate();
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';

  .calendar--dashboard {
    .calendar__tb__head {
      border-radius: 2px;
      background-color: $main;
    }
    .calendar__cell {
      width: calc(100% / 7);
      padding: 4px;
    }
    .calendar__head {
      .calendar__cell__value {
        background-color: $main;
      }
    }
    .calendar__tb__main {
      margin-top: 5px;
    }
  }
</style>
