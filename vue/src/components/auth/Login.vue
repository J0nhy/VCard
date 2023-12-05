<script setup>

import { useToast } from "vue-toastification"
import { useRouter } from 'vue-router'
import { ref, inject } from 'vue'
import { useAdminStore } from '../../stores/admin.js'

const toast = useToast()
const router = useRouter()
const userStore = useAdminStore()

const axios = inject('axios')

const credentials = ref({
  username: '',
  password: ''
})
const emit = defineEmits(['login'])
const login = async () => {
  try {

    const response = await axios.post('login', credentials.value)

    console.log('Login successful. Response:', response.data);
    sessionStorage.setItem("token", response.data.access_token);
    toast.success('Admin ' + credentials.value.username + ' has entered the application.')
    axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
    //axios.defaults.headers.common.Authorization = "Bearer " + sessionStorage.getItem("token")
    await userStore.loadAdmin()

    emit('login')
    router.back()
  }
  catch (error) {
    console.log('Login failed. Error:', error.response)
    console.log(error)
    delete axios.defaults.headers.common.Authorization
    userStore.clearAdmin()

    credentials.value.password = ''
    toast.error('Admin credentials are invalid!')
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

