<template>
  <div class="calendar">
    <div class="calendar__header">
      <div class="calendar__header__name">{{data.month_name}}</div>
    </div>
    <table cellpadding="0" cellspacing="0" class="calendar__tb">
      <tr class="calendar__row">
        <th v-for="(heading, index) in headings" class="calendar__head" :class="{ 'calendar__weekend' : index > 4}">
          <div class="calendar__number">{{heading}}</div>
        </th>
      </tr>
      <tr class="calendar__row" v-for="week in data.list">
        <template v-for="day in week">
          <td class="calendar__np" v-if="day.day === 0"></td>
          <td class="calendar__day" :title="'Points: '+day.points" :class="[{ 'calendar__day--sucess' : data.average != 0 && day.points > data.average}, {'calendar__day--today' : day.day == data.today}]" v-else>
            <div class="calendar__number">{{day.day}}</div>
          </td>
        </template>
      </tr>
    </table>
  </div>
</template>

<script>
  import axios from 'axios';
  
  export default {
    data () {
      return {
        headings: ['M','T','W','T','F','S','S'],
        data: []
      }
    },
    methods: {
      calendarUpdate(){
        axios.get('/api/getcalendar')
        .then((responce) => {
          this.data = responce.data;
        });
      }
    },
    mounted() {
      this.calendarUpdate();
    }
  }
</script>
<style lang="scss">
  @import '../../../sass/_variables.scss';

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
  .calendar__head, .calendar__day {
    text-align: center;
  }
  .calendar__number {
    line-height: 25px;
  }
  .calendar__day {
    padding: 2px 2px;

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
      background: #c9ffbe;
    }
  }
  .calendar__day--today {
    .calendar__number {
      background: $main;
    }
  }
  .calendar__header__name {
    display: none;
  }
</style>
