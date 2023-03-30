<template ref="shortstats">
  <div class="sb__stat">
    <div class="stat__count">
      <span class="stat__count__n">{{total}}</span>
      <div class="stat__count__t">last 30 days</div>
    </div>
    <div class="sb__stat--diagram" >
      <template v-if="total > 0">
        <div class="sb__diagram__item" 
          v-for="item in diagram" 
          :style="{background: '#'+item.color,width: item.width+'%'}" 
          :title="item.name +' '+item.width +'%'">
        </div>
      </template>
      <template v-else>
        <div class="sb__diagram__item sb__diagram__item--nothing" 
          title="Nothing">
        </div>
      </template>
    </div>
    <div class="sb__stat__bomb">
      <img class="sb__stat__bomb__img" :src="bomb" alt="">
    </div>
  </div>
</template>
<script>
  import axios from 'axios';

  export default {
    props: {
      statsIsUpdate: Number
    },
    data () {
      return {
        data: [],
        total: 0,
        oldTotal: '',
        bomb: '',
        countLoad: 0,
      }
    },
    methods: {
      totalUpdate(isbomb){
        this.oldTotal = this.total;

        axios.get('/getdashboardstats')
          .then((response) => {
            this.$root.loading.loaded++;
            if(this.countLoad == 0 || !isbomb)
              this.countLoad++;
            else if(this.countLoad > 0 && isbomb){
              if(response.data.total >= 50 && this.oldTotal < 50 || response.data.total >= 100 && this.oldTotal < 100){
                this.bomb = '/images/bomb.gif?3ee01c3566d3875da7c87af880baffad';
                setTimeout(() => this.bomb = '', 1600);
              }
            }
            this.total = response.data.total;
            this.data = response.data;
        });
      }
    },
    computed: {
      diagram(){
        let response = this.data.diagram;

        Object.entries(response).forEach(([key, item]) => {
          response[key].width = ((item.points/this.total)*100).toFixed(2);
        });

        return response;
      }
    },
    watch: {
      statsIsUpdate: function (val) {
        this.totalUpdate(true);
      }
    },
    created() {
      this.totalUpdate();
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';
  
  .sb__stat {
    position: relative;
  }
  .sb__stat__bomb {
    position: absolute;
    text-align: center;
    width: 100%;
    top: -50px;
  }
  .stat__count__t {
    margin-top: 5px;
    font-size: 14px;
    color: $light-gray;
  }
  .sb__stat__bomb__img {
    width: 130px;
  }
  .sb__stat--diagram {
    overflow: hidden;
    border-radius: 2px;
    margin-top: 20px;
  }
  .sb__diagram__item {
    height: 15px;
    float:left;
    border-left: 2px solid #fff;
    box-sizing: border-box;
    cursor: pointer;

    &:hover {
      opacity: 0.8;
    }

    &:first-child {
      border-left: none;
    }
  }
  .sb__diagram__item--nothing {
    background-color: $main-light;
    width: 100%;
  }
</style>
