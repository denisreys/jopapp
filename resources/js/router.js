import { createWebHistory, createRouter } from 'vue-router';
import DashboardComponent from './components/DashboardComponent.vue';
import PostComponent from './components/PostComponent.vue';
import LoginComponent from './components/auth/LoginComponent.vue';
import RegisterComponent from './components/auth/RegisterComponent.vue';
import NotFoundComponent from './components/NotFoundComponent.vue';

const routes = [
    { 
        path: '/',
        name: 'dashboard',
        component: DashboardComponent,
        meta: {requiresAuth: true} 
    },
    { 
        path: '/posts',
        name: 'posts', 
        component: PostComponent,
        meta: {requiresAuth: true}
    },
    { 
        path: '/:pathMatch(.*)*', 
        name: 'notFound', 
        component: NotFoundComponent,
        meta: {requiresAuth: false}
    },
    //AUTH
    { 
        path: '/login', 
        name: 'login', 
        component: LoginComponent,
        meta: {requiresAuth: false}
    },
    { 
        path: '/register', 
        name: 'register', 
        component: RegisterComponent,
        meta: {requiresAuth: false}
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to,from) => {
    if(to.meta.requiresAuth && !localStorage.getItem('token')){
        return {name: 'Login'}
    }
    if(to.meta.requiresAuth == false && localStorage.getItem('token')){
        return {name: 'Home'}
    }
});

export default router;