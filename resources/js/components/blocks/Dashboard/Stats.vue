<template ref="stat">
  <div class="sb__stat" v-if="total > 0">
    <div class="stat__count">
      <span class="stat__count__n">{{total}}</span>
      <div class="stat__count__t">last 30 days</div>
    </div>
    <div class="sb__stat--diagram">
      <div class="sb__diagram__item" v-for="item in diagram" :style="{background: '#'+item.color,width: item.width+'%'}" :title="item.name +' '+item.width +'%'"></div>
    </div>
    <div class="sb__stat__bomb">
      <img class="sb__stat__bomb__img" :src="bomb" alt="">
    </div>
    <!--<a class="btn btn--default mt-15" href="/stats"><i class="fa-light fa-chart-pie"></i>  Stats</a>-->
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
        total: 0,
        oldTotal: '',
        bomb: '',
        countLoad: 0,
        diagram: [],
        popups: {
          groupCreate: {
            name: 'stats',
            visibility: false,
            title: 'Статистика',
            desc: ''
          }
        },
      }
    },
    methods: {
      totalUpdate(isbomb){
        this.oldTotal = this.total;

        axios.get('/totalupdate')
          .then((response) => {
            if(this.countLoad == 0 || !isbomb){
              this.countLoad++;
            }
            else if(this.countLoad > 0 && isbomb){
              if(response.data.total >= 50 && this.oldTotal < 50 || response.data.total >= 100 && this.oldTotal < 100){
                this.bomb = '/images/bomb.gif?3ee01c3566d3875da7c87af880baffad';
                setTimeout(() => this.bomb = '', 1600);
              }
            }
            this.total = response.data.total;
            this.diagram = response.data.diagram;
        });
      }
    },
    watch: {
      statsIsUpdate: function (val) {
        this.totalUpdate(true);
      }
    },
    mounted() {
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
</style>
