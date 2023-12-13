<script setup>

import { useToast } from "vue-toastification"
import { useRouter } from 'vue-router'
import { ref, inject } from 'vue'
import { useUserStore } from '../../stores/user.js'

const toast = useToast()
const router = useRouter()
const userStore = useUserStore()

const axios = inject('axios')

const credentials = ref({
  username: '',
  password: ''
})
const emit = defineEmits(['login'])
const login = async () => {
  try {

    const response = await axios.post('login', credentials.value)
    sessionStorage.setItem("token", response.data.access_token);
    toast.success('User ' + credentials.value.username + ' has entered the application.')
    axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
    //axios.defaults.headers.common.Authorization = "Bearer " + sessionStorage.getItem("token")
    await userStore.loadUser()
    //console.log('userStore.user:', userStore.user)
    emit('login')
    router.push({ name: 'Dashboard' })
  }
  catch (error) {
    //console.log('error login.vue:', error)
    delete axios.defaults.headers.common.Authorization
    userStore.clearUser()
    credentials.value.password = ''
    console.log('error.response.data:', error.response.data.message)
    if(error.response.data)
      toast.error(error.response.data)
    else
      toast.error(error.response.data.message)
  }
}
</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="login">
    <h3 class="mt-5 mb-3">Login</h3>
    <hr>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputUsername" class="form-label">Username</label>
        <input type="text" class="form-control" id="inputUsername" required v-model="credentials.username">
      </div>
    </div>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputPassword" class="form-label">Password</label>
        <input type="password" class="form-control" id="inputPassword" required v-model="credentials.password">
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click="login">Login</button>
    </div>
  </form>
</template>

