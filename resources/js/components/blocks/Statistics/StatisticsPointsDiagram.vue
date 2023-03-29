<template>
  <div class="chart chart--points">
    <div class="main__header">
      <div class="main__header__title">Points chart</div>
    </div>
    <div class="chart__container">
      <div class="chart__scale">
        <div class="chart__scale__item" v-for="item in chart.scale">
          <div class="chart__scale__item__value">{{ item }}</div>
        </div>
      </div>
      <div class="chart__list">
          <div class="chart__item" v-if="selectedData.days" v-for="day in selectedData.days" :style="{width: (100 / selectedData.days.length) +'%'}">
            <div class="chart__item__line">
              <div class="chart__item__line__paint" 
                :style="{height: ((day.points/chart.biggestValue) * 100)+'%'}" 
                :title="day.points+' Points'"
                :key="selectedData.year+'-'+selectedData.month">
              </div>
            </div>
            <div class="chart__item__date">{{ day.day }}</div>
          </div>
          <div class="chart__item" v-if="selectedData.months" v-for="month in selectedData.months">
            <div class="chart__item__line">
              <div class="chart__item__line__paint" 
                :style="{height: ((month.points/chart.biggestValue) * 100)+'%'}" 
                :title="month.points+' Points'"
                :key="month.year+'-'+month.month">
              </div>
            </div>
            <div class="chart__item__date">{{ month.month_name_short }}</div>
          </div>
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
    computed: {
      chart(){
        var response = {biggestValue: 0, scale: []};

        if(this.selectedData.days){
          response.biggestValue = 10;

          Object.entries(this.selectedData.days).forEach(([key, day]) => {
            if(response.biggestValue < day.points) response.biggestValue = day.points;
          });
          if(response.biggestValue > 10){
            response.biggestValue = Math.ceil(response.biggestValue/10)*10;
          }
        }
        else if(this.selectedData.months){
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
      this.$root.loading.loaded++;
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';

  @keyframes pointsRender {
    0% {
      height: 0px;
    }
  }
  .chart--points {
    .chart__container {
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
      background-color: $main-light;
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
      animation-name: pointsRender;
      animation-duration: 0.3s; 

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
  }
  
</style>
