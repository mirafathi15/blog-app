import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap/dist/js/bootstrap.js'

import App from './components/App.vue';
import AdminLogin from './pages/AdminLogin.vue';
import UserLogin from './pages/UserLogin.vue';
import AdminDashboard from './pages/AdminDashboard.vue';
import UserDashboard from './pages/UserDashboard.vue';

axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const routes = [
    { path: '/', redirect: '/users/login' },
    { path: '/admins/login', component: AdminLogin },
    { path: '/users/login', component: UserLogin },
    { 
        path: '/admins', 
        component: AdminDashboard,
        meta: { requiresAuth: true, type: 'admin' }
    },
    { 
        path: '/users', 
        component: UserDashboard,
        meta: { requiresAuth: true, type: 'user' }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Create app
const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.mount('#app');