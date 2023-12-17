<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import transactionTable from "./TransactionTable.vue"

const axios = inject('axios')

const router = useRouter()

const transactions = ref([])
const currentPage = ref(1) // Add this line to keep track of the current page
const phone_number = ref(null);

const totalTransactions = computed(() => {
  return transactions.value.length
})

const loadTransactions = async (search=null ) => {
  try {
    let response;
    if(search) response = await axios.get(`vcard/${phone_number.value}/transactions?search=${search}&page=${currentPage.value}`)
    else response = await axios.get(`vcard/${phone_number.value}/transactions?page=${currentPage.value}`)
  
    
    transactions.value = response.data

  } catch (error) {
    //console.log(error)
  }
}

onMounted(() => {
  phone_number.value=router.currentRoute.value.params.phone_number;
  loadTransactions()
})
const show  =  (transaction) => {
  router.push({
    name: 'TransactionDetail',
    params: {
      id: transaction.id,
    },
  });
}
const search  =  (search) => {
  currentPage.value=1;
  loadTransactions(search);
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
    @search="search"
    @show="show"
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

