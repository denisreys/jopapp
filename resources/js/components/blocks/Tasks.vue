<template>
  <div class="sb__block sb__block--tasks">
    <div class="sb__block__title">
      <span class="sb__block__title__span">Tasks</span>
    </div>
    <div class="sb__add">
      <a href="#" class="sb__add__btn" @click.prevent="$refs.popupTaskAdd.popupShow();">
        <i class="fal fa-plus"></i>
      </a>
    </div>
    <template v-if="tasks.length != 0">
      <ul class="sb__list">
        <li class="sb__list__item note" v-for="task in tasks" :key="task.id">
          <affair
            class="affair--sb"
            :id="task.id"
            :name="task.name"
            :checked="task.check_week">
            <template v-slot:actions>
              <div class="affair__actions">
                <a href="#" class="affair__actions__item affair__actions__item--edit" @click.prevent="
                  $refs.popupTaskEdit.popupShow();
                  popups.taskEdit.form.id = task.id;
                  popups.taskEdit.form.name = task.name;
                  popups.taskEdit.form.points = task.points;
                "><i class="fal fa-pencil"></i></a>
              </div>
            </template>
          </affair>
        </li>
      </ul>
    </template>
    <template v-else>
      <div class="sb__desc">
        Например:<br> - Прочитать книгу на английском языке<br> - Переехать из России
      </div>
    </template>

    <!-- ADD A TASK -->
    <v-popup
      ref="popupTaskAdd"
      :title="popups.taskAdd.title"
      :desc="popups.taskAdd.desc"
      :btnSubmitName="popups.taskAdd.btnSubmitName"
      @popupSubmit="taskAddSubmit()">
      <p v-if="popups.taskAdd.form.errors.name">{{popups.taskAdd.form.errors.name[0]}}</p>
      <p v-if="popups.taskAdd.form.errors.points">{{popups.taskAdd.form.errors.points[0]}}</p>
      <div class="input-group">
        <input type="text" class="input w-100" placeholder="What're the plans?" v-model="popups.taskAdd.form.name">
      </div>
      <div class="input-group">
        <label for="popup-affair-points" class="label label--default">Difficult</label>
        <select id="popup-affair-points" class="select" v-model="popups.taskAdd.form.points">
          <option value="10">Очень легко</option>
          <option value="20">Легко</option>
          <option value="30">Нормально</option>
          <option value="40">Сложно</option>
          <option value="50">Очень сложно</option>
        </select>
      </div>
    </v-popup>

    <!-- EDIT A TASK -->
    <v-popup
      ref="popupTaskEdit"
      :title="popups.taskEdit.title"
      :desc="popups.taskEdit.desc"
      :btnSubmitName="popups.taskEdit.btnSubmitName"
      @popupSubmit="taskEditSubmit()">
      <p v-if="popups.taskEdit.form.errors.name">{{popups.taskEdit.form.errors.name[0]}}</p>
      <p v-if="popups.taskEdit.form.errors.points">{{popups.taskEdit.form.errors.points[0]}}</p>
      <div class="input-group">
        <input type="text" class="input w-100" placeholder="What're the plans?" v-model="popups.taskEdit.form.name">
      </div>
      <div class="input-group">
        <label for="popup-affair-points" class="label label--default">Difficult</label>
        <select id="popup-affair-points" class="select" v-model="popups.taskEdit.form.points">
          <option value="10">Очень легко</option>
          <option value="20">Легко</option>
          <option value="30">Нормально</option>
          <option value="40">Сложно</option>
          <option value="50">Очень сложно</option>
        </select>
      </div>
      <template v-slot:additional>
        <a href="#" @click.prevent="taskDelete()" class="popup__footer__additional__delete">Удалить</a>
      </template>
    </v-popup>
  </div>
</template>

<script>
  import axios from 'axios';
  import affair from './Affair.vue';
  import popup from './Popup.vue';

  export default {
    components: {
      affair,
      'v-popup' : popup,
  	},
    data () {
      return {
        tasks: [],
        popups: {
          taskAdd: {
            name: 'taskAdd',
            visibility: false,
            title: 'Add a task',
            desc: 'Добавляйте большие задачи или ваши цели, чтобы они всегда были перед глазами.<br>За выполнение таска каждой из сложности вы получите в 10 раз больше поинтов, чем за обычные дела.<br><br>Примеры: Прочитать Гарри Поттера в оригинале, Запустить проект, Закончить рассказ',
            btnSubmitName: 'Добавить',
            form: {
              name: '',
              points: 30,
              errors: []
            }
          },
          taskEdit: {
            name: 'taskEdit',
            visibility: false,
            title: 'Edit a task',
            desc: 'Добавляйте большие задачи или ваши цели, чтобы они всегда были перед глазами.<br>За выполнение таска каждой из сложности вы получите в 10 раз больше поинтов, чем за обычные дела.<br><br>Примеры: Прочитать Гарри Поттера в оригинале, Запустить проект, Закончить рассказ',
            btnSubmitName: 'Сохранить',
            form: {
              id: '',
              name: '',
              points: 30,
              errors: []
            }
          },
        }
      }
    },
    methods: {
      taskAddSubmit(){
        axios.post('/api/addtask', this.popups.taskAdd.form).then((r) => {
          this.$refs.popupTaskAdd.popupClose();
          this.getTasks();
        }).catch((error) =>{
          this.popups.taskAdd.form.errors = error.response.data.errors;
        });;
      },
      taskEditSubmit(){
        axios.post('/api/edittask', this.popups.taskEdit.form).then((r) => {
          this.$refs.popupTaskEdit.popupClose();
          this.popups.taskEdit.form.id = this.popups.taskEdit.form.name = this.popups.taskEdit.form.points = '';
          this.popups.taskEdit.form.errors = [];
          this.getTasks();
        }).catch((error) =>{
          this.popups.taskAdd.form.errors = error.response.data.errors;
        });;
      },
      taskDelete(){
        axios.post('/api/deletetask', this.popups.taskEdit.form).then((r) => {
          this.$refs.popupTaskEdit.popupClose();
          this.popups.taskEdit.form.id = this.popups.taskEdit.form.name = this.popups.taskEdit.form.points = '';
          this.popups.taskEdit.form.errors = [];
          this.getTasks();
        }).catch((error) =>{
          this.popups.taskAdd.form.errors = error.response.data.errors;
        });;
      },
      getTasks(){
        axios.get('/api/gettasks').then((r) => {
          this.tasks = r.data;
        });
      }
    },
    mounted() {
      this.getTasks();
    }
  }
</script>
