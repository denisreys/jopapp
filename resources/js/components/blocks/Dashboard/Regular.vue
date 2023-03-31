<template>
  <div class="main__regular">
    <div class="main__header">
      <div class="main__header__title">Groups and Regular tasks</div>
      <div class="main__header__add">
        <a href="#" class="main__header__add__a" @click.prevent="$refs.popupGroupCreate.popupShow();"><i class="fal fa-plus"></i></a>
      </div>
    </div>
    <ul class="regular__list" v-if="list.length != 0">
      <li class="regular__item" v-for="group in list">
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
                $refs.popupAffairCreate.popupShow();
                popups.affairCreate.form.group_id = group.id;
                popups.affairCreate.form.group_name = group.name;
              "><i class="fal fa-plus"></i></a>
            </li>
          </ul>
          <div class="group__wrap">
            <div class="group__i"><i class="fal" :class="('fa-' + group.icon)"></i><i class="fas" :class="('fa-' + group.icon)" :style="{color: ('#' + group.color)}"></i></div>
            <div class="group__t">{{group.name}}</div>
          </div>
        </div>
        <div class="group__list" v-if="group.affairs.length">
          <affair
            v-for="item in group.affairs"
            :key="item.id"
            class="affair--default"
            :id="item.id"
            :name="item.name"
            :checked="item.check">
            <template v-slot:actions>
              <div class="affair__actions">
                <a href="#" class="affair__actions__item affair__actions__item--edit" @click.prevent="
                  $refs.popupAffairEdit.popupShow();
                  popups.affairEdit.form.id = item.id;
                  popups.affairEdit.form.group_id = item.group_id;
                  popups.affairEdit.form.name = item.name;
                  popups.affairEdit.form.points = item.points;
                "><i class="fal fa-pencil"></i></a>
              </div>
            </template>
          </affair>
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
      ref="popupAffairCreate"
      :title="popups.affairCreate.title"
      :desc="popups.affairCreate.desc"
      :btnSubmitName="popups.affairCreate.btnSubmitName"
      @popupSubmit="affairCreateSubmit()">
      <p v-if="popups.affairCreate.form.errors.name">{{popups.affairCreate.form.errors.name}}</p>
      <p v-if="popups.affairCreate.form.errors.points">{{popups.affairCreate.form.errors.points}}</p>
      <div class="input-group">
        <input type="text" ref="affairCreate" class="input w-100" placeholder="Чем займешься?" v-model="popups.affairCreate.form.name">
      </div>
      <div class="input-group input-group--inline">
        <div class="input-group__item">
          <label for="popup-affair-points" class="label label--default">Difficult</label>
          <select id="popup-affair-points" class="select" v-model="popups.affairCreate.form.points">
            <option value="1">Очень легко</option>
            <option value="2">Легко</option>
            <option value="3">Нормально</option>
            <option value="4">Сложно</option>
            <option value="5">Очень сложно</option>
          </select>
        </div>
        <div class="input-group__item">
          <label for="popup-affair-group" class="label label--default">Group</label>
          <select id="popup-affair-group" class="select" v-model="popups.affairCreate.form.group_id">
            <option v-for="group in list" :value="group.id">{{ group.name }}</option>
          </select>
        </div>
      </div>
    </v-popup>

    <!-- РЕДАКТИРОВАТЬ РЕГУЛЯРНОЕ ЗАНЯТИЕ -->
    <v-popup
      ref="popupAffairEdit"
      :title="popups.affairEdit.title"
      :desc="popups.affairEdit.desc"
      :btnSubmitName="popups.affairEdit.btnSubmitName"
      @popupSubmit="affairEditSubmit()">
      <p v-if="popups.affairEdit.form.errors.name">{{popups.affairEdit.form.errors.name[0]}}</p>
      <p v-if="popups.affairEdit.form.errors.points">{{popups.affairEdit.form.errors.points[0]}}</p>
      <div class="input-group">
        <input type="text" ref="affairEdit" class="input w-100" placeholder="Чем займешься?" v-model="popups.affairEdit.form.name">
      </div>
      <div class="input-group input-group--inline">
        <div class="input-group__item">
          <label for="popup-affair-points" class="label label--default">Difficult</label>
          <select id="popup-affair-points" class="select" v-model="popups.affairEdit.form.points">
            <option value="1">Очень легко</option>
            <option value="2">Легко</option>
            <option value="3">Нормально</option>
            <option value="4">Сложно</option>
            <option value="5">Очень сложно</option>
          </select>
        </div>
        <div class="input-group__item">
          <label for="popup-affair-group" class="label label--default">Group</label>
          <select id="popup-affair-group" class="select" v-model="popups.affairEdit.form.group_id">
            <option v-for="group in list" :value="group.id">{{ group.name }}</option>
          </select>
        </div>
      </div>
      <template v-slot:additional>
        <a href="#" @click.prevent="affairDelete()" class="popup__footer__additional__delete">Удалить</a>
      </template>
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
          affairCreate: {
            name: 'affairCreate',
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
          affairEdit: {
            name: 'affairEdit',
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
          this.getRegularList();
          this.popups.groupEdit.form.name = this.popups.groupEdit.form.color = '';
          this.popups.groupEdit.form.errors = [];
        }).catch((error) =>{
          this.popups.groupEdit.form.errors = error.response.data.errors;
        });
      },
      groupEditSubmit(){
        axios.post('/editgroup', this.popups.groupEdit.form).then(() =>{
          this.$refs.popupGroupEdit.popupClose();
          this.getRegularList();
          this.popups.groupEdit.form.name = this.popups.groupEdit.form.color = '';
          this.popups.groupEdit.form.errors = [];
        }).catch((error) =>{
          this.popups.groupEdit.form.errors = error.response.data.errors;
        });
      },
      groupCreateSubmit(){
        axios.post('/creategroup', this.popups.groupCreate.form).then((r) =>{
          this.$refs.popupGroupCreate.popupClose();
          this.getRegularList();
          this.popups.groupCreate.form.name = this.popups.groupCreate.form.color = '';
          this.popups.groupCreate.form.errors = [];
        }).catch((error) =>{
          this.popups.groupCreate.form.errors = error.response.data.errors;
        });
      },
      affairCreateSubmit(){
        axios.post('/createaffair', this.popups.affairCreate.form).then((r) =>{
          this.$refs.popupAffairCreate.popupClose();
          this.getRegularList();
          this.popups.affairCreate.form.name = '';
          this.popups.affairCreate.form.points = 3;
          this.popups.affairCreate.form.errors = [];
        }).catch((error) =>{
          this.popups.affairCreate.form.errors = error.response.data.errors;
        });
      },
      affairEditSubmit(){
        axios.post('/editaffair', this.popups.affairEdit.form).then(() =>{
          this.$refs.popupAffairEdit.popupClose();
          this.getRegularList();
          this.popups.affairEdit.form.name = this.popups.affairEdit.form.points = '';
          this.popups.affairEdit.form.errors = [];

        }).catch((error) =>{
          this.popups.affairEdit.form.errors = error.response.data.errors;
        });
      },
      affairDelete(){
        axios.post('/deleteaffair', this.popups.affairEdit.form).then(() =>{
          this.$refs.popupAffairEdit.popupClose();
          this.getRegularList();
          this.popups.affairEdit.form.name = this.popups.affairEdit.form.points = '';
          this.popups.affairEdit.form.errors = [];
        }).catch((error) =>{
          this.popups.affairEdit.form.errors = error.response.data.errors;
        });
      },
      getRegularList(){
        axios.get('/getregular')
          .then((response) => {
            this.$root.loading.loaded++;
            this.list = response.data;
        });
      },
    },
    created(){
      this.getRegularList();
    }
  }
</script>
<style lang="scss" scoped>
  @import './resources/sass/_variables.scss';

  .regular__list {
    column-count: 2;
    margin-right: -5px;
  }
  .regular__item {
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
