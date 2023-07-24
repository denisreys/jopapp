<template>
  <div class="main__groups">
    <div class="main__header">
      <div class="main__header__title">Groups and Repeatable tasks</div>
      <div class="main__header__add">
        <a href="#" class="main__header__add__a" 
        @click.prevent="$refs.popupGroupCreate.popupShow();">
        <i class="fal fa-plus"></i>
      </a>
      </div>
    </div>
    <ul class="groups__list" v-if="list.length != 0">
      <li class="groups__item" v-for="group in list">
        <div class="group">
          <ul class="group__actions">
            <li class="group__actions__item">
              <a href="#" @click.prevent="
                $refs.popupGroupEdit.popupShow();
                popups.groupEdit.form.group_id = group.id;
                popups.groupEdit.form.name = group.name;
                popups.groupEdit.form.color = group.color;
                popups.groupEdit.form.icon = group.icon;
              "><i class="fal fa-pencil"></i></a>
            </li>
            <li class="group__actions__item">
              <a href="#" @click.prevent="
                $refs.popupTaskCreate.popupShow();
                popups.taskCreate.form.group_id = group.id;
                popups.taskCreate.form.group_name = group.name;
              "><i class="fal fa-plus"></i></a>
            </li>
          </ul>
          <div class="group__wrap">
            <div class="group__i"><i class="fal" :class="('fa-' + group.icon)"></i><i class="fas" :class="('fa-' + group.icon)" :style="{color: ('#' + group.color)}"></i></div>
            <div class="group__t">{{group.name}}</div>
          </div>
        </div>
        <div class="group__list" v-if="group.tasks.length">
          <task
            v-for="item in group.tasks"
            :key="item.id"
            class="task--default"
            :id="item.id"
            :name="item.name"
            :checked="item.check">
            <template v-slot:actions>
              <div class="task__actions">
                <a href="#" class="task__actions__item task__actions__item--edit" @click.prevent="
                  $refs.popupTaskEdit.popupShow();
                  popups.taskEdit.form.id = item.id;
                  popups.taskEdit.form.group_id = item.group_id;
                  popups.taskEdit.form.name = item.name;
                  popups.taskEdit.form.points = item.points;
                "><i class="fal fa-pencil"></i></a>
              </div>
            </template>
          </task>
        </div>
      </li>
    </ul>
    <div v-else>Добавьте свою первую группу дел</div>

    <!-- СОЗДАТЬ НОВУЮ ГРУППУ -->
    <v-popup
      :title="popups.groupCreate.title"
      :desc="popups.groupCreate.desc"
      ref="popupGroupCreate"
      :btnSubmitName="popups.groupCreate.btnSubmitName"
      @popupSubmit="groupCreateSubmit()">
      <p v-if="popups.groupCreate.form.errors.name">{{popups.groupCreate.form.errors.name[0]}}</p>
      <p v-if="popups.groupCreate.form.errors.color">{{popups.groupCreate.form.errors.color[0]}}</p>
      <p v-if="popups.groupCreate.form.errors.icon">{{popups.groupCreate.form.errors.icon[0]}}</p>
      <div class="input-group">
        <label for="popup-group-name" class="label label--default">Название</label>
        <input type="text" ref="groupCreate" class="input w-100" placeholder="Название группы" id="popup-group-name" v-model="popups.groupCreate.form.name">
      </div>
      <div class="input-group input-group--inline">
        <div class="input-group__item">
          <label class="label label--default" for="popup-group-icon">Иконка</label>
          <div class="checkboxes checkboxes--icon" v-for="icon in icons">
            <input type="radio" class="checkboxes__input" :id="('group-create-icon-' + icon)" :value="icon" v-model="popups.groupCreate.form.icon">
            <label class="checkboxes__label" :for="('group-create-icon-' + icon)">
              <i class="fal" :class="('fa-' + icon)"></i>
            </label>
          </div>
        </div>
        <div class="input-group__item">
          <span class="label label--default">Цвет</span>
          <div class="checkboxes checkboxes--color" v-for="color in colors">
            <input type="radio" class="checkboxes__input" :id="('popup-' + color.name)" :value="color.code" v-model="popups.groupCreate.form.color">
            <label class="checkboxes__label" :for="('popup-' + color.name)" :style="{backgroundColor: ('#' + color.code)}"></label>
          </div>
        </div>
      </div>
    </v-popup>

    <!-- РЕДАКТИРОВАТЬ ГРУППУ -->
    <v-popup
      :title="popups.groupEdit.title"
      :desc="popups.groupEdit.desc"
      ref="popupGroupEdit"
      :btnSubmitName="popups.groupEdit.btnSubmitName"
      @popupSubmit="groupEditSubmit()">
      <p v-if="popups.groupEdit.form.errors.name">{{popups.groupEdit.form.errors.name[0]}}</p>
      <p v-if="popups.groupEdit.form.errors.color">{{popups.groupEdit.form.errors.color[0]}}</p>
      <p v-if="popups.groupEdit.form.errors.icon">{{popups.groupEdit.form.errors.icon[0]}}</p>
      <div class="input-group">
        <label for="group-edit-name" class="label label--default">Название</label>
        <input type="text" ref="groupCreate" class="input w-100" placeholder="Название группы" id="group-edit-name" v-model="popups.groupEdit.form.name">
      </div>
      <div class="input-group input-group--inline">
        <div class="input-group__item">
          <label class="label label--default" for="popup-group-icon">Иконка</label>
          <div class="checkboxes checkboxes--icon" v-for="icon in icons">
            <input type="radio" class="checkboxes__input" :id="('group-edit-icon-' + icon)" :value="icon" v-model="popups.groupEdit.form.icon">
            <label class="checkboxes__label" :for="('group-edit-icon-' + icon)">
              <i class="fal" :class="('fa-' + icon)"></i>
            </label>
          </div>
        </div>
        <div class="input-group__item">
          <span class="label label--default">Цвет</span>
          <div class="checkboxes checkboxes--color" v-for="color in colors">
            <input type="radio" class="checkboxes__input" :id="('group-edit-color-' + color.name)" :value="color.code" v-model="popups.groupEdit.form.color">
            <label class="checkboxes__label" :for="('group-edit-color-' + color.name)" :style="{backgroundColor: ('#' + color.code)}"></label>
          </div>
        </div>
      </div>
      <template v-slot:additional>
        <a href="#" @click.prevent="groupDelete()" class="popup__footer__additional__delete">Удалить</a>
      </template>
    </v-popup>

    <!-- СОЗДАТЬ НОВОЕ РЕГУЛЯРНОЕ ЗАНЯТИЕ -->
    <v-popup
      ref="popupTaskCreate"
      :title="popups.taskCreate.title"
      :desc="popups.taskCreate.desc"
      :btnSubmitName="popups.taskCreate.btnSubmitName"
      @popupSubmit="taskCreateSubmit()">
      <p v-if="popups.taskCreate.form.errors.name">{{popups.taskCreate.form.errors.name}}</p>
      <p v-if="popups.taskCreate.form.errors.points">{{popups.taskCreate.form.errors.points}}</p>
      <div class="input-group">
        <input type="text" ref="taskCreate" class="input w-100" placeholder="Чем займешься?" v-model="popups.taskCreate.form.name">
      </div>
      <div class="input-group input-group--inline">
        <div class="input-group__item">
          <label for="popup-task-points" class="label label--default">Difficult</label>
          <select id="popup-task-points" class="select" v-model="popups.taskCreate.form.points">
            <option value="1">Очень легко</option>
            <option value="2">Легко</option>
            <option value="3">Нормально</option>
            <option value="4">Сложно</option>
            <option value="5">Очень сложно</option>
          </select>
        </div>
        <div class="input-group__item">
          <label for="popup-task-group" class="label label--default">Group</label>
          <select id="popup-task-group" class="select" v-model="popups.taskCreate.form.group_id">
            <option v-for="group in list" :value="group.id">{{ group.name }}</option>
          </select>
        </div>
      </div>
    </v-popup>

    <!-- РЕДАКТИРОВАТЬ РЕГУЛЯРНОЕ ЗАНЯТИЕ -->
    <v-popup
      ref="popupTaskEdit"
      :title="popups.taskEdit.title"
      :desc="popups.taskEdit.desc"
      :btnSubmitName="popups.taskEdit.btnSubmitName"
      @popupSubmit="taskEditSubmit()">
      <p v-if="popups.taskEdit.form.errors.name">{{popups.taskEdit.form.errors.name[0]}}</p>
      <p v-if="popups.taskEdit.form.errors.points">{{popups.taskEdit.form.errors.points[0]}}</p>
      <div class="input-group">
        <input type="text" ref="taskEdit" class="input w-100" placeholder="Чем займешься?" v-model="popups.taskEdit.form.name">
      </div>
      <div class="input-group input-group--inline">
        <div class="input-group__item">
          <label for="popup-task-points" class="label label--default">Difficult</label>
          <select id="popup-task-points" class="select" v-model="popups.taskEdit.form.points">
            <option value="1">Очень легко</option>
            <option value="2">Легко</option>
            <option value="3">Нормально</option>
            <option value="4">Сложно</option>
            <option value="5">Очень сложно</option>
          </select>
        </div>
        <div class="input-group__item">
          <label for="popup-task-group" class="label label--default">Group</label>
          <select id="popup-task-group" class="select" v-model="popups.taskEdit.form.group_id">
            <option v-for="group in list" :value="group.id">{{ group.name }}</option>
          </select>
        </div>
      </div>
      <template v-slot:additional>
        <a href="#" @click.prevent="taskDelete()" class="popup__footer__additional__delete">Удалить</a>
      </template>
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
      'v-popup' : popup,
  	},
    data () {
      return {
        list: [],
        popups: {
          groupCreate: {
            name: 'groupCreate',
            visibility: false,
            title: 'Добавление группы',
            desc: 'Группируйте повторяющиеся дела по спискам для их удобного планирования по дням и отслеживанию прогресса<br><br>Примеры: Обучение, Работа, Здоровье. Или более конкретные: Английский, Программирование',
            btnSubmitName: 'Добавить',
            form: {
              name: '',
              color: '',
              icon: '',
              errors: []
            }
          },
          groupEdit: {
            name: 'groupEdit',
            visibility: false,
            title: 'Редактирование группы',
            desc: 'Группируйте повторяющиеся дела по спискам для их удобного планирования по дням и отслеживанию прогресса<br><br>Примеры: Обучение, Работа, Здоровье. Или более конкретные: Английский, Программирование',
            btnSubmitName: 'Сохранить',
            form: {
              group_id: '',
              name: '',
              color: '',
              icon: '',
              errors: []
            }
          },
          taskCreate: {
            name: 'taskCreate',
            visibility: false,
            title: 'Добавить регулярное дело',
            desc: 'Добавляйте повторяющиеся дела в список, чтобы в будущем было удобней их планировать по дням или, чтобы иметь возможность отмечать их выполнение без предварительного планирования. <br><br>Примеры: Позаниматься в Дуолинго; Выйти на пробежку; Почитать книгу;',
            btnSubmitName: 'Добавить',
            form: {
              group_id: '',
              name: '',
              points: 3,
              errors: []
            }
          },
          taskEdit: {
            name: 'taskEdit',
            visibility: false,
            title: 'Редактировать регулярное дело',
            desc: 'Добавляйте повторяющиеся дела в список, чтобы в будущем было удобней их планировать по дням или, чтобы иметь возможность отмечать их выполнение без предварительного планирования. <br><br>Примеры: Позаниматься в Дуолинго; Выйти на пробежку; Почитать книгу;',
            btnSubmitName: 'Сохранить',
            form: {
              id: '',
              group_id: '',
              name: '',
              points: '',
              errors: []
            }
          }
        },
        colors: [
          {
            name: 'yellow',
            code: 'ffe494'
          },
          {
            name: 'gray',
            code: '939393'
          },
          {
            name: 'red',
            code: 'ff9494'
          },
          {
            name: 'green',
            code: 'aaeeb2'
          },
          {
            name: 'blue',
            code: 'aad8ee'
          },
          {
            name: 'pink',
            code: 'f7c7ed'
          },
          {
            name: 'light-blue',
            code: 'aab8ed'
          },
          {
            name: 'purple',
            code: 'd6a2ed'
          },
          {
            name: 'orange',
            code: 'ffbb89'
          },
        ],
        icons: ['book', 'desktop', 'code', 'running', 'heartbeat', 'graduation-cap', 'utensils', 'dumbbell', 'biking', 'briefcase', 'lightbulb-on', 'money-bill']
      }
    },
    methods: {
      groupDelete(){
        axios.post('/deletegroup', this.popups.groupEdit.form).then(() =>{
          this.$refs.popupGroupEdit.popupClose();
          this.getGroups();
          this.popups.groupEdit.form.name = this.popups.groupEdit.form.color = '';
          this.popups.groupEdit.form.errors = [];
        }).catch((error) =>{
          this.popups.groupEdit.form.errors = error.response.data.errors;
        });
      },
      groupEditSubmit(){
        axios.post('/editgroup', this.popups.groupEdit.form).then(() =>{
          this.$refs.popupGroupEdit.popupClose();
          this.getGroups();
          this.popups.groupEdit.form.name = this.popups.groupEdit.form.color = '';
          this.popups.groupEdit.form.errors = [];
        }).catch((error) =>{
          this.popups.groupEdit.form.errors = error.response.data.errors;
        });
      },
      groupCreateSubmit(){
        axios.post('/creategroup', this.popups.groupCreate.form).then((r) =>{
          this.$refs.popupGroupCreate.popupClose();
          this.getGroups();
          this.popups.groupCreate.form.name = this.popups.groupCreate.form.color = '';
          this.popups.groupCreate.form.errors = [];
        }).catch((error) =>{
          this.popups.groupCreate.form.errors = error.response.data.errors;
        });
      },
      taskCreateSubmit(){
        axios.post('/createtask', this.popups.taskCreate.form).then((r) =>{
          this.$refs.popupTaskCreate.popupClose();
          this.getGroups();
          this.popups.taskCreate.form.name = '';
          this.popups.taskCreate.form.points = 3;
          this.popups.taskCreate.form.errors = [];
        }).catch((error) =>{
          this.popups.taskCreate.form.errors = error.response.data.errors;
        });
      },
     taskEditSubmit(){
        axios.post('/edittask', this.popups.taskEdit.form).then(() =>{
          this.$refs.popupTaskEdit.popupClose();
          this.getGroups();
          this.popups.taskEdit.form.name = this.popups.taskEdit.form.points = '';
          this.popups.taskEdit.form.errors = [];

        }).catch((error) =>{
          this.popups.taskEdit.form.errors = error.response.data.errors;
        });
      },
      taskDelete(){
        axios.post('/deletetask', this.popups.taskEdit.form).then(() =>{
          this.$refs.popupTaskEdit.popupClose();
          this.getGroups();
          this.popups.taskEdit.form.name = this.popups.taskEdit.form.points = '';
          this.popups.taskEdit.form.errors = [];
        }).catch((error) =>{
          this.popups.taskEdit.form.errors = error.response.data.errors;
        });
      },
      getGroups(){
        axios.post('/getgroups')
          .then((response) => {
            this.$root.loading.loaded++;
            this.list = response.data;
        });
      },
    },
    created(){
      this.getGroups();
    }
  }
</script>
<style lang="scss" scoped>
  @import './resources/sass/_variables.scss';

  .groups__list {
    column-count: 2;
    margin-right: -5px;
  }
  .groups__item {
    overflow: hidden;
    margin-right: 5px;

    &:not(:last-child) {
      margin-bottom: 20px;
    }
  }
  .group {
    overflow: hidden;
    position: relative;

    &:hover {
      .group__actions {
        opacity: 1;
      }
      .group__actions__item:first-child {
        opacity: 0.2;
      }
    }
  }
  .group__wrap {
    overflow: hidden;
    margin-right: 40px;
  }
  .group__i {
    float: left;

    .fal {
      font-size: 26px;
    }
    .fas {
      position:absolute;
      left: 2px;
      top: 2px;
      opacity: 0.5;
      font-size: 22px;
    }
  }
  .group__t {
    margin-left: 40px;
    font-size: 24px;
    text-transform: uppercase;
    line-height: 30px;
  }
  .group__actions {
    float: right;
    opacity: 0.2;
  }
  .group__list {
    margin-top: 10px;
  }
  .group__actions__item {
    float: left;
    margin-left: 8px;

    .fal {
      color: $black;
      font-size: 20px;
      line-height: 30px;
    }
    &:first-child {
      margin-left: 0;
      opacity: 0;

      &:hover {
        opacity: 1!important;
      }
    }
  }
</style>
