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
        <div class="cards__item__dynamics__percent">{{ dynamicInPercent(cards.points, lastDateCards.points) }}</div>
      </div>
    </div>
    <div class="cards__item">
      <div class="cards__item__value">
        <div class="cards__item__value__icon"><i class="fa-light fa-bolt"></i></div>
        <div class="cards__item__value__text">{{ cards.difficult }}</div>
      </div>
      <div class="cards__item__name">
        <template v-if="cards.difficult == 0">No</template>
        <template v-else-if="cards.difficult >= 1 && cards.difficult < 1.5">Very eazy</template>
        <template v-else-if="cards.difficult >= 1.5 && cards.difficult < 2.5">Eazy</template>
        <template v-else-if="cards.difficult >= 2.5 && cards.difficult < 3.5">Normal</template>
        <template v-else-if="cards.difficult >= 3.5 && cards.difficult < 4.5">Hard</template>
        <template v-else-if="cards.difficult >= 4.5 && cards.difficult <= 50">Very hard</template>
        difficulty
      </div>
      <div class="cards__item__dynamics">
        <div class="cards__item__dynamics__icon">
          <i class="fa-solid" :class="[
            {'fa-down-left': cards.difficult < lastDateCards.difficult },
            {'fa-up-right': cards.difficult > lastDateCards.difficult},
            {'fa-right': cards.difficult == lastDateCards.difficult}
          ]"></i>
        </div>
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
        <div class="cards__item__dynamics__percent">
          <template v-if="cards.done == lastDateCards.done">the same</template>
          <template v-else>{{ Math.round(cards.done - lastDateCards.done) + '%' }}</template>
        </div>
      </div>
    </div>
    <div class="cards__item">
      <div class="cards__item__value">
        <div class="cards__item__value__icon"><i class="fa-light fa-calendar-plus"></i></div>
        <div class="cards__item__value__text">{{ cards.todoes }}</div>
      </div>
      <div class="cards__item__name">Todoes</div>
      <div class="cards__item__dynamics">
        <div class="cards__item__dynamics__icon">
          <i class="fa-solid" :class="[
            {'fa-down-left': cards.todoes < lastDateCards.todoes },
            {'fa-up-right': cards.todoes > lastDateCards.todoes},
            {'fa-right': cards.todoes == lastDateCards.todoes}
          ]"></i>
        </div>
        <div class="cards__item__dynamics__percent">{{ dynamicInPercent(cards.todoes, lastDateCards.todoes) }}</div>
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
      dynamicInPercent(firstValue, lastValue){
        var response = '';

        if(firstValue == lastValue)
          response = 'the same';
        else if(!firstValue || !lastValue){
          response = firstValue - lastValue;

          if(response > 0) response = '+' + response;

          response += ' units';
        }
        else {
          response = Math.round((firstValue/lastValue) * 100 - 100);

          if(response > 0) response = '+' + response;
          
          response += '%';
        }
        
        return response;
      },
      getCardsInfo(array){
        var response = {
              points: array.points,
              todoes: 0,
              todoes_checks: 0,
              done: 0,
              difficult: 0
            }, 
            checksCount = 0;
            
        if(array.months){
          Object.entries(array.months).forEach(([key, month]) => {
            Object.entries(month.days).forEach(([key, day]) => {
              if(day.checks) checksCount += day.checks.length;
              if(day.todoes) response['todoes'] += day.todoes.length;
              if(day.todoes_checks) response['todoes_checks'] += day.todoes_checks.length;
            });
          });
        }
        else if(array.days){
          Object.entries(array.days).forEach(([key, day]) => {
              if(day.checks) checksCount += day.checks.length;
              if(day.todoes) response['todoes'] += day.todoes.length;
              if(day.todoes_checks) response['todoes_checks'] += day.todoes_checks.length;
          });
        }

        if(response['todoes'] || response['todoes_checks'])
          response['done'] = Math.round((response['todoes_checks'] / response['todoes']) * 100);
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
              todoes: 0,
              todoes_checks: 0,
              done: 0,
              difficult: 0
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
            if(lastDate.month in this.fullData.calendar[lastDate.year].months)
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