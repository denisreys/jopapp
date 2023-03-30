<template>
  <div class="cards">
    <div class="cards__item">
      <div class="cards__item__value">
        <div class="cards__item__value__icon"><i class="fa-light fa-hundred-points"></i></div>
        <div class="cards__item__value__text">{{ cards.points }}</div>
      </div>
      <div class="cards__item__name">Points</div>
      <div class="cards__item__dynamics">
        <div class="cards__item__dynamics__icon">
          <i class="fa-solid" :class="[
            {'fa-down-left': cards.points < lastDateCards.points },
            {'fa-up-right': cards.points > lastDateCards.points},
            {'fa-right': cards.points == lastDateCards.points}
          ]"></i>
        </div>
        <div class="cards__item__dynamics__value">{{ lastDateCards.points }} pt.</div>
        <div class="cards__item__dynamics__percent">{{ dynamicInPercent(cards.points, lastDateCards.points) }}</div>
      </div>
    </div>
    <div class="cards__item">
      <div class="cards__item__value">
        <div class="cards__item__value__icon"><i class="fa-light fa-bolt"></i></div>
        <div class="cards__item__value__text">{{ cards.difficult }}</div>
      </div>
      <div class="cards__item__name">
        <template v-if="cards.difficult >= 1 && cards.difficult < 2">Very eazy</template>
        <template v-else-if="cards.difficult >= 2 && cards.difficult < 3">Eazy</template>
        <template v-else-if="cards.difficult >= 3 && cards.difficult < 4">Normal</template>
        <template v-else-if="cards.difficult >= 4 && cards.difficult < 4.5">Hard</template>
        <template v-else-if="cards.difficult >= 4.5 && cards.difficult <= 5">Very hard</template>
        difficulty
      </div>
      <div class="cards__item__dynamics">
        <div class="cards__item__dynamics__icon">
          <i class="fa-solid" :class="[
            {'fa-down-left': cards.diary_checks < lastDateCards.diary_checks },
            {'fa-up-right': cards.diary_checks > lastDateCards.diary_checks},
            {'fa-right': cards.diary_checks == lastDateCards.diary_checks}
          ]"></i>
        </div>
        <div class="cards__item__dynamics__value">{{ lastDateCards.difficult }}</div>
        <div class="cards__item__dynamics__percent">{{ dynamicInPercent(cards.difficult, lastDateCards.difficult) }}</div>
      </div>
    </div>
    <div class="cards__item">
      <div class="cards__item__value">
        <div class="cards__item__value__icon"><i class="fa-light fa-check-double"></i></div>
        <div class="cards__item__value__text">{{ cards.done+"%" }}</div>
      </div>
      <div class="cards__item__name">Done</div>
      <div class="cards__item__dynamics">
        <div class="cards__item__dynamics__icon">
          <i class="fa-solid" :class="[
            {'fa-down-left': cards.done < lastDateCards.done },
            {'fa-up-right': cards.done > lastDateCards.done},
            {'fa-right': cards.done == lastDateCards.done}
          ]"></i>
        </div>
        <div class="cards__item__dynamics__percent">{{ Math.round(cards.done - lastDateCards.done) + '% this period' }}</div>
      </div>
    </div>
    <div class="cards__item">
      <div class="cards__item__value">
        <div class="cards__item__value__icon"><i class="fa-light fa-calendar-plus"></i></div>
        <div class="cards__item__value__text">{{ cards.diaries }}</div>
      </div>
      <div class="cards__item__name">Diaries</div>
      <div class="cards__item__dynamics">
        <div class="cards__item__dynamics__icon">
          <i class="fa-solid" :class="[
            {'fa-down-left': cards.diaries < lastDateCards.diaries },
            {'fa-up-right': cards.diaries > lastDateCards.diaries},
            {'fa-right': cards.diaries == lastDateCards.diaries}
          ]"></i>
        </div>
        <div class="cards__item__dynamics__value">{{ lastDateCards.diaries }}</div>
        <div class="cards__item__dynamics__percent">{{ dynamicInPercent(cards.diaries, lastDateCards.diaries)  }}</div>
      </div>
    </div>
    
  </div>
</template>

<script>
  export default {
    props: {
      selectedData: Object,
      fullData: Object
    },
    methods: {
      dynamicInPercent(firstValue, secondValue){
        var response = 0;

        if(firstValue && secondValue) 
          response = Math.round(((firstValue/secondValue) * 100 - 100));
        
        response += '% this period';

        return response;
      },
      getCardsInfo(array){
        var response = {
              points: 0,
              diaries: 0,
              diary_checks: 0,
              done: 0,
              difficult: 0
            }, 
            checksCount = 0;
            
        if(array.months){
          Object.entries(array.months).forEach(([key, month]) => {
            Object.entries(month.days).forEach(([key, day]) => {
              if(day.checks) checksCount += day.checks.length;
              if(day.diaries) response['diaries'] += day.diaries.length;
              if(day.diary_checks) response['diary_checks'] += day.diary_checks.length;
            });
          });
        }
        else if(array.days){
          response.points = array.points;
          Object.entries(array.days).forEach(([key, day]) => {
              if(day.checks) checksCount += day.checks.length;
              if(day.diaries) response['diaries'] += day.diaries.length;
              if(day.diary_checks) response['diary_checks'] += day.diary_checks.length;
          });
        }

        if(response['diaries'] || response['diary_checks'])
          response['done'] = Math.round((response['diary_checks'] / response['diaries']) * 100);
        if(checksCount > 0)
          response.difficult = (response.points / checksCount).toFixed(2);

        return response;
      }
    },
    computed: {
      cards() {
        return this.getCardsInfo(this.selectedData);
      },
      lastDateCards() {
        var response = {
              points: 0,
              diaries: 0,
              diary_checks: 0,
              done: 0
            },
            lastDate = {year: this.selectedData.year, month: 0};

        if(this.selectedData.months){
          lastDate.year = (this.selectedData.year - 1);

          if(this.fullData.calendar[lastDate.year])
            response = this.fullData.calendar[lastDate.year];
        }
        else if(this.selectedData.days){
          lastDate.month = (this.selectedData.month - 1);
          
          if(lastDate.month == 0){
            lastDate.year--;
            lastDate.month = 12;
          }
          
          if(lastDate.year in this.fullData.calendar)
            if(lastDate.month in this.fullData.calendar[lastDate.year])
              response = this.fullData.calendar[lastDate.year].months[lastDate.month];
        }

        return this.getCardsInfo(response);
      }
    },
    mounted(){
      this.$root.loading.loaded++;
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';

  .cards {
    width: 100%;
    overflow: hidden;
    margin-bottom: 30px;
  }
  .cards__item {
    width: 25%;
    float: left;
    min-width: 150px;
    padding: 0 30px;
    border-right: solid 1px $border;
    box-sizing: border-box;
  }
  .cards__item:first-child {
    padding-left: 0;
  }
  .cards__item:last-child {
    padding-right: 0;
    border-right: none;
  }
  .cards__item__value__text {
    font-size: 30px;
    width: 100%;
  }
  .cards__item__value__icon {
    float: right;
    line-height: 37px;

    i {
      font-size: 20px;
    }
  }
  .cards__item__name {
    font-size: 16px;
    margin: 5px 0;
  }
  .cards__item__dynamics {
    overflow: hidden;

    div {
      float: left;
    }

  }
  .cards__item__dynamics__icon {
    margin-right: 8px;

    i {
      font-size: 14px;
    }
    .fa-down-left {
      color: $red;
    }
    .fa-up-right {
      color: $green;
    }
    .fa-right {
      color: $light-gray-icon;
    }
  }
  .cards__item__dynamics__value {
    margin-right: 8px;
  }
  .cards__item__dynamics__value, .cards__item__dynamics__percent {
    color: $light-gray;
    font-size: 12px;
    line-height: 22px;
  }
</style>
