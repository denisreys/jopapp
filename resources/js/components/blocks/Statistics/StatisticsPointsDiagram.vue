<template>
  <div class="main__header">
    <div class="main__header__title">Regular affairs</div>
  </div>
  <div class="chart chart--points">
    <div class="chart__scale">
      <div class="chart__scale__item" v-for="item in chart.scale">
        <div class="chart__scale__item__value">{{ item }}</div>
      </div>
    </div>
    <div class="chart__list">
        <div class="chart__item" v-if="selectedData.days" v-for="day in selectedData.days" :style="{width: (100 / selectedData.days.length) +'%'}">
          <div class="chart__item__line">
            <div class="chart__item__line__paint" :style="{height: ((day.points/chart.biggestValue) * 100)+'%'}" :title="day.points+' Points'"></div>
          </div>
          <div class="chart__item__date">{{ day.day }}</div>
        </div>
        <div class="chart__item" v-if="selectedData.months" v-for="month in selectedData.months">
          <div class="chart__item__line">
            <div class="chart__item__line__paint" :style="{height: ((month.points/chart.biggestValue) * 100)+'%'}" :title="month.points+' Points'"></div>
          </div>
          <div class="chart__item__date">{{ month.month_name_short }}</div>
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
        chart: {
          biggestValue: Number,
          countItems: Number,
          scale: Object
        }
      }
    },
    methods: {
      
    },
    computed: {
      chart: function(){
        var response = {biggestValue: 0, countItems: 0, scale: []};

        if(this.selectedData.days){
          response.countItems = this.selectedData.days.length;
          response.biggestValue = 10;

          Object.entries(this.selectedData.days).forEach(([key, day]) => {
            if(response.biggestValue < day.points) response.biggestValue = day.points;
          });
          if(response.biggestValue > 10){
            response.biggestValue = Math.ceil(response.biggestValue/10)*10;
          }
        }
        else if(this.selectedData.months){
          response.countItems = 12;
          response.biggestValue = 100;

          Object.entries(this.selectedData.months).forEach(([key, month]) => {
            if(response.biggestValue < month.points) response.biggestValue = month.points;
          });

          if(response.biggestValue > 100)
            response.biggestValue = Math.ceil(response.biggestValue/100)*100;
        }

        for (var i = 0; i < response.biggestValue;i = i + (response.biggestValue / 5)) 
          response.scale.unshift(Math.round(i));
        
        response.scale.unshift(response.biggestValue);

        return response;  
      }
    },
    mounted(){
      console.log(this.selectedData);
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';


  .chart--points {
    display: flex;
    width: 100%;
    height: 250px;
  }
  .chart__scale {
    margin-right: 10px;
    margin-bottom: 18px;
    margin-top: -6px;
    display: flex;
    justify-content: space-between;
    flex-direction: column;
  }
  .chart__scale__item {
    vertical-align: middle;
  }
  .chart__list {
    width: 100%;
    position: relative;
    display: flex;
    align-items: stretch;
  }
  .chart__item {
    flex-grow: 1;
    margin-right: 5px;
    flex-direction: column;
    display: flex;

    &:last-child {
      margin-right: 0;
    }
  }
  .chart__item__line {
    background-color: #FFF8E3;
    height: 100%;
    margin-bottom: 10px;
    width: 100%;
    display: flex;
    align-items: flex-end;
    border-radius: 5px;
  }
  .chart__item__line__paint {
    background-color: #9ef3a0;
    width: 100%;
    border-radius: 5px;
    cursor: pointer;

    &:hover {
      opacity: 0.8;
    }
  }
  .chart__item__date {
    width: 9px;
    margin: auto;
  }
  .chart__item__date, .chart__scale__item {
    color: $light-gray;
    text-align: center;
    font-size: 12px;
  }
</style>
