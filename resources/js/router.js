import { createWebHistory, createRouter } from 'vue-router';
import Dashboard from './components/pages/Dashboard.vue';
import Stats from './components/pages/Stats.vue';
import Login from './components/pages/Login.vue';
import Register from './components/pages/Register.vue';
import NotFound from './components/pages/NotFound.vue';

const routes = [
    { 
        path: '/',
        name: 'dashboard',
        component: Dashboard,
        meta: {requiresAuth: true} 
    },
    { 
        path: '/stats',
        name: 'stats', 
        component: Stats,
        meta: {requiresAuth: true}
    },
    { 
        path: '/settings',
        name: 'settings', 
        component: Stats,
        meta: {requiresAuth: true}
    },
    { 
        path: '/:pathMatch(.*)*', 
        name: 'notFound', 
        component: NotFound,
        meta: {requiresAuth: false}
    },
    //AUTH
    { 
        path: '/login', 
        name: 'login', 
        component: Login,
        meta: {requiresAuth: false}
    },
    { 
        path: '/register', 
        name: 'register', 
        component: Register,
        meta: {requiresAuth: false}
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to,from) => {
    if(to.meta.requiresAuth && !localStorage.getItem('token'))
        return {name: 'login'}
    if(to.meta.requiresAuth == false && localStorage.getItem('token'))
        return {name: 'dashboard'}
});

export default router;