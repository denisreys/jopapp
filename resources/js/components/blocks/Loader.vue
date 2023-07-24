<template>
    <div class="loader">
        <div class="loader__container">
            <div class="loader__container__information">
                <div class="loader__container__information__wrap">
                    <div class="loader__icon">
                        <i class="fa-light" :class="selected.icon"></i>
                    </div>
                    <div class="loader__text">{{ selected.text }}</div>
                </div>
            </div>
            <div class="loader__loading">
                <div class="loader__loading__wrap">
                    <div class="loader__loading__progress" :style="{width: width+'%'}"></div>
                </div>
            </div>
        </div>
    </div>
  </template>
  <script>
      export default {
        props: {
            loading: Object
        },
        data () {
          return {
            list: [
                {
                    icon: 'fa-circle-exclamation', 
                    text: 'Можно обновить сложность повторяющихся дел, если со временем они для вас стали проще или труднее.'},
                {
                    icon: 'fa-circle-exclamation', 
                    text: 'Однажды покурив канабис вы когда-нибудь умрете.'},
                {
                    icon: 'fa-circle-exclamation', 
                    text: 'Если посмотреть на канабис через микроскоп, то можно увидеть много микроскопических ножей и умереть.'},
                {
                    icon: 'fa-circle-exclamation', 
                    text: 'Выполненый таргет будет оставаться видимым в течении 7 дней, чтобы у вас было время осознать свою крутизну.'
                },
                {
                    icon: 'fa-circle-question', 
                    text: 'Заметки могут помочь вам, не забыть что-то важное.'
                },
            ]
          }
        },
        methods: {
            getAlmostRandomKey(){
                let key = Math.floor(Math.random() * (this.list.length)); 

                if(this.loading.lastKey == key)
                    return this.getAlmostRandomKey();

                return key;
            }
        },
        computed: {
            selected(){
                let key = this.getAlmostRandomKey();
                this.loading.lastKey = key;

                return this.list[key];
            },
            width(){
                let response = (((this.loading.loaded + 1) / this.loading.components)*100);
                if(response > 100) 
                    response = 100;
                
                return response;
            }
        }
      }
  </script>
  <style lang="scss">
    @import './resources/sass/_variables.scss';

    @keyframes loadingRender {
        0% {
            width: 0%;
        }
    }
    .loader {
        opacity: 1;
        position:fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        background-color: #fffdfdf7;
        box-sizing: border-box;
        padding: 15px;
    }
    .loader__container {
        margin: auto;
        width: 400px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }
    .loader__container__information {
        flex: 1;
        height: 100%;
        align-items: center;
        display: flex;
        text-align: center;
    }
    .loader__icon {
        margin-bottom: 15px;

        i {
            font-size:26px;
            color: $light-gray-icon;
        }
    }
    .loader__loading {
        margin: 50px 0;
    }
    .loader__loading__wrap {
        border-radius: 5px;
        background-color: $main-light;
        margin: 0 13%;
        height: 10px;
    }
    .loader__loading__progress {
        border-radius: 5px;
        background-color: $main;
        height: 100%;
        width: 0%;
        transition: width 0.8s;
        animation-name: loadingRender;
        animation-duration: 0.4s; 
    }
  </style>