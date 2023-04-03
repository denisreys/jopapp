<template>
  <div class="todoes">
    <ul class="todoes__list" ref="todoes" :style="{maxHeight: height}">
      <li class="todoes__item" v-for="day in week">
        <div class="todoes__dayname">{{ day.day_name }}</div>
        <div class="todoes__table">
          <div class="todoes__table__day">{{ day.day }}</div>
          <div class="todoes__table__month">{{ day.month_name_short }}</div>
          <div class="todoes__table__btn">
            <a href="#" class="todoes__table__btn__a" @click.prevent="">
              <i class="fal fa-plus" @click.prevent="
                $refs.popupAddTodo.popupShow();
                popups.addTodo.form.date.day = day.day;
                popups.addTodo.form.date.month_name = day.month_name;
                popups.addTodo.form.date.full_date = day.date;
                getGroups();
              "></i>
            </a>
          </div>
        </div>
        <div class="todoes__tasks" v-if="day.todoes.length">
          <task
            v-for="todo in day.todoes"
            :key="todo.task.id"
            class="task--small"
            :id="todo.task.id"
            :date="day.date"
            :name="todo.task.name"
            :checked="todo.task.check">
            <template v-slot:actions>
              <div class="task__actions">
                <a href="#" class="task__actions__item task__actions__item--delete" @click.prevent="todoDelete(todo.id)"><i class="fal fa-times"></i></a>
              </div>
            </template>
          </task>
        </div>
      </li>
    </ul>
    <div class="todoes__show">
      <a href="#" class="todoes__show__a" @click.prevent="toggleTodoes()">
        {{ showWeek }}
      </a>
    </div>
    <v-popup
      ref="popupAddTodo"
      :title="popups.addTodo.title + popups.addTodo.form.date.month_name+' '+this.popups.addTodo.form.date.day"
      :desc="popups.addTodo.desc"
      :btnSubmitName="popups.addTodo.btnSubmitName"
      @popupSubmit="addTodoSubmit()">
      <ul class="tabs tabs--popup tabs--todoes">
        <li class="tabs__item" 
          v-for="tab in popups.addTodo.tabs.items" 
          :class="{'active' : popups.addTodo.tabs.active == tab.id}" 
          @click="popups.addTodo.tabs.active = tab.id">
          {{tab.name}}
        </li>
      </ul>
      <div class="input-group" v-if="popups.addTodo.tabs.active == 1">
        <span v-if="popups.addTodo.form.errors['name']">
          {{popups.addTodo.form.errors['name'][0]}}
        </span>
        <label for="popup-todoes-newtaskname" class="label label--default">New daily task</label>
        <input type="text" ref="groupCreate" class="input w-100" placeholder="What do u want to do?" id="popup-todoes-newtaskname" v-model="popups.addTodo.form.newTaskName">
      </div>
      <div class="input-group" v-else-if="popups.addTodo.tabs.active === 2">
        <label for="popup-todoes-repeatable" class="label label--default">Select a repeatable task</label>
        <select id="popup-todoes-repeatable" class="select w-100" v-model="popups.addTodo.form.selectedTask.id" @change="changeDisabledInputs(event)">
          <option value="" selected>Не выбрано</option>
          <optgroup v-for="group in popups.addTodo.form.repeatableTasks" :label="group.name">
            <option v-for="task in group.tasks" :value="task.id">{{task.name}}</option>
          </optgroup>
        </select>
      </div>
      <div class="input-group input-group--inline">
        <div class="input-group__item">
        <label for="popup-todoes-points" class="label label--default">Difficult</label>
        <select id="popup-todoes-points" 
                class="select w-100" 
                v-model="popups.addTodo.form.points" 
                :disabled="popups.addTodo.tabs.active === 2">
          <option value="1">Очень легко</option>
          <option value="2">Легко</option>
          <option value="3">Нормально</option>
          <option value="4">Сложно</option>
          <option value="5">Очень сложно</option>
        </select>
        </div>
        <div class="input-group__item">
          <label for="popup-todoes-group" class="label label--default">Group</label>
          <select id="popup-todoes-group" 
                  class="select w-100" 
                  v-model="popups.addTodo.form.group" 
                  :disabled="popups.addTodo.tabs.active === 2">
            <option value="" selected>Без группы</option>
            <option v-for="group in popups.addTodo.form.repeatableTasks" :value="group.id">{{group.name}}</option>
          </select>
        </div>
      </div>
    </v-popup>
  </div>
</template>

