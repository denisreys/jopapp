<template>
    <div class="container container--auth">
        <div class="auth">
            <div class="auth__header">
                <img class="auth__header__img" src="/images/dog.png" alt="good dog">
            </div>
            <div class="auth__main">
                <p v-if="error">{{error}}</p>
                <form @submit.prevent="formSubmit">
                    <input class="input" type="email" placeholder="Add email" v-model="form.email">
                    <input class="input" type="password" placeholder="Add password" v-model="form.password">
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
                    email: '',
                    password: ''
                },
                error: ''  
            }
        },
        methods: {
            formSubmit(){
                axios.post('/api/login', this.form)
                    .then(response => {
                        if(response.data.success){
                            localStorage.setItem('token', response.data.token);
                            this.$router.push('/');
                        }else {
                            this.error = response.data.message;
                        }
                    });
            } 
        },
        name: 'LoginComponent'
    }
</script>