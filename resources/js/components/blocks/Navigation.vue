<template>
  <ul class="nav">
    <li class="nav__item">
      <router-link :to="{ name: 'dashboard' }"  class="nav__item__link">
        <i class="fa-light fa-house"></i> Dashboard
      </router-link>
    </li>
    <li class="nav__item">
      <router-link :to="{ name: 'stats' }" class="nav__item__link">
        <i class="fa-light fa-chart-mixed"></i>
        Stats
      </router-link>
    </li>
    <!--<li class="nav__item">
      <router-link :to="{ name: 'settings' }" @click="loading()" class="nav__item__link">
        <i class="fa-light fa-gear"></i> Settings
      </router-link>
    </li>-->
    <li class="nav__item">
      <a href="#" @click.prevent='logout' class="nav__item__link">
        <i class="fa-light fa-right-from-bracket"></i> Logout
      </a>
    </li>
  </ul>
</template>
<script>
    export default {
      methods: {
        logout(){
            localStorage.removeItem('token');
            this.$router.push('/login')
        }
      }
    }
</script>
<style lang="scss">
  @import './resources/sass/_variables.scss';

  .nav {
    position: fixed;
    bottom: 0;
    padding: 15px 0;

    .nav__item {
      &:last-child .nav__item__link {
        color: red;
      }
    }
    .nav__item__link {
      color: $black;
      padding: 10px 30px;
      box-sizing: border-box;
      width: 200px;
      display: block;
      font-size: 16px;
      border-radius: 2px;

      i {
        width: 22px;
      }
    }
    .router-link-active {
      border-left: solid 4px $main;
      margin-left: -4px;
      background: $main-light;
    }
  }
</style>