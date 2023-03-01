<template>
  <div class="diary">
    <ul class="diary__list" ref="diary" :style="{maxHeight: height}">
      <li class="diary__item" v-for="day in week">
        <div class="diary__dayname">{{ day.day_name }}</div>
        <div class="diary__table">
          <div class="diary__table__day">{{ day.day }}</div>
          <div class="diary__table__month">{{ day.month_name_short }}</div>
          <div class="diary__table__btn">
            <a href="#" class="diary__table__btn__a" @click.prevent="">
              <i class="fal fa-plus" @click.prevent="
                $refs.popupAddTodo.popupShow();
                popups.addTodo.form.date.day = day.day;
                popups.addTodo.form.date.month_name = day.month_name;
                popups.addTodo.form.date.full_date = day.date;
                getRegularList();
              "></i>
            </a>
          </div>
        </div>
        <div class="diary__affairs" v-if="day.todoes.length">
          <affair
            v-for="todo in day.todoes"
            :key="todo.affair.id"
            class="affair--small"
            :id="todo.affair.id"
            :date="day.date"
            :name="todo.affair.name"
            :checked="todo.affair.check">
            <template v-slot:actions>
              <div class="affair__actions">
                <a href="#" class="affair__actions__item affair__actions__item--delete" @click.prevent="diaryDelete(todo.id)"><i class="fal fa-times"></i></a>
              </div>
            </template>
          </affair>
        </div>
      </li>
    </ul>
    <div class="diary__show">
      <a href="#" class="diary__show__a" @click.prevent="toggleDiary()">
        {{ showWeek }}
      </a>
    </div>

    <v-popup
      ref="popupAddTodo"
      :title="popups.addTodo.title + popups.addTodo.form.date.month_name+' '+this.popups.addTodo.form.date.day"
      :desc="popups.addTodo.desc"
      :btnSubmitName="popups.addTodo.btnSubmitName"
      @popupSubmit="addTodoSubmit()">
      <ul class="tabs tabs--popup tabs--diary">
        <li class="tabs__item" v-for="tab in popups.addTodo.tabs.items" :class="{'active' : popups.addTodo.tabs.active == tab.id}" @click="popups.addTodo.tabs.active = tab.id">{{tab.name}}</li>
      </ul>
      <div class="input-group" v-if="popups.addTodo.tabs.active == 1">
        <span v-if="popups.addTodo.form.errors['name']">
          {{popups.addTodo.form.errors['name'][0]}}
        </span>
        <label for="popup-diary-newaffairname" class="label label--default">New affair</label>
        <input type="text" ref="groupCreate" class="input w-100" placeholder="What do u want to do?" id="popup-diary-newaffairname" v-model="popups.addTodo.form.newAffairName">
      </div>
      <div class="input-group" v-else-if="popups.addTodo.tabs.active === 2">
        <label for="popup-diary-regular" class="label label--default">It's a regular</label>
        <select id="popup-diary-regular" class="select w-100" v-model="popups.addTodo.form.selectedAffair.id" @change="changeDisabledInputs(event)">
          <option value="" selected>Не выбрано</option>
          <optgroup v-for="group in popups.addTodo.form.regularAffairs" :label="group.name">
            <option v-for="affair in group.affairs" :value="affair.id">{{affair.name}}</option>
          </optgroup>
        </select>
      </div>
      <div class="input-group input-group--inline">
        <div class="input-group__item">
        <label for="popup-diary-points" class="label label--default">Difficult</label>
        <select id="popup-diary-points" class="select w-100" v-model="popups.addTodo.form.points" :disabled="popups.addTodo.tabs.active === 2">
          <option value="1">Очень легко</option>
          <option value="2">Легко</option>
          <option value="3">Нормально</option>
          <option value="4">Сложно</option>
          <option value="5">Очень сложно</option>
        </select>
        </div>
        <div class="input-group__item">
          <label for="popup-diary-group" class="label label--default">Group</label>
          <select id="popup-diary-group" class="select w-100" v-model="popups.addTodo.form.group" :disabled="popups.addTodo.tabs.active === 2">
            <option value="" selected>Без группы</option>
            <option v-for="group in popups.addTodo.form.regularAffairs" :value="group.id">{{group.name}}</option>
          </select>
        </div>
      </div>
    </v-popup>
  </div>
</template>

<script>
  import axios from 'axios';
  import affair from './Affair.vue';
  import popup from '../Popup.vue';

  export default {
    components: {
      affair,
      'v-popup' : popup
  	},
    data () {
      return {
        week: [],
        visible: false,
        height: '100%',
        showWeek: '',
        popups: {
          addTodo: {
            name: 'addTodo',
            visibility: false,
            title: 'To do on ',
            desc: 'Группируйте повторяющиеся дела по спискам для их удобного планирования по дням и отслеживанию прогресса',
            btnSubmitName: 'Добавить',
            tabs: {
              active: 1,
              items: [
              {
                  id: 1,
                  name: 'New'
                },
              {
                  id: 2,
                  name: 'Regular'
                }
              ]
            },
            form: {
              date: {
                day: '',
                month_name: '',
                full_date: ''
              },
              selectedAffair: {
                id: '',
                group_id: '',
                points: ''
              },
              newAffairName: '',
              points: 3,
              group: '',
              regularAffairs: [],
              errors: []
            }
          }
        },
      }
    },
    methods: {
      addTodoSubmit(){
        var data = {'date': this.popups.addTodo.form.date.full_date};

        if(this.popups.addTodo.tabs.active == 1){
          data['name'] = this.popups.addTodo.form.newAffairName;
          data['points'] = this.popups.addTodo.form.points;
          data['group_id'] = this.popups.addTodo.form.group;
        }
        else if(this.popups.addTodo.tabs.active == 2) data['id'] = this.popups.addTodo.form.selectedAffair.id;

        axios.post('/addtodo', data).then((r) =>{
          this.$refs.popupAddTodo.popupClose();
          this.getDiaryList();
          this.popups.addTodo.form.selectedAffair.id = this.popups.addTodo.form.selectedAffair.group_id = this.popups.addTodo.form.selectedAffair.points = this.popups.addTodo.form.group = this.popups.addTodo.form.newAffairName = '';
          this.popups.addTodo.form.points = 3;
          this.popups.addTodo.form.errors = [];
        }).catch((error) =>{
          this.popups.addTodo.form.errors = error.response.data.errors;
        });
      },
      getRegularList(){
        axios.get('/getregular')
          .then((responce) => {
            this.popups.addTodo.form.regularAffairs = responce.data;
        });
      },
      changeDisabledInputs(event){
        this.popups.addTodo.form.regularAffairs.forEach((group) => {
          group.affairs.forEach((affair) => {
            if(affair.id == this.popups.addTodo.form.selectedAffair.id){
              this.popups.addTodo.form.group = affair.group_id; 
              this.popups.addTodo.form.points = affair.points;
              return true;
            }
          });
        });
      },
      getDiaryList(){
        axios.get('/getdiary')
          .then((response) => {
            if(!this.week.length){
              this.week = response.data;
              this.$nextTick(() => {
                this.hideDiary();
              })
            }
            else {
              this.week = response.data;
              this.$nextTick(() => {
                this.updateHeightDiary();
              })
            }
        });
      },
      showDiary(){
        this.height = '100%';
        this.showWeek = 'Few days ';
      },
      hideDiary(){
        this.height = (this.$refs.diary.children[0].clientHeight + this.$refs.diary.children[2].clientHeight + 20) + 'px';

        var affairsHideCount = 0;
        for(var i=4; i < 7;i++) affairsHideCount += this.week[i].todoes;

        if(affairsHideCount > 0) this.showWeek = 'Show 7 days ('+affairsHideCount+')';
        else this.showWeek = 'Show 7 days';
      },
      updateHeightDiary(){
        if(this.height != '100%') this.hideDiary();
      },
      toggleDiary(){
        if(this.height == '100%') this.hideDiary();
        else this.showDiary();
      },
      diaryDelete(todo_id){
        if(todo_id){
          axios.post('/deletetodo', {id: todo_id}).then((r) =>{
            this.getDiaryList();
          });
        }
      }
    },
    mounted(){
      this.getDiaryList();
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';

  .diary {
    margin-bottom: 10px;
    position: relative;
  }
  .diary__list {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    overflow: hidden;
  }
  .diary__show {
    width: 100%;
    margin-top: 10px;
    text-align: center;
  }
  .diary__show__a {
    color: $black;

    padding: 5px 10px;
    display: block;
  }
  .diary__show__a:hover {
    text-decoration: underline;
  }
  .diary__item {
    flex-basis: calc(50% - 12px);
    margin-bottom: 20px;

    &:last-child {
      margin-bottom: 0;
    }
  }
  .diary__dayname {
    padding: 0 10px;
    line-height: 25px;
    font-size: 16px;
    letter-spacing: 2.4px;
    text-transform: uppercase;
    background: $main;
    border-radius: 2px;
  }
  .diary__table {
    display: flex;
    padding: 2px 14px;
    text-align: center;
    align-items: center;
    border-bottom: solid 2px $black;
  }
  .diary__table__day {
    font-size: 36px;
  }
  .diary__table__month {
    flex: 1;
    font-size: 16px;
    letter-spacing: 2.4px;
    text-transform: uppercase;
  }
  .diary__table__btn {
    .fal {
      font-size: 22px;
      color: $black;
    }
  }
  .diary__affairs {
    padding-top: 15px;
    padding-left: 10px;
  }
</style>
