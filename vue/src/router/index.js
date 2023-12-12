import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Dashboard from "../components/Dashboard.vue"
import Login from "../components/auth/Login.vue"
import ChangePassword from "../components/auth/ChangePassword.vue"
import Admin from "../components/users/Admin.vue"
import User from "../components/users/User.vue"
import Vcard from "../components/vcards/Vcard.vue"
import vPassword from "../components/vcards/Password.vue"
import aPassword from "../components/users/Password.vue"
import Transactions from "../components/transactions/Transaction.vue"
import GerirAdmins from "../components/users/admins/GerirAdmins.vue"
import GerirUsers from "../components/users/admins/GerirUsers.vue"
import Credit from "../components/transactions/Credit.vue"
import History from "../components/transactions/History.vue"
import Statistics from "../components/statistics/Statistics.vue"
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
    /*{
      path: '/user',
      name: 'User',
      component: User,
    },*/
    {
      path: '/admin/new',
      name: 'NewAdmin',
      component: Admin,
      props: { id: -1 }
    },
    /*{
      path: '/user/:id',
      name: 'User',
      component: User,
    }, */
    {
      path: '/admin/:id',
      name: 'Admin',
      component: Admin,
      //props: true
      // Replaced with the following line to ensure that id is a number
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/admin/password/:id',
      name: 'AdminPassword',
      component: aPassword,
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
    {
      path: '/vcard/delete/:phone_number',
      name: 'VcardDelete'
      //props: true
      // Replaced with the following line to ensure that id is a number
    },
    {
      path: '/transactions/new',
      name: 'NewTransaction',
      component: Transactions,
      props: route => ({ id: -1, phone_number: parseInt(route.params.phone_number) })
    },
    {
      path: '/transactions',
      name: 'History',
      component: History,
      //props: true
      // Replaced with the following line to ensure that id is a number
      //props: route => ({ phone_number: parseInt(route.params.phone_number) })
    },
    {
      path: '/transaction/:id',
      name: 'Transaction',
      component: Transactions,
      //props: true
      // Replaced with the following line to ensure that id is a number
      props: route => ({ id: parseInt(route.params.id) })
    },
    {
      path: '/admin/gerir',
      name: 'GerirAdmins',
      component: GerirAdmins
      //props: true
      // Replaced with the following line to ensure that id is a number
    },
    {
      path: '/users',
      name: 'GerirUsers',
      component: GerirUsers
      //props: true
      // Replaced with the following line to ensure that id is a number
    },
    {
      path: '/credit/:phone_number',
      name: 'Credit',
      component: Credit,
      props: route => ({ phone_number: parseInt(route.params.phone_number) })

    },
    {
      path: '/statistics',
      name: 'Statistics',
      component: Statistics
    }

  ]
})

export default router
