<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import categoryTable from "./CategoryTable.vue"

import Default_CategoriesNew from './NewCategory.vue'; 

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
      response = await axios.get(`admin/default_categories?search=${search}&page=${currentPage.value}`);
    }
    else {
      response = await axios.get(`admin/default_categories?page=${currentPage.value}`)
    }
    categorias.value = response.data

  } catch (error) {
    //console.log(error)
  }
}

const deleteCategoria = async (categoria, search = null) => {
  const response = await axios.delete(`admin/default_categories/` + categoria.id)
  loadCategorias(search);
}
const search = (search) => {
  currentPage.value = 1;
  loadCategorias(search);
}
const save = (categoryName, categoryType) => {
  //console.log("OLASDASD");
}
const cancel = () => {
  //console.log("olas");
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
  <h3 class="mt-5 mb-3">Default Categories</h3>
  <button class="btn btn-secondary" type="button"> <router-link class="nav-link"
      :to="{ name: 'Default_CategoriesNew' }">
      Adicionar Categoria
    </router-link>
</button>

  <hr>
  <category-table :categorias="categorias" :showType="true" :showName="true" @page-changed="page_changed"
    @delete="deleteCategoria" @search="search"></category-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 2.3rem;
}
</style>

