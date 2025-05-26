import {createRouter, createWebHistory} from 'vue-router';
import Login from '../components/dashboard/Login.vue';
import {isAuthenticated} from '../services/auth';
import BookingIndex from "../components/booking/BookingIndex.vue";
import CancelBooking from "../components/booking/CancelBooking.vue";
import Dashboard from "../components/dashboard/Dashboard.vue";
import Account from "../components/dashboard/Account.vue";
import Start from "../components/Start.vue";
import PasswordReset from "../components/PasswordReset.vue";

const routes = [
    {
        name: 'start',
        path: '/',
        component: Start,
    },
    {
        name: 'login',
        path: '/dashboard/login',
        component: Login
    },
    {
        name: 'password_reset',
        path: '/password/reset',
        component: PasswordReset
    },
    {
        name: 'booking_index',
        path: '/:email',
        component: BookingIndex
    },
    {
        name: 'cancel_booking',
        path: '/event/:id/cancel/:hash',
        component: CancelBooking
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: Dashboard,
        meta: {requiresAuth: true}
    },
    {
        name: 'account',
        path: '/dashboard/account',
        component: Account,
        meta: {requiresAuth: true}
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !isAuthenticated()) {
        next('/dashboard/login');
    } else {
        next();
    }
});

export default router;
