<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import UserTable from "./UserTable.vue"

const axios = inject('axios')

const router = useRouter()

const vcards = ref([])

const totalVcards = computed(() => {
  return vcards.value.length
})

const loadVcards = async () => {
    try {
      const response = await axios.get('vcards')
      vcards.value = response.data.data

  } catch (error) {
    //console.log(error)
  }
}

const editUser = (user) => {
  router.push({ name: 'Vcards', params: { id: user.id } })
}

onMounted (() => {
  loadUsers()
})
</script>

<template>
  <h3 class="mt-5 mb-3">Vcards</h3>
  <hr>
  <user-table
    :users="users"
    :showId="false"
    @edit="editUser"
  ></user-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 2.3rem;
}
</style>

