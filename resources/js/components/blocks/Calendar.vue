<template>
  <div class="calendar">
    <div class="calendar__header">
      <div class="calendar__actions">
        <div class="calendar__actions__arrow" :class="{'calendar_actions__arrow--disable' : !calendar.canISwitchLast}" @click="calendarSwitch('last')"><i class="fal fa-angle-left"></i></div>
        <div class="calendar__actions__arrow" :class="{'calendar_actions__arrow--disable' : !calendar.canISwitchNext}" @click="calendarSwitch('next')"><i class="fal fa-angle-right"></i></div>
      </div>
      <div class="calendar__header__name">
        {{calendar.data.month_name}}
        <template v-if="data.year != calendar.data.year">
          {{ calendar.data.year }}
        </template>
      </div>
    </div>
    <div class="calendar__tb">
      <div class="calendar__tb__head">
        <div v-for="(heading, index) in headings" class="calendar__tb__head__cell calendar__cell" :class="{ 'calendar__weekend' : index > 4}">
          <div class="calendar__number">{{heading}}</div>
        </div>
      </div>
      <div class="calendar__tb__main">
        <div class="calendar__np calendar__cell" v-if="calendar.data.offset > 0" v-for="index in calendar.data.offset" :key="index"></div>
        <template v-for="day in calendar.data.days">
          <div class="calendar__day calendar__cell" 
              :title="'Points: '+day.points" 
              :class="[
                {'calendar__day--sucess' : day.points > 0 && day.day < data.today}, 
                {'calendar__day--today' : this.calendar.data.year == data.year && 
                                          this.calendar.data.month == data.month && 
                                          day.day == data.today
                                        }
              ]">
            <div class="calendar__number">{{day.day}}</div>
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
        axios.get('/api/getcalendar')
        .then((response) => {
          console.log(response);
          this.data = response.data;
          this.calendar.data = this.data.calendar[this.data.year][this.data.month];
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

        this.calendar.data = this.data.calendar[newYear][newMonth];
        this.canISwitch();
      },
      canISwitch(){
        var newNextYear = Number(this.calendar.data.year), 
            newNextMonth = Number(this.calendar.data.month),
            newLastYear = Number(this.calendar.data.year), 
            newLastMonth = Number(this.calendar.data.month);

        if(this.calendar.data.month < 12) {
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
          if(newNextMonth in this.data.calendar[newNextYear])
            this.calendar.canISwitchNext = true;
        if(newLastYear in this.data.calendar)
          if(newLastMonth in this.data.calendar[newLastYear])
            this.calendar.canISwitchLast = true;
      }
    },
    mounted() {
      this.calendarUpdate();
    }
  }
</script>
<style lang="scss">
  @import '../../../sass/_variables.scss';
  .calendar__header {
    position: relative;
    margin-bottom: 10px;
  }
  .calendar__header__name {
    width: 100%;
    text-align: center;
  }
  .calendar__actions {
    position: absolute;
    width: 100%;
  }
  .calendar__actions__arrow {
    float:right;
    cursor: pointer;
  }
  .calendar__actions__arrow:hover {
    color: #000;
  }
  .calendar__actions__arrow:first-child {
    float:left;
  }
  .calendar_actions__arrow--disable {
    opacity: 0.5;
  }
  .calendar {
    .calendar__tb {
      width: 100%;
    }
    .calendar__row {
      &:first-child th {
        padding-bottom: 5px;
      }
    }
  }
  .calendar__tb__head {
    background-color: $main;
  }
  .calendar__tb__main {
    margin-top: 5px;
  }
  .calendar__tb__head, .calendar__tb__main {
    display: flex;
    max-width: 100%;
    flex-wrap: wrap;
  }
  .calendar__cell {
    text-align: center;
    width: calc(100% / 7);
    padding: 2px 2px;
    box-sizing: border-box;
  }
  .calendar__number {
    line-height: 25px;
  }
  .calendar__day {
    .calendar__number {
      border-radius: 50%;
    }
  }
  .calendar__head {
    .calendar__number {
      background-color: $main;
      font-weight: normal;
    }
  }
  .calendar__day--sucess {
    .calendar__number {
      background: #c9ffbed4;
    }
  }
  .calendar__day--today {
    .calendar__number {
      background: $main;
    }
  }
  
</style>
