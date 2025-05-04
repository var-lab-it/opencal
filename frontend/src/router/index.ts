import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import { isAuthenticated } from '../services/auth';
import StartPage from "../components/StartPage.vue";
import UserDashboard from "../components/UserDashboard.vue";

const routes = [
    { path: '/test', component: { template: '<h1>Test erfolgreich</h1>' } },
    { path: '/', component: StartPage },
    { path: '/login', component: Login },
    {
        path: '/dashboard',
        component: UserDashboard,
        beforeEnter: (to, from, next) => {
            if (isAuthenticated()) {
                next();
            } else {
                next('/login');
            }
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
