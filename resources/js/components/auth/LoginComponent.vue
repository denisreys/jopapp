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
<template>
    <div>
        <form @submit.prevent="formSubmit">
            <input type="email" placeholder="Add email" v-model="form.email">
            <input type="password" placeholder="Add password" v-model="form.password">
            <input type="submit" value="Login">
        </form>
        <p v-if="error">{{error}}</p>
    </div>
</template>
