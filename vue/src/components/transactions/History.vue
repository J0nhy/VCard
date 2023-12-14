<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import transactionTable from "./TransactionTable.vue"

const axios = inject('axios')

const router = useRouter()

const transactions = ref([])
const currentPage = ref(1) // Add this line to keep track of the current page

const totalTransactions = computed(() => {
  return transactions.value.length
})

const loadTransactions = async () => {
  try {
    const response = await axios.get(`transactions?page=${currentPage.value}`) // Use currentPage
    transactions.value = response.data

  } catch (error) {
    console.log(error)
  }
}

const deleteAdmin = async (user) => {
    const response = await axios.delete('admins/gerir/' + user.id)
    loadUsers()

}

onMounted(() => {
  loadTransactions()
})

const clickMenuOption = () => {
  const domReference = document.getElementById('buttonSidebarExpandId')
  if (domReference) {
    if (window.getComputedStyle(domReference).display !== "none") {
      domReference.click()
    }
  }
}
const page_changed = (page) => {

  currentPage.value = page
  loadTransactions();
}
</script>

<template>


  <h3 class="mt-5 mb-3">Transações</h3>
  <hr>
  <transaction-table
    :transactions="transactions"
    :show="true"
    @page-changed="page_changed"
  ></transaction-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 2.3rem;
}
</style>

