<template>
  <div class="popup" :class="('js-popup--' + target)" v-show="visibility">
    <div class="popup__window">
      <div class="popup__header">
        <div class="popup__title">{{title}}</div>
        <div class="popup__close" @click="popupClose()"><i class="fal fa-times"></i></div>
      </div>
      <div class="popup__container">
        <div class="popup__desc" v-html="desc"></div>
        <slot></slot>
      </div>
      <div class="popup__footer">
        <div class="popup__footer__additional">
          <slot name="additional"></slot>
        </div>
        <div class="popup__footer__btns">
          <button class="btn btn--default" @click.prevent="popupClose()">Отменить</button>
          <button class="btn btn--submit" @click.prevent="$emit('popupSubmit')">{{ btnSubmitName }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  export default {
    data(){
      return {
        visibility: false
      }
    },
    props: {
      title: String,
      desc: String,
      target: String,
      btnSubmitName: String
    },
    methods: {
      popupShow(){
        this.visibility = true;
      },
      popupClose(){
        this.visibility = false;
      },
    }
  }
</script>
<style lang="scss">
  @import '../../../sass/_variables.scss';

  .popup {
    background-color: #5953428f;
    width: 100%;
    height: 100%;
    z-index: 5;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    overflow: auto;
  }
  .popup__window {
    width: 600px;
    margin: auto;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0px 2px 18px #4d4b43;
    box-sizing: border-box;
    position: relative;
  }
  .popup__header {
    padding: 25px 30px;
    background: $main;
  }
  .popup__container {
    padding: 30px;
  }
  .popup__footer {
    border-top: solid 1px $border;
    padding: 20px 30px;
    overflow: hidden;

    .btn {
      float: left;
    }
  }
  .popup__footer__additional {
    float: left;
  }
  .popup__footer__additional__delete {
    color: red;
    line-height:42px;
    text-decoration: none;
  }
  .popup__footer__btns {
    float: right;

    .btn {
      margin-left: 10px;
    }
  }
  .popup__title {
    font-size: 16px;
    letter-spacing: 1.3px;
    text-transform: uppercase;
  }
  .popup__desc {
    font-size: 14px;
    color: $light-gray;
    margin-bottom: 20px;
  }
  .popup__close {
    position: absolute;
    right: 20px;
    top: 20px;

    .fal {
      font-size: 22px;
      display: block;
      line-height: 30px;
      width: 30px;
      text-align: center;
      cursor: pointer;
    }
  }
</style>
