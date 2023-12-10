<script setup>
import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import UserTable from "../UserTable.vue"

const axios = inject('axios')
const router = useRouter()

const users = ref([])
const currentPage = ref(1)
const perPage = 10 // Adjust this based on your desired number of items per page

const isLoading = ref(false);
const errorOccurred = ref(false);
let searchQueryGlobal = null

const totalUsers = computed(() => {
  return users.value.length
})

const loadUsers = async () => {
  try {
    const response = await axios.get(`users?page=${currentPage.value}&per_page=${perPage}`)
    users.value = response.data.data
    // Assuming your API response includes pagination information
    // You may need to adjust this based on the actual structure of your API response
    currentPage.value = response.data.current_page
  } catch (error) {
    console.log(error)
  }
}

const editMaxDebit = async (user, newMaxDebit) => {
  const userToUpdate = user.phone_number;
  const response = await axios.patch(`users/${userToUpdate}`, {
    newMaxDebit: newMaxDebit.value,
  });

  let userrr = response.data.data;
  const updatedUsers = users.value.map(existingUser => {
    // Check if the user ID matches
    if (existingUser.phone_number === userrr.phone_number) {
      // Update the user with the information from the new user
      return { ...existingUser, ...userrr };
    }
    // If the user ID doesn't match, return the existing user as is
    return existingUser;
  });
  users.value = updatedUsers;
}

const delete_user = async (user) => {
  const userToUpdate = user.phone_number;
  const response = await axios.delete(`users/` + user.phone_number)
  let userrr = response.data.data;
  const updatedUsers = users.value.map(existingUser => {
    // Check if the user ID matches
    if (existingUser.phone_number === userrr.phone_number) {
      // Update the user with the information from the new user
      return { ...existingUser, ...userrr };
    }
    // If the user ID doesn't match, return the existing user as is
    return existingUser;
  });
  users.value = updatedUsers;

}
const updateBlockedStatus = async (user) => {
  const userToUpdate = user.phone_number;
  const response = await axios.patch(`users/block/` + user.phone_number)
  let userrr = response.data.data;
  const updatedUsers = users.value.map(existingUser => {
    // Check if the user ID matches
    if (existingUser.phone_number === userrr.phone_number) {
      // Update the user with the information from the new user
      return { ...existingUser, ...userrr };
    }
    // If the user ID doesn't match, return the existing user as is
    return existingUser;
  });
  users.value = updatedUsers;


}

const search = async (searchQuery) => {
  if (!searchQuery) return loadUsers();
  isLoading.value = true;
  try {
    const response = await axios.get(`users/${searchQuery}?page=1&per_page=${perPage}`);
    users.value = response.data.data
    currentPage.value = response.data.current_page

    // Now you can use currentItems as your collection of items on the current page

    //loadUsers()

  } catch (error) {
    errorOccurred.value = true;
  } finally {
    isLoading.value = false;
  }
};

const pageChanged = (newPage) => {
  currentPage.value = newPage
  loadUsers()
}

onMounted(() => {
  loadUsers()
  console.log(users)
})
</script>

<template>
  <div>
    <h3 class="mt-5 mb-3">V-Cards</h3>
    <hr>
    <user-table :admins="users" :showId="false" :showPhoneNumber="true" :showAdmin="false" :showEditButton="false"
      :showDeleteButton="true" :showSearchVCard="true" :showSaldo="true" :showLimiteDebito="true" :showBloqueado="true"
      :showApagado="true" :showPhoto="true" @edit="editMaxDebit" @search="search" @delete="delete_user"
      @updateBlockedStatus="updateBlockedStatus">
    </user-table>

    <!-- Pagination component -->
    <pagination :total="totalUsers" :current="currentPage" :per-page="perPage" @pagination-change="pageChanged">
    </pagination>
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
