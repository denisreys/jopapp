<template>
    <div class="container container--auth">
        <div class="auth">
            <div class="auth__header">
                <img class="auth__header__img" src="/images/dog2.png" alt="good dog">
                <ul v-if="errors" class="auth__errors">
                    <template v-for="error in errors">
                        <li class="auth__errors__item" v-for="message in error">{{ message }}</li>
                    </template>
                </ul>
            </div>
            <form class="auth__form" @submit.prevent="formSubmit">
                <input class="input" type="text" placeholder="your login" v-model="form.login"/>
                
                <input class="input" type="password" placeholder="your password" v-model="form.password"/>
                <input type="submit" class="btn btn--submit w-100" value="Register"/>
            </form>
            <div class="auth__links">
                <router-link :to="{ name: 'login' }" class="btn btn--default w-100">Login</router-link>
            </div>
            <div class="auth__info">
                Для простоты регистрации я не прошу ваш email и его подтверждение, поэтому в будущем если вы забудете логин и пароль, то восстановить доступ в аккаунт будет невозможно.
            </div>
        </div>
    </div>
</template>
<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form:  {
                    login: '',
                    password: ''
                },
                errors: [] 
            }
        },
        methods: {
            formSubmit(){
                this.errors = [];
                axios.get('/sanctum/csrf-cookie').then(response => {
                    axios.post('/register', this.form)
                        .then(response => {
                            console.log(response.data);
                            if(response.data.success){
                                if(response.data.token){
                                    localStorage.setItem('token', response.data.token);
                                    this.$router.push('/');
                                }
                                else this.$router.push('/login');
                            }
                            else this.errors = response.data.errors;
                            
                    });
                });
            } 
        },
        name: 'Register'
    }
</script>
<style lang="scss">
    @import './resources/sass/_variables.scss';

    .auth__info {
        margin-top: 25px;
        color: $light-gray;
        font-size: 16px;
    }
</style>