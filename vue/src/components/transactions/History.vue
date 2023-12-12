<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import transactionTable from "./TransactionTable.vue"

const axios = inject('axios')

const router = useRouter()

const transactions = ref([])

const totalTransactions = computed(() => {
  return transactions.value.length
})

const loadTransactions = async () => {
  try {
    const response = await axios.get('transactions/900000001')
    transactions.value = response.data.data

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
</script>

<template>


  <h3 class="mt-5 mb-3">Admins</h3>
  <hr>
  <transaction-table
    :transactions="transactions"
    :show="true"
    
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

