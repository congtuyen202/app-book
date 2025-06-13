import { createRouter, createWebHistory } from 'vue-router'
import IndexHome from '../pages/Index.vue'
import Default from '../layout/Default.vue'

const routes = [
    {
        path: '/',
        component: IndexHome,
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router