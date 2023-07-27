<template>
  <ul class="nav">
    <li class="nav__item">
      <router-link :to="{ name: 'dashboard' }"  class="nav__item__link">
        <i class="nav__item__link__icon fa-light fa-house"></i> 
        <span class="nav__item__link__text">Dashboard</span>
      </router-link>
    </li>
    <li class="nav__item">
      <router-link :to="{ name: 'stats' }" class="nav__item__link">
        <i class="nav__item__link__icon fa-light fa-chart-mixed"></i>
        <span class="nav__item__link__text">Stats</span>
      </router-link>
    </li>
    <li class="nav__item">
      <a href="#" @click.prevent='logout' class="nav__item__link">
        <i class="nav__item__link__icon fa-light fa-right-from-bracket"></i> 
        <span class="nav__item__link__text">Logout</span> 
        
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
    background-color: #fff;
    padding: 0 0 15px 0;

    .nav__item {
      width: 200px;

      &:last-child .nav__item__link {
        color: red;
      }
    }
    .nav__item__link {
      color: $black;
      padding: 10px 30px;
      box-sizing: border-box;
      display: block;
      font-size: 16px;
      border-radius: 2px;

      .nav__item__link__text {
        margin-left: 4px;
      }
      .nav__item__link__icon {
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