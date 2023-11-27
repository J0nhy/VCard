//import './assets/main.css'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-icons/font/bootstrap-icons.css"
import "bootstrap"

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import { io } from 'socket.io-client'

import Toast from "vue-toastification"
// Import the Toast CSS (or use your own)!
import "vue-toastification/dist/index.css"

import FieldErrorMessage from './components/global/FieldErrorMessage.vue'
import ConfirmationDialog from './components/global/ConfirmationDialog.vue'

import App from './App.vue'
import router from './router'

const app = createApp(App)

const apiDomain = import.meta.env.VITE_API_DOMAIN
console.log('apiDomain', apiDomain)
const wsConnection = import.meta.env.VITE_WS_CONNECTION

app.provide('socket', io(wsConnection))

app.provide(
  'axios',
  axios.create({
    baseURL: apiDomain + '/api',
    headers: {
      'Content-type': 'application/json'
    }
  })
)
app.provide('serverBaseUrl', apiDomain)

// Default/Global Toast configuration
app.use(Toast, {
    position: "top-center",
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: true,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false
})

app.use(createPinia())
app.use(router)

app.component('FieldErrorMessage', FieldErrorMessage)
app.component('ConfirmationDialog', ConfirmationDialog)

app.mount('#app')
