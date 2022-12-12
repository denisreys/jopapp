<template>
  <div class="affair">
    <slot name="actions"></slot>
    <div class="affair__wrap">
      <label class="affair__label">
        <input type="checkbox" class="affair__check" :value="id" v-model="selected" @change.later="changeCheckAffair()">
        <span class="affair__fake"></span>
      </label>
      <div class="affair__name">{{name}}</div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  
  export default {
    props: {
      id: Number,
      date: String,
      name: String,
      checked: Object
    },
    data(){
      return {
        selected: this.checked != null
      }
    },
    methods: {
      changeCheckAffair(){
        axios.post('/api/createorupdatecheck', {affair_id: this.id, date: this.date, status: this.selected}).then((r) => {
          //this.$root.$children['0'].$refs.total.statListUpdate(this.selected);
        }).catch((error) =>{
          alert('АШИБКА')
        });
      }
    }
  }
</script>

<style lang="scss">
  @import '../../../sass/_variables.scss';
  .affair {
    &:hover .affair__actions__item{
      opacity: 0.2;
    }
  }
  .affair__label {
    float: left;
    position: relative;

    input {
      opacity: 0;
      z-index: -1;
      position: absolute;
    }
    .affair__fake {
      display: inline-block;
      border: 2px solid $black;
      border-radius: 30px;
      cursor: pointer;
      vertical-align: middle;
    }
  }
  .affair__label>input:checked+.affair__fake{
    background-color: $black;
  }
  .affair__label>input:not(:checked)+.affair__fake:hover{
    background-color: #e8e8e8;
  }
  .affair__wrap {
    overflow: hidden;
  }
  .affair__actions {
    float: right;

    .fal {
      color: $black;
    }
    .affair__actions__item {
      opacity: 0;

      &:hover {
        opacity: 1;
      }
    }
  }
  .affair--default {
    .affair__fake {
      width: 10px;
      height: 10px;
    }
    .affair__wrap {
      margin-right: 22px;
    }
    .affair__name {
      margin-left: 25px;
    }
    &:not(:last-child) {
      margin-bottom: 8px;
    }
  }
  .affair--small {
    .affair__fake {
      width: 8px;
      height: 8px;
    }
    .affair__wrap {
      margin-right: 20px;
    }
    .affair__name {
      margin-left: 22px;
    }
    &:not(:last-child) {
      margin-bottom: 5px;
    }
  }
  .affair--sb {
    .affair__fake {
      width: 8px;
      height: 8px;
    }
    .affair__actions {
      margin-right: -9px;

      .fal {
        font-size: 16px;
      }
    }
    .affair__wrap {
      margin-right: 10px;
    }
    .affair__name {
      margin-left: 20px;
    }
    &:not(:last-child) {
      margin-bottom: 5px;
    }
  }

</style>
