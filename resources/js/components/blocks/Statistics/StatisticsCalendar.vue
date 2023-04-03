<template>
  <div class="calendar calendar--statistic">
    <div class="calendar__header">
      <div class="calendar__actions">
        <div class="calendar__actions__arrow" :class="{'calendar_actions__arrow--disable' : !canISwitchYearLast}" @click="changeDate(selectedDate.year - 1, 0)"><i class="fal fa-angle-left"></i></div>
        <div class="calendar__actions__arrow" :class="{'calendar_actions__arrow--disable' : !canISwitchYearNext}" @click="changeDate((selectedDate.year + 1), 0)"><i class="fal fa-angle-right"></i></div>
      </div>
      <div class="calendar__header__value">
        {{selectedDate.year}}
      </div>
    </div>
    <div class="calendar__tb">
      <div class="calendar__tb__main">
        <template v-for="month in thisData.months">
          <div class="calendar__cell" 
              :class="[
                {'calendar__cell--now' : this.selectedDate.year == month.year && 
                                          this.selectedDate.month == month.month},
                {'calendar__cell--disable': month.points == 0}
              ]" @click="changeDate(month.year, month.month)">
            <div class="calendar__cell__value">{{month.month_name_short}}</div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
  
  export default {
    props: {
      data: Object
    },
    data () {
      return {
        selectedDate: {
          year: Number(this.data.year),
          month: Number(this.data.month)
        },
        thisData: this.data.calendar[this.data.year],
        canISwitchYearNext: Boolean,
        canISwitchYearLast: Boolean
      }
    },
    methods: {
      changeDate(year, month){
        if(this.selectedDate.year == year && this.selectedDate.month == month){
          this.selectedDate.month = 0;
        }
        else if(this.selectedDate.year != year){
          if(this.selectedDate.year < year && !this.canISwitchYearNext) return;
          if(this.selectedDate.year > year && !this.canISwitchYearLast) return;

          this.selectedDate.year = Number(year);
          this.selectedDate.month = Number(month);
          this.thisData = this.data.calendar[year];
        }
        else {
          this.selectedDate.year = Number(year);
          this.selectedDate.month = Number(month);
        }

        this.$parent.updateSelectedDate(this.selectedDate.year, this.selectedDate.month);
        this.canISwitchYear();
      },
      canISwitchYear(){
        if((this.selectedDate.year + 1) in this.data.calendar) 
          this.canISwitchYearNext = true;
        else 
          this.canISwitchYearNext = false;

        if((this.selectedDate.year - 1) in this.data.calendar) 
          this.canISwitchYearLast = true;
        else 
          this.canISwitchYearLast = false;  
      }
    },
    created(){
      this.canISwitchYear();
    },
    mounted(){
      this.$root.loading.loaded++;
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';

  .calendar--statistic {
    .calendar__header {
      position: relative;
      margin-bottom: 10px;
      min-height: 22px;
    }
    .calendar__header__value {
      font-size: 20px;
    }
    .calendar__cell {
      width: calc(100% / 3);
      padding: 7px;
      cursor: pointer;
    }
    .calendar__cell--disable {
      color: $light-gray;
    }
    .calendar__cell--now {
      background-color: $main;
      color: $black;
    }
    .calendar__tb__main {
      margin-top: 10px;
    }
  }
</style>
