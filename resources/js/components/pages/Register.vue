<template>
    <div class="container container--auth">
        <div class="auth">
            <div class="auth__header">
                <img class="auth__header__img" src="/images/dog2.jpg" alt="good dog">
                <p v-if="error">{{error}}</p>
            </div>
            <form class="auth__form" @submit.prevent="formSubmit">
                <input class="input" type="text" placeholder="Add your name" v-model="form.name"/>
                <input class="input" type="email" placeholder="Add email" v-model="form.email"/>
                <input class="input" type="password" placeholder="Add password" v-model="form.password"/>
                <input class="input" type="password" placeholder="Add password again" v-model="form.c_password"/>
                <input type="submit" class="btn btn--submit w-100" value="Register"/>
            </form>
            <div class="auth__links">
                <router-link :to="{ name: 'login' }" class="btn btn--default w-100">Login</router-link>
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
                    name: '',
                    email: '',
                    password: '',
                    c_password: ''
                },
                error: ''  
            }
        },
        methods: {
            formSubmit(){
                axios.post('/register', this.form)
                    .then(response => {
                        if(response.data){
                            localStorage.setItem('token', response.data.token);
                            this.$router.push('/');
                        }else this.error = response.data.message;
                        
                    });
            } 
        },
        name: 'Register'
    }
</script>