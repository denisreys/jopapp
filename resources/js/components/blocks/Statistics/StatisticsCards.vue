<template>
  <div class="cards">
    <div class="cards__item">
      <div class="cards__item__value">
        <div class="cards__item__value__icon"><i class="fa-light fa-hundred-points"></i></div>
        <div class="cards__item__value__text">{{ cards.points }}</div>
      </div>
      <div class="cards__item__name">Points by date</div>
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
    <div class="cards__item">
      <div class="cards__item__value">
        <div class="cards__item__value__icon"><i class="fa-light fa-calendar-check"></i></div>
        <div class="cards__item__value__text">{{ cards.diary_checks }}</div>
      </div>
      <div class="cards__item__name">Diary checks</div>
      <div class="cards__item__dynamics">
        <div class="cards__item__dynamics__icon">
          <i class="fa-solid" :class="[
            {'fa-down-left': cards.diary_checks < lastDateCards.diary_checks },
            {'fa-up-right': cards.diary_checks > lastDateCards.diary_checks},
            {'fa-right': cards.diary_checks == lastDateCards.diary_checks}
          ]"></i>
        </div>
        <div class="cards__item__dynamics__value">{{ lastDateCards.diary_checks }}</div>
        <div class="cards__item__dynamics__percent">{{ dynamicInPercent(cards.diary_checks, lastDateCards.diary_checks) }}</div>
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
    data () {
      return {
        cards: {
          points: 0,
          done: 0,
          diaries: 0,
          diary_checks: 0
        },
        lastDateCards: {
          points: 0,
          done: 0,
          diaries: 0,
          diary_checks: 0
        }
      }
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
          points: array.points,
          diaries: 0,
          diary_checks: 0,
          done: 0
        }

        if(array.months){
          Object.entries(array.months).forEach(([key, month]) => {
            Object.entries(month.days).forEach(([key, day]) => {
              if(day.diaries) response['diaries'] += day.diaries.length;
              if(day.diary_checks) response['diary_checks'] += day.diary_checks.length;
            });
          });
        }
        else if(array.days){
          Object.entries(array.days).forEach(([key, day]) => {
              if(day.diaries) response['diaries'] += day.diaries.length;
              if(day.diary_checks) response['diary_checks'] += day.diary_checks.length;
          });
        }

        if(response['diaries'] || response['diary_checks'])
          response['done'] = Math.round((response['diary_checks'] / response['diaries']) * 100);

        return response;
      }
    },
    computed: {
      cards: function () {
        return this.getCardsInfo(this.selectedData);
      },
      lastDateCards: function () {
        var lastData = [];
        
        if(this.selectedData.months){
          var lastYear = this.selectedData.months[1].year - 1;

          if(this.fullData.calendar[lastYear])
            lastData = this.fullData.calendar[lastYear];
        }
        else if(this.selectedData.days){
          var lastMonth = this.selectedData.month - 1,
              thisYear = this.selectedData.year;

          if(lastMonth == 0){
            var lastYear = thisYear - 1;
            lastMonth = 12;
            
            if(this.fullData.calendar[lastYear])
              lastData = this.fullData.calendar[lastYear].months[lastMonth];
          }
          else lastData = this.fullData.calendar[thisYear]['months'][lastMonth];
        }

        return this.getCardsInfo(lastData);
      }
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
