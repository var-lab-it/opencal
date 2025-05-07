import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import { isAuthenticated } from '../services/auth';
import UserDashboard from "../components/UserDashboard.vue";
import MyAccount from "../components/Me/MyAccount.vue";

const routes = [
    { path: '/login', component: Login },
    {
        path: '/',
        component: UserDashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/account',
        component: MyAccount,
        meta: { requiresAuth: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !isAuthenticated()) {
        next('/login');
    } else {
        next();
    }
});

export default router;
