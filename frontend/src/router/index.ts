import {createRouter, createWebHashHistory} from 'vue-router'
import type {RouteRecordRaw} from 'vue-router'
import LoginView from '../views/LoginView.vue'
import HomePage from '../views/HomePage.vue'
import TeacherDashboard from '../views/TeacherDashboard.vue'
import EnsayoView from '../views/EnsayoView.vue'
import CreateExam from '../views/CreateExam.vue'
import Dashboard from '../views/Dashboard.vue'
import Historial from '../views/HistorialEnsayos.vue'
const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'login',
        component: LoginView
    },
    {
        path: '/home',
        name: 'home',
        component: HomePage,
        meta: {requireAuth: true, role: 'student'}
    },
    {
        path: '/teacher',
        name: 'teacher',
        component: TeacherDashboard,
        meta: {requireAuth: true, role: 'teacher'}
    },
    {
        path: '/ensayo',
        name: 'ensayo',
        component: EnsayoView,
        meta: {requireAuth: true, role: 'student'}

    },
    {
        path: '/create-ensayo',
        name: 'create-ensayo',
        component: CreateExam,
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        
    },
    {
        path:'/historial',
        name: 'historial',
        component: Historial,
    }

];
const router = createRouter({
    history: createWebHashHistory(),
    routes
});
router.beforeEach((to, _, next) => {
    const requireAuth = to.matched.some(record => record.meta.requireAuth);
    const requiredRole = to.matched.some(record => record.meta.role) 
        ? to.matched.find(record => record.meta.role)?.meta.role 
        : null;
    
    const userStr = localStorage.getItem('user');
    const isAuthenticated = userStr !== null;
    
    if (requireAuth && !isAuthenticated) {
        next('/');
    } else if (requiredRole && isAuthenticated) {
        const user = JSON.parse(userStr);
        if (user.role !== requiredRole) {
            // Redirigir según el rol
            next(user.role === 'teacher' ? '/teacher' : '/home');
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
