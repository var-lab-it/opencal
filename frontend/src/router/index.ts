import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import { isAuthenticated } from '../services/auth';
import UserDashboard from "../components/UserDashboard.vue";
import MyAccount from "../components/Me/MyAccount.vue";
import BookingIndex from "../components/BookingIndex.vue";
import BookNow from "../components/BookNow.vue";
import CancelBooking from "../components/CancelBooking.vue";

const routes = [
    {name: 'login', path: '/login', component: Login },
    {name: 'booking_index', path: '/:email', component: BookingIndex},
    {name: 'book_now', path: '/:email/:slug', component: BookNow},
    {name: 'cancel_booking', path: '/event/:id/cancel/:hash', component: CancelBooking},
    {
        name: 'dashboard',
        path: '/',
        component: UserDashboard,
        meta: { requiresAuth: true }
    },
    {
        name: 'account',
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
