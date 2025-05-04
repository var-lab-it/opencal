import { createRouter, createWebHistory } from 'vue-router';
import type { RouteLocationNormalized, NavigationGuardNext } from 'vue-router';
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
        beforeEnter: (
            to: RouteLocationNormalized,
            from: RouteLocationNormalized,
            next: NavigationGuardNext
        ) => {
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
