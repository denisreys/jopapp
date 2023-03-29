<template>
  <div class="chart chart--groups mt-30">
    <div class="main__header">
      <div class="main__header__title">Groups chart</div>
    </div>
    <div class="chart__container">
      <div class="chart__list chart__list--circle" >
        <svg class="chart__list--circle__svg" width="150" height="150" viewBox="0 0 37 37" >
          <circle class="chart__list--circle__svg__line" r="15.9" cx="50%" cy="50%" 
            v-for="item in chart.groups" 
            :stroke="'#'+item.color" 
            :stroke-dasharray="item.width +' 100'" 
            :stroke-dashoffset="item.offset">
            <title>{{ item.name+': '+item.width+'%'}}</title>
          </circle>
        </svg>
      </div>
      <div class="chart__container chart__list--lines">
        <div class="chart__list">
          <div class="chart__item"  v-for="item in chart.groups" >
            <div class="chart__item__name">{{ item.name }}</div>
            <div class="chart__item__line">
              <div class="chart__item__line__paint" :indexGroup="item.id + item.points" :style="[
                {backgroundColor: '#'+item.color},
                {width: ((item.points / chart.totalPoints) * 100)+'%'}]" 
                :title="item.points+' Points'"
                :key="item.key">
              </div>
            </div>
            <div class="chart__item__value">{{ item.width+'%' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</template>

<script>
  export default {
    props: {
      selectedData: Object
    },
    data() {
      return {
        circleChartDegrees: 0
      }
    },
    computed: {
      chart(){
        let groupsAssoc = {},
            response = {totalPoints: 0, groups: [{name: 'Nothing', points: 0, key: 0, color: 'FFF8E3', width: 100}]};
        
        if(this.selectedData.days && this.selectedData.groups_diagram) {
          groupsAssoc = Object.assign({}, this.selectedData.groups_diagram);
        }
        else if(this.selectedData.months){
          Object.entries(this.selectedData.months).forEach(([key, month]) => {
            if(month.groups_diagram){
              Object.entries(month.groups_diagram).forEach(([key, group]) => {
                if(group.id in groupsAssoc) groupsAssoc[group.id]['points'] += group.points;
                else groupsAssoc[group.id] = Object.assign({}, group);
              });
            }
          });
        }
        //CREATE ARRAY AND SORTING BY POINTS
        if(Object.keys(groupsAssoc).length){
          var arrayIndex = 0;
          response.groups = [];

          Object.entries(groupsAssoc).forEach(([key, group]) => {
            response.totalPoints += group.points;
            response.groups[arrayIndex] = group;
            response.groups[arrayIndex]['width'] = 0;
            arrayIndex++;
          });

          response.groups.sort(function(a,b){
            return b.points - a.points;
          });

          var groupOffset = 0,
              randomNumber = Math.random().toFixed(5);

          for (let i = 0; i < response['groups'].length;) {
            var width = ((response['groups'][i].points / response.totalPoints) * 100).toFixed(0);
            
            response.groups[i].width = width;
            response.groups[i].offset = groupOffset;
            response.groups[i].key = response['groups'][i].points +'.'+randomNumber;
            groupOffset -= Number(width);
            i++;
          }
        }

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

  @keyframes groupLines {
    0% {
      width: 0%;
    }
  }
  .chart__list--circle__svg__line {
    fill: none;
    stroke-width: 5;

    &:hover {
      opacity: 0.8;
      cursor: pointer;
    }
  }
  .chart--groups {
    .chart__container {
      display: flex;
    }
    .chart__list--circle {
      flex-basis: 150px;
    }
    .chart__list--lines {
      flex: 1;
      margin-left: 30px;
      
      .chart__list {
        width: 100%;
        display: flex;
        flex-direction: column;
      }
      .chart__item {
        display: flex;
        margin-bottom: 10px;
        justify-content: center;
      }
      .chart__item__name {
        width: 120px;
        font-size: 16px;
        line-height: 20px;
      }
      .chart__item__line {
        flex: 1;
        background-color: $main-light;
        width: 100%;
        display: flex;
        border-radius: 5px;
        height: 8px;
        margin: auto 10px;
      }
      .chart__item__line__paint {
        width: 100%;
        border-radius: 5px;
        cursor: pointer;
        animation-name: groupLines;
        animation-duration: 0.3s; 
      }
      .chart__item__value {
        flex-basis: 20px;
        font-size: 14px;
        line-height: 20px;
      }
    }
  }
</style>
