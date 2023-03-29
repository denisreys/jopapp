<template>
  <div class="sb__block sb__block--donelist">
    <div class="sb__block__title">
      <span class="sb__block__title__span">Done</span>
    </div>
    <template v-if="doneList">
      <div class="sb__list sb__list--donelist">
        <div class="sb__list__item" v-for="(item, index) in doneList">
          <div class="donelist__name">{{ item.list[0].affair.name }}</div>
            <div class="donelist__button" @click="toggleList(index, item)" >
              <i class="fa-sharp fa-light fa-chevron-down"></i>
            </div>
            <div class="donelist__list" v-if="index === showList">
              <div class="donelist__list__item" v-for="check in item.list">
                <div class="donelist__list__item__date">{{ check.day+' '+check.month +' at '+check.time }}</div>
              </div>
            </div>
        </div>
      </div>
    </template>
    <template v-else>
      <div class="sb__desc">
        Nothing yet
      </div>
    </template>
  </div>
</template>

<script>
  
  export default {
    props: {
      selectedData: Object
    },
    data () {
      return {
        showList: ''
      }
    },
    methods: {
      toggleList(index, item){
        if(index === this.showList) this.showList = false
        else this.showList = index;
      },
      forEachDays(response, array){
        Object.entries(array).forEach(([key, day]) => {
          if(day.checks){
            Object.entries(day.checks).forEach(([key, check]) => {
              if(check.affair.id in response){
                response[check.affair_id].push(check);
              }
              else {
                response[check.affair_id] = [];
                response[check.affair_id].push(check);
              }
            });
          }
        });

        return response;
      }
    },
    computed: {
      doneList(){
        var doneAssoc = [];
        
        if(this.selectedData.months){
          Object.entries(this.selectedData.months).forEach(([key, month]) => {
            doneAssoc = this.forEachDays(doneAssoc, month.days);
          });
        }
        else if(this.selectedData.days){
          doneAssoc = this.forEachDays(doneAssoc, this.selectedData.days);
        }

        let arrayIndex = 0,
            response = [];

        Object.entries(doneAssoc).forEach(([key, item]) => {
          response[arrayIndex] = [];
          response[arrayIndex].list = item;
          arrayIndex++;
        });
        console.log(response);
        return response;
      }
    },
    created(){
      
    },
    mounted(){
      this.$root.loading.loaded++;
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';

  .donelist__name{
    margin-right: 15px;
  }
  .donelist__button {
    position: absolute;
    right: -10px;
    top:0;
    width: 20px;
    cursor: pointer;

    i {
      font-size: 14px;
    }
  }
  .donelist__list {
    margin-top: 5px;
    padding-left: 5px;
    font-size: 14px;
  }
  .donelist__list__item__points {
    float: left;
    color: $green;
    margin-right: 5px;
  }
</style>