<script>
  import axios from 'axios';
  import task from './Task.vue';
  import popup from '../Popup.vue';

  export default {
    components: {
      task,
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
                  name: 'Daily task'
                },
              {
                  id: 2,
                  name: 'Repeatable task'
                }
              ]
            },
            form: {
              date: {
                day: '',
                month_name: '',
                full_date: ''
              },
              selectedTask: {
                id: '',
                group_id: '',
                points: ''
              },
              newTaskName: '',
              points: 3,
              group: '',
              repeatableTasks: [],
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
          data['name'] = this.popups.addTodo.form.newTaskName;
          data['points'] = this.popups.addTodo.form.points;
          data['group_id'] = this.popups.addTodo.form.group;
        }
        else if(this.popups.addTodo.tabs.active == 2) data['id'] = this.popups.addTodo.form.selectedTask.id;

        axios.post('/addtodo', data).then((r) =>{
          this.$refs.popupAddTodo.popupClose();
          this.getTodoes();
          this.popups.addTodo.form.selectedTask.id = this.popups.addTodo.form.selectedTask.group_id = this.popups.addTodo.form.selectedTask.points = this.popups.addTodo.form.group = this.popups.addTodo.form.newTaskName = '';
          this.popups.addTodo.form.points = 3;
          this.popups.addTodo.form.errors = [];
        }).catch((error) =>{
          this.popups.addTodo.form.errors = error.response.data.errors;
        });
      },
      getGroups(){
        axios.get('/getgroups')
          .then((responce) => {
            this.popups.addTodo.form.repeatableTasks = responce.data;
        });
      },
      changeDisabledInputs(event){
        this.popups.addTodo.form.repeatableTasks.forEach((group) => {
          group.tasks.forEach((task) => {
            if(task.id == this.popups.addTodo.form.selectedTask.id){
              this.popups.addTodo.form.group = task.group_id; 
              this.popups.addTodo.form.points = task.points;
              return true;
            }
          });
        });
      },
      getTodoes(){
        axios.get('/gettodoes')
          .then((response) => {
            this.$root.loading.loaded++;
            if(!this.week.length){
              this.week = response.data;
              this.$nextTick(() => {
                this.hideTodoes();
              })
            }
            else {
              this.week = response.data;
              this.$nextTick(() => {
                this.updateHeightTodoes();
              })
            }
        });
      },
      showTodoes(){
        this.height = '100%';
        this.showWeek = 'Show 4 days ';
      },
      hideTodoes(){
        this.height = (this.$refs.todoes.children[0].clientHeight + this.$refs.todoes.children[2].clientHeight + 20) + 'px';

        var tasksHideCount = 0;
        for(var i=4; i < 7;i++) tasksHideCount += this.week[i].todoes;

        if(tasksHideCount > 0) this.showWeek = 'Show 7 days ('+tasksHideCount+')';
        else this.showWeek = 'Show 7 days';
      },
      updateHeightTodoes(){
        if(this.height != '100%') this.hideTodoes();
      },
      toggleTodoes(){
        if(this.height == '100%') this.hideTodoes();
        else this.showTodoes();
      },
      todoDelete(todo_id){
        if(todo_id){
          axios.post('/deletetodo', {id: todo_id}).then((r) =>{
            this.getTodoes();
          });
        }
      }
    },
    created(){
      this.getTodoes();
    }
  }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';

  .todoes {
    margin-bottom: 20px;
    position: relative;
  }
  .todoes__list {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    overflow: hidden;
  }
  .todoes__show {
    width: 100%;
    margin-top: 15px;
    text-align: center;
  }
  .todoes__show__a {
    color: $black;
    font-size: 16px;
    padding: 4px 10px;
    display: block;
  }
  .todoes__show__a:hover {
    text-decoration: underline;
  }
  .todoes__item {
    flex-basis: calc(50% - 10px);
    margin-bottom: 20px;

    &:last-child {
      margin-bottom: 0;
    }
  }
  .todoes__dayname {
    padding: 0 10px;
    line-height: 25px;
    font-size: 16px;
    letter-spacing: 2.4px;
    text-transform: uppercase;
    background: $main;
    border-radius: 2px;
  }
  .todoes__table {
    display: flex;
    padding: 2px 14px;
    text-align: center;
    align-items: center;
    border-bottom: solid 2px $black;
  }
  .todoes__table__day {
    font-size: 36px;
  }
  .todoes__table__month {
    flex: 1;
    font-size: 16px;
    letter-spacing: 2.4px;
    text-transform: uppercase;
  }
  .todoes__table__btn {
    .fal {
      font-size: 22px;
      color: $black;
    }
  }
  .todoes__tasks {
    padding-top: 15px;
    padding-left: 10px;
  }
</style>
