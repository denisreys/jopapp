<template>
    <div>
        <form @submit.prevent="formSubmit">
            <input type="text" placeholder="Add your name" v-model="form.name"/>
            <input type="email" placeholder="Add email" v-model="form.email"/>
            <input type="password" placeholder="Add password" v-model="form.password"/>
            <input type="password" placeholder="Add password again" v-model="form.c_password"/>
            <input type="submit" value="Register"/>
        </form>
        <p v-if="error">{{error}}</p>
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
                axios.post('/api/register', this.form)
                    .then(response => {
                       /// if(response.data){
                       //     localStorage.setItem('token', response.data.token);
                       //     this.$router.push('/');
                        //}else {
                       //     this.error = response.data.message;
                       // }
                    });
            } 
        },
        name: 'RegisterComponent'
    }
</script>