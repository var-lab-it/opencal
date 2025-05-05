import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import { isAuthenticated } from '../services/auth';
import UserDashboard from "../components/UserDashboard.vue";
import Teams from "../components/Me/Teams.vue";

const routes = [
    { path: '/login', component: Login },
    {
        path: '/',
        component: UserDashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/teams',
        component: Teams,
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
