<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import UserTable from "../UserTable.vue"

const axios = inject('axios')

const router = useRouter()

const users = ref([])

const totalUsers = computed(() => {
  return users.value.length
})

const loadUsers = async () => {
    try {
    const response = await axios.get('admins/gerir')
    users.value = response.data.data

  } catch (error) {
    console.log(error)
  }
}

const editUser = (user) => {
  router.push({ name: 'Users', params: { id: user.id } })
}
const deleteAdmin = async (user) => {
    const response = await axios.delete('admins/gerir/' + user.id)
    loadUsers()

}

onMounted (() => {
  loadUsers()
})
</script>

<template>
  <h3 class="mt-5 mb-3">Admins</h3>
  <hr>
  <user-table
    :admins="users"
    :showId="true"
    :showPhoto="false"
    :showAdmin="false"
    :showEditButton="false"
    :showDeleteButton="true"
    @delete="deleteAdmin"
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

