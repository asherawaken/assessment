import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/journal-entries',
      name: 'journal-entries',
      component: () => import('../views/JournalEntriesView.vue'),
    },
    {
      path: '/trial-balance',
      name: 'trial-balance',
      component: () => import('../views/TrialBalanceView.vue'),
    },
  ],
})

export default router
