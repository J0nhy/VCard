<script setup>
import {onMounted} from 'vue'

import { useRouter, RouterLink, RouterView } from 'vue-router'
import { useToast } from "vue-toastification"
import { useVcardStore } from './stores/vcard.js'
import { useAdminStore } from './stores/admin.js'
import { useUserStore } from './stores/user.js'
import { inject } from 'vue'


const socket = inject('socket')


import LaravelTester from '@/components/LaravelTester.vue'
import WebSocketTester from '@/components/WebSocketTester.vue'

const toast = useToast()
const vcardStore = useVcardStore()
const adminStore = useAdminStore()
const userStore = useUserStore()
const router = useRouter()
const routeName = router.currentRoute.value.name

const logout = async () => {
  if (await userStore.logout()) {
    toast.success('User has logged out of the application.')
    clickMenuOption()
    router.push({ name: 'home' })
  } else {
    toast.error('There was a problem logging out of the application!')
  }
}

const clickMenuOption = () => {
  const domReference = document.getElementById('buttonSidebarExpandId')
  if (domReference) {
    if (window.getComputedStyle(domReference).display !== "none") {
      domReference.click()
    }
  }
}





onMounted(() => {
  userStore.restoreToken()
})







</script>


<template>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top flex-md-nowrap p-0 shadow">
    <div class="container-fluid">
      <router-link class="navbar-brand col-md-3 col-lg-2 me-0 px-3 navbar-dark bg-dark" :to="{ name: 'Dashboard' }" @click="clickMenuOption">
        <img src="@/assets/logo1.svg" alt="" width="30" height="24" class="d-inline-block align-text-top">
        VCard
      </router-link>
      <button id="buttonSidebarExpandId" class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>


      <div class="collapse navbar-collapse justify-content-end">


        <div v-show="userStore.user && userStore.userType === 'V' && routeName != 'Dashboard'" class="text-white">
          <h5 class="mt-3">Saldo Atual: {{ userStore.userBalance }}€</h5>
        </div>

        <ul class="navbar-nav">


          <li class="nav-item" v-show="!userStore.user">

            <router-link class="nav-link" :class="{ active: $route.name === 'NewVcard' }" :to="{ name: 'NewVcard' }"
              @click="clickMenuOption">
              <i class="bi bi-person-check-fill"></i>
              Register
            </router-link>
          </li>
          <li class="nav-item" v-show="!userStore.user">
            <router-link class="nav-link" :class="{ active: $route.name === 'Login' }" :to="{ name: 'Login' }"
              @click="clickMenuOption">
              <i class="bi bi-box-arrow-in-right"></i>
              Login
            </router-link>
          </li>
          <li class="nav-item dropdown" v-show="userStore.user">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img :src="userStore.userPhotoUrl" class="rounded-circle z-depth-0 avatar-img " alt="avatar image">
              <span class="avatar-text">{{ userStore.userName }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <li>

                <!-- Quando as sessoes tiverem arranjadas, verificar se é Admin ou Vcard com um if e redirecionar para as respetivas paginas -->

                <router-link v-if="userStore.userType === 'A'" class="dropdown-item"
                  :class="{ active: $route.name == 'Admin' && $route.params.id == userStore.userId }"
                  :to="{ name: 'Admin', params: { id: userStore.userId } }" @click="clickMenuOption">
                  <i class="bi bi-person-square"></i>
                  Profile (Admin)

                </router-link>

                <router-link v-else-if="userStore.userType === 'V'" class="dropdown-item"
                  :class="{ active: $route.name == 'Vcard' && $route.params.phone_number == userStore.userId }"
                  :to="{ name: 'Vcard', params: { phone_number: userStore.userId } }" @click="clickMenuOption">
                  <i class="bi bi-person-square"></i>
                  Profile (Vcard)
                </router-link>
              </li>
              <li>

                <!-- Quando as sessoes tiverem arranjadas, verificar se é Admin ou Vcard com um if e redirecionar para as respetivas paginas -->

                <router-link v-if="userStore.userType === 'A'" class="dropdown-item"
                  :class="{ active: $route.name === 'aPassword' && $route.params.id == userStore.userId }"
                  :to="{ name: 'AdminPassword', params: { id: userStore.userId } }" @click="clickMenuOption">
                  <i class="bi bi-key-fill"></i>
                  Change password (Admin)
                </router-link>

                <router-link v-else-if="userStore.userType === 'V'" class="dropdown-item"
                  :class="{ active: $route.name === 'vPassword' && $route.params.phone_number == userStore.userId }"
                  :to="{ name: 'VcardPassword', params: { phone_number: userStore.userId } }" @click="clickMenuOption">
                  <i class="bi bi-key-fill"></i>
                  Change password (Vcard)
                </router-link>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" @click.prevent="logout">
                  <i class="bi bi-arrow-right"></i>Logout
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" v-if="userStore.user">
        <div class="position-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <router-link v-if="userStore.userType === 'V'" to="/transactions/new" class="nav-link">
                Enviar Dinheiro
              </router-link>
            </li>
            <li class="nav-item">
              <router-link v-if="userStore.userType === 'V'" class="nav-link"
                  :to="{ name: 'History', params: { phone_number: userStore.userId } }">
                  Transações
                </router-link>
            </li>

            <li class="nav-item">
              <router-link to="/statistics" class="nav-link">
                Estatísticas
              </router-link>
            </li>
            <li class="nav-item">
              <router-link v-if="userStore.userType === 'V'" class="nav-link"
                  :to="{ name: 'Categories', params: { phone_number: userStore.userId } }">
                Categorias
              </router-link>
            </li>
          </ul>

          <div v-show="userStore.userType === 'A'">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Adminstrator Menu</span>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item">
                <router-link to="/admins" class="nav-link">
                  Gerir Admins
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/admin/vcards" class="nav-link">
                  Gerir vcards
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/default_categories" class="nav-link">
                  Default Categorias
                </router-link>
              </li>

            </ul>
          </div>

        </div>
      </nav>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<style>
@import "./assets/dashboard.css";

.avatar-img {
  margin: -1.2rem 0.8rem -2rem 0.8rem;
  width: 3.3rem;
  height: 3.3rem;
}

.avatar-text {
  line-height: 2.2rem;
  margin: 1rem 0.5rem -2rem 0;
  padding-top: 1rem;
}

.dropdown-item {
  font-size: 0.875rem;
}

.btn:focus {
  outline: none;
  box-shadow: none;
}

#sidebarMenu {
  overflow-y: auto;
}
</style>
