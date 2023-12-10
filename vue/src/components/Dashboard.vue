<script setup>
import { useRouter, RouterLink, RouterView } from 'vue-router'
import { useToast } from "vue-toastification"
import { useVcardStore } from '../stores/vcard.js'
import { useAdminStore } from '../stores/admin.js'
import { useUserStore } from '../stores/user.js'

const toast = useToast()
const vcardStore = useVcardStore()
const adminStore = useAdminStore()
const userStore = useUserStore()
const router = useRouter()
</script>

<template>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h1 class="h2">Dashboard</h1>
    </div>
    <div>
      <div v-show="userStore.user && userStore.userType === 'V'">
          <h4 class="mt-3">Balance: {{ userStore.userBalance }}</h4>
        </div>
      
    </div>
    <div>
      
    </div>
</template>
  