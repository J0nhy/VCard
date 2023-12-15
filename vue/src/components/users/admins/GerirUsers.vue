<script setup>
import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import UserTable from "../UserTable.vue"

const axios = inject('axios')
const router = useRouter()

const users = ref([])
const currentPage = ref(1)

const isLoading = ref(false);
const errorOccurred = ref(false);

const totalUsers = computed(() => {
  return users.value.length
})

const loadUsers = async (search = null) => {
  try {
    let response;
    if (search) {
      response = await axios.get(`vcards?search=${search}&page=${currentPage.value}`);
    }
    else {
      response = await axios.get(`vcards?page=${currentPage.value}`)
    }
    users.value = response.data
  } catch (error) {
    console.log(error)
  }
}
const search  =  (search) => {
  currentPage.value=1;
  loadUsers(search);
}
const editMaxDebit = async (user, newMaxDebit) => {
  const response = await axios.patch(`vcards/${user.phone_number}`, {
    newMaxDebit: newMaxDebit.value,
  });

  updateTable(response.data.data);
}

const delete_user = async (user) => {
  const response = await axios.delete(`vcards/` + user.phone_number)
  updateTable(response.data.data);
}
const updateBlockedStatus = async (user) => {
  const response = await axios.patch(`vcards/${user.phone_number}`, {
    block: "yes",
  });
  updateTable(response.data.data);
}
const updateTable = (user) => {
  const updatedUsers = users.value.data.map(existingUser => {
    // Check if the user ID matches
    if (existingUser.phone_number === user.phone_number) {
      // Update the user with the information from the new user
      return { ...existingUser, ...user };
    }
    // If the user ID doesn't match, return the existing user as is
    return existingUser;
  });
  users.value.data = updatedUsers;
}

const page_changed = (page, searchQuery) => {

  currentPage.value = page
  loadUsers(searchQuery);
}

onMounted(() => {
  loadUsers()
})
</script>

<template>
  <div>
    <h3 class="mt-5 mb-3">V-Cards</h3>
    <hr>
    <user-table :admins="users" :showId="false" :showPhoneNumber="true" :showAdmin="false" :showEditButton="false"
      :showDeleteButton="true" :showSearchVCard="true" :showSaldo="true" :showLimiteDebito="true" :showBloqueado="true"
      :showApagado="true" :showPhoto="true" @edit="editMaxDebit" @search="search" @delete="delete_user"
      @updateBlockedStatus="updateBlockedStatus" @page-changed="page_changed">
    </user-table>
  </div>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 2.3rem;
}
</style>
