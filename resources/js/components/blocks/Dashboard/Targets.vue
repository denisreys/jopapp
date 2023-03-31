<template>
  <div class="sb__block sb__block--targets">
    <div class="sb__block__title">
      <span class="sb__block__title__span">Targets</span>
    </div>
    <div class="sb__add">
      <a href="#" class="sb__add__btn" @click.prevent="$refs.popupTargetAdd.popupShow();">
        <i class="fal fa-plus"></i>
      </a>
    </div>
    <template v-if="targets.length != 0">
      <ul class="sb__list">
        <li class="sb__list__item" v-for="target in targets" :key="target.id">
          <affair
            class="affair--sb"
            :id="target.id"
            :name="target.name"
            :checked="target.check_week">
            <template v-slot:actions>
              <div class="affair__actions">
                <a href="#" class="affair__actions__item affair__actions__item--edit" @click.prevent="
                  $refs.popupTargetEdit.popupShow();
                  popups.targetEdit.form.id = target.id;
                  popups.targetEdit.form.name = target.name;
                  popups.targetEdit.form.points = target.points;
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

    <!-- ADD A TARGET -->
    <v-popup
      ref="popupTargetAdd"
      :title="popups.targetAdd.title"
      :desc="popups.targetAdd.desc"
      :btnSubmitName="popups.targetAdd.btnSubmitName"
      @popupSubmit="targetAddSubmit()">
      <p v-if="popups.targetAdd.form.errors.name">{{popups.targetAdd.form.errors.name[0]}}</p>
      <p v-if="popups.targetAdd.form.errors.points">{{popups.targetAdd.form.errors.points[0]}}</p>
      <div class="input-group">
        <input type="text" class="input w-100" placeholder="What're the plans?" v-model="popups.targetAdd.form.name">
      </div>
      <div class="input-group">
        <label for="popup-affair-points" class="label label--default">Difficult</label>
        <select id="popup-affair-points" class="select" v-model="popups.targetAdd.form.points">
          <option value="10">Очень легко</option>
          <option value="20">Легко</option>
          <option value="30">Нормально</option>
          <option value="40">Сложно</option>
          <option value="50">Очень сложно</option>
        </select>
      </div>
    </v-popup>

    <!-- EDIT A TARGET -->
    <v-popup
      ref="popupTargetEdit"
      :title="popups.targetEdit.title"
      :desc="popups.targetEdit.desc"
      :btnSubmitName="popups.targetEdit.btnSubmitName"
      @popupSubmit="targetEditSubmit()">
      <p v-if="popups.targetEdit.form.errors.name">
        {{popups.targetEdit.form.errors.name[0]}}
      </p>
      <p v-if="popups.targetEdit.form.errors.points">
        {{popups.targetEdit.form.errors.points[0]}}
      </p>
      <div class="input-group">
        <input type="text" class="input w-100" placeholder="What're the plans?" v-model="popups.targetEdit.form.name">
      </div>
      <div class="input-group">
        <label for="popup-affair-points" class="label label--default">Difficult</label>
        <select id="popup-affair-points" class="select" v-model="popups.targetEdit.form.points">
          <option value="10">Очень легко</option>
          <option value="20">Легко</option>
          <option value="30">Нормально</option>
          <option value="40">Сложно</option>
          <option value="50">Очень сложно</option>
        </select>
      </div>
      <template v-slot:additional>
        <a href="#" 
          @click.prevent="targetDelete()" 
          class="popup__footer__additional__delete">
          Удалить
        </a>
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
        targets: [],
        popups: {
          targetAdd: {
            name: 'targetAdd',
            visibility: false,
            title: 'Add a target',
            desc: 'Добавляйте большие задачи или ваши цели, чтобы они всегда были перед глазами.<br>За выполнение таргета каждой из сложности вы получите в 10 раз больше поинтов, чем за обычные дела.<br><br>Примеры: Прочитать Гарри Поттера в оригинале, Запустить проект, Закончить рассказ',
            btnSubmitName: 'Добавить',
            form: {
              name: '',
              points: 30,
              errors: []
            }
          },
          targetEdit: {
            name: 'targetEdit',
            visibility: false,
            title: 'Edit a target',
            desc: 'Добавляйте большие задачи или ваши цели, чтобы они всегда были перед глазами.<br>За выполнение таска каждой из сложности вы получите в 10 раз больше поинтов, чем за обычные дела.<br><br>Примеры: Прочитать Гарри Поттера в оригинале, Запустить проект, Закончить рассказ',
            btnSubmitName: 'Сохранить',
            form: {
              id: '',
              name: '',
              points: 30,
              errors: []
            }
          },
        },
        loaded: false
      }
    },
    methods: {
      targetAddSubmit(){
        axios.post('/addtarget', this.popups.targetAdd.form).then((r) => {
          this.$refs.popupTargetAdd.popupClose();
          this.getTargets();
        }).catch((error) =>{
          this.popups.targetAdd.form.errors = error.response.data.errors;
        });
      },
      targetEditSubmit(){
        axios.post('/edittarget', this.popups.targetEdit.form).then((r) => {
          this.$refs.popupTargetEdit.popupClose();
          this.popups.targetEdit.form.id = this.popups.targetEdit.form.name = this.popups.targetEdit.form.points = '';
          this.popups.targetEdit.form.errors = [];
          this.getTargets();
        }).catch((error) =>{
          this.popups.targetAdd.form.errors = error.response.data.errors;
        });;
      },
      targetDelete(){
        axios.post('/deletetarget', this.popups.targetEdit.form).then((r) => {
          this.$refs.popupTargetEdit.popupClose();
          this.popups.targetEdit.form.id = this.popups.targetEdit.form.name = this.popups.targetEdit.form.points = '';
          this.popups.targetEdit.form.errors = [];
          this.getTargets();
        }).catch((error) =>{
          this.popups.targetAdd.form.errors = error.response.data.errors;
        });
      },
      getTargets(){
        axios.get('/gettargets').then((r) => {
          this.$root.loading.loaded++;
          this.targets = r.data;
        });
      }
    },
    created() {
      this.getTargets();
    }
  }
</script>