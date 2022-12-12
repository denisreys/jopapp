<template>
    <ul class="affairs__list">
      <li class="affairs__item">
        <div class="affairs__item__group">
          <div class="affairs__item__group__wrap">
            <div class="affairs__item__group__i"><i class="fal fa-hand-rock"></i><i class="fas fa-hand-rock"></i></div>
            <div class="affairs__item__group__t">Masturbation</div>
          </div>
          <span class="affairs__item__group__line" style=""></span>
        </div>
        <div class="affairs__item__list">
          <div class="affairs__item__list__item">
            <label class="affairs__item__list__item__check">
              <input type="checkbox" name="1" value="">
              <span class="affairs__item__list__item__check__fake"></span>
            </label>
            <div class="affairs__item__list__item__name">Cum today</div>
          </div>
        </div>
      </li>
    </ul>
</template>

<script>
  import popup from './Popup.vue';

  export default {
    components: {
      'v-popup' : popup
  	},
    data () {
      return {
        groups: [],
        popupStatus: false,
        popupForm: {
          name: '',
          color: '',
          errors: []
        },
        colors: [
          {
            name: 'yellow',
            code: 'ffe494'
          },
          {
            name: 'gray',
            code: 'bbb'
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
        ],
      }
    },
    methods: {

    }
    
  }
</script>
