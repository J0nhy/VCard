<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import UserTable from "../UserTable.vue"

const axios = inject('axios')

const router = useRouter()
const currentPage = ref(1)

const users = ref([])

const totalUsers = computed(() => {
  return users.value.length
})

const loadUsers = async () => {
  try {
    const response = await axios.get(`admins/gerir?page=${currentPage.value}`)
    users.value = response.data

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

onMounted(() => {
  loadUsers()
})

const page_changed = (page) => {

currentPage.value = page
loadUsers();
}

const clickMenuOption = () => {
  const domReference = document.getElementById('buttonSidebarExpandId')
  if (domReference) {
    if (window.getComputedStyle(domReference).display !== "none") {
      domReference.click()
    }
  }
}
</script>

<template>
  <!--button to create admin-->
  <div class="mt-5 mb-3">
    <router-link class="btn btn-success" :class="{ active: $route.name === 'NewAdmin' }" :to="{ name: 'NewAdmin' }"
      @click="clickMenuOption">
      <i class="bi bi-person-check-fill"></i>
      Create Admin
    </router-link>
  </div>

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
    @page-changed="page_changed"
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

