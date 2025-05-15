import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import { isAuthenticated } from '../services/auth';
import BookingIndex from "../components/booking/BookingIndex.vue";
import CancelBooking from "../components/booking/CancelBooking.vue";
import Dashboard from "../components/user/Dashboard.vue";
import Account from "../components/user/Account.vue";

const routes = [
    {name: 'login', path: '/login', component: Login },
    {name: 'booking_index', path: '/:email', component: BookingIndex},
    {name: 'cancel_booking', path: '/event/:id/cancel/:hash', component: CancelBooking},
    {
        name: 'dashboard',
        path: '/',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        name: 'account',
        path: '/account',
        component: Account,
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
