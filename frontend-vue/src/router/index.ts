import { createRouter, createWebHistory } from 'vue-router'
import MessageListView from '../views/MessageListView.vue';
import AddMessageView from '../views/AddMessageView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: MessageListView
    },
    {
      path: '/message-add',
      name: 'message-add',
      component: AddMessageView
    }
  ]
})

export default router
