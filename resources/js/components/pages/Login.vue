<template>
    <div class="wrapper wrapper--auth">
        <div class="auth">
            <div class="auth__header">
                <img class="auth__header__img" src="/images/dog2.png" alt="good dog">
                <ul v-if="error" class="auth__errors">
                    <li class="auth__errors__item">{{ error }}</li>
                </ul>
            </div>
            <div class="auth__main">
                <form @submit.prevent="formSubmit">
                    <input class="input" type="text" placeholder="your login" v-model="form.login">
                    <input class="input" type="password" placeholder="your password" v-model="form.password">
                    <input class="btn btn--submit w-100" type="submit" value="Login">
                </form>
                <div class="auth__links">
                    <router-link :to="{ name: 'register' }" class="btn btn--default w-100">Register</router-link> 
                </div>
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
                error: '',
                components: 0
            }
        },
        methods: {
            formSubmit(){
                axios.get('/sanctum/csrf-cookie').then(response => {
                    axios.post('/login', this.form)
                        .then(response => {
                            if(response.data.success){
                                localStorage.setItem('token', response.data.data.token);
                                this.$router.push('/');
                            }else this.error = response.data.message;
                    });
                });
            } 
        },
        created(){
            this.$root.loading.components = this.components;
        },
        name: 'Login'
    }
</script>
<style lang="scss">
    
</style>