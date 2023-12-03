import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Dashboard from "../components/Dashboard.vue"
import Login from "../components/auth/Login.vue"
import ChangePassword from "../components/auth/ChangePassword.vue"
import User from "../components/users/User.vue"
import Users from "../components/users/Users.vue"
import Vcard from "../components/vcards/Vcard.vue"
import vPassword from "../components/vcards/Password.vue"
import uPassword from "../components/users/Password.vue"

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue')
    },
    {
      path: '/login',
      name: 'Login',
      component: Login
    },
    {
      path: '/password',
      name: 'ChangePassword',
      component: ChangePassword
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: Dashboard
    },
    {
      path: '/users',
      name: 'Users',
      component: Users,
    },
    {
      path: '/users/new',
      name: 'NewUser',
      component: User,
      props: { id: -1 }
    },
    {
      path: '/users/:id',
      name: 'Users',
      component: Users,
    }, 
    {
      path: '/user/:id',
      name: 'User',
      component: User,
      //props: true
      // Replaced with the following line to ensure that id is a number
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/user/password/:id',
      name: 'UserPassword',
      component: uPassword,
      //props: true
      // Replaced with the following line to ensure that id is a number
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/vcard/:phone_number',
      name: 'Vcard',
      component: Vcard,
      //props: true
      // Replaced with the following line to ensure that id is a number
      props: route => ({ phone_number: parseInt(route.params.phone_number) })
    }, 
    {
      path: '/vcard/new',
      name: 'NewVcard',
      component: Vcard,
      props: { id: -1 }
    },
    {
      path: '/vcard/password/:phone_number',
      name: 'VcardPassword',
      component: vPassword,
      //props: true
      // Replaced with the following line to ensure that id is a number
      props: route => ({ phone_number: parseInt(route.params.phone_number) })
    },

  ]
})

export default router
