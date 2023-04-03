<template>
  <div class="task">
    <slot name="actions"></slot>
    <div class="task__wrap">
      <label class="task__label">
        <input type="checkbox" class="task__check" :value="id" v-model="selected" @change.later="changeCheckTask()">
        <span class="task__fake"></span>
      </label>
      <div class="task__name">{{name}}</div>
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
      changeCheckTask(){
        axios.post('/createorupdatecheck', {task_id: this.id, date: this.date, status: this.selected}).then((r) => {
          this.$parent.$parent.updateStats();
        }).catch((error) =>{
          alert('АШИБКА')
        });
      }
    }
  }
</script>

<style lang="scss">
  @import './resources/sass/_variables.scss';
  .task {
    &:hover .task__actions__item{
      opacity: 0.2;
    }
  }
  .task__label {
    float: left;
    position: relative;

    input {
      opacity: 0;
      z-index: -1;
      position: absolute;
    }
    .task__fake {
      display: inline-block;
      border: 2px solid $black;
      border-radius: 30px;
      cursor: pointer;
      vertical-align: middle;
    }
  }
  .task__label>input:checked+.task__fake{
    background-color: $black;
  }
  .task__label>input:not(:checked)+.task__fake:hover{
    background-color: #e8e8e8;
  }
  .task__wrap {
    overflow: hidden;
  }
  .task__actions {
    float: right;

    .fal {
      color: $black;
    }
    .task__actions__item {
      opacity: 0;

      &:hover {
        opacity: 1;
      }
    }
    .task__actions__item--delete:hover .fal {
      color: red;
    }
  }
  .task--default {
    .task__fake {
      width: 10px;
      height: 10px;
    }
    .task__wrap {
      margin-right: 22px;
    }
    .task__name {
      margin-left: 25px;
    }
    &:not(:last-child) {
      margin-bottom: 8px;
    }
  }
  .task--small {
    .task__fake {
      width: 8px;
      height: 8px;
    }
    .task__wrap {
      margin-right: 20px;
    }
    .task__name {
      margin-left: 22px;
    }
    &:not(:last-child) {
      margin-bottom: 5px;
    }
  }
  .task--sb {
    .task__fake {
      width: 8px;
      height: 8px;
    }
    .task__actions {
      margin-right: -9px;

      .fal {
        font-size: 16px;
      }
    }
    .task__wrap {
      margin-right: 10px;
    }
    .task__name {
      margin-left: 20px;
    }
    &:not(:last-child) {
      margin-bottom: 5px;
    }
  }
</style>