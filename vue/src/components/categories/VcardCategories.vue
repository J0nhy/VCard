<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import categoryTable from "./CategoryTable.vue"

const axios = inject('axios')

const router = useRouter()

const categorias = ref([])
const currentPage = ref(1) // Add this line to keep track of the current page

const totalCategorias = computed(() => {
  return categorias.value.length
})

const loadCategorias = async (search = null) => {
  try {
    
    let response;
    if (search) {
      response  = await axios.get(`categories/${search}?page=${currentPage.value}`);
    }
    else {
      response = await axios.get(`categories?page=${currentPage.value}`)
    }
    console.log(categorias);

    categorias.value = response.data

  } catch (error) {
    console.log(error)
  }
}

const deleteCategoria = async (categoria,search=null) => {
  const response = await axios.delete(`categories/` + categoria.id)
  loadCategorias(search);
}
onMounted(() => {
  loadCategorias()
})

const page_changed = (page) => {

  currentPage.value = page
  loadCategorias();
}
</script>

<template>
  <h3 class="mt-5 mb-3">Categorias</h3>
  <hr>
  <category-table :categorias="categorias" :showType="true" :showName="true" @page-changed="page_changed"
    @search="loadCategorias"
    @delete="deleteCategoria"></category-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 2.3rem;
}
</style>

