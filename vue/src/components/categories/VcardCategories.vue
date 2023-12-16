<script setup>

import { useRouter } from 'vue-router'
import { ref, computed, onMounted, inject } from 'vue'
import categoryTable from "./CategoryTable.vue"
import { useToast } from "vue-toastification"


const axios = inject('axios')

const toast = useToast()
const router = useRouter()

const categorias = ref([])
const currentPage = ref(1) // Add this line to keep track of the current page
const phone_number = ref(null);

const totalCategorias = computed(() => {
  return categorias.value.length
})

const loadCategorias = async (search = null) => {
  try {

    let response;
    if (search) {
      response = await axios.get(`vcard/${phone_number.value}/categories?search=${search}&page=${currentPage.value}`);
    }
    else {
      response = await axios.get(`vcard/${phone_number.value}/categories?page=${currentPage.value}`)
    }
    console.log(categorias);

    categorias.value = response.data

  } catch (error) {
    console.log(error)
  }
}
const search = (search) => {
  currentPage.value = 1;
  loadCategorias(search);
}
const deleteCategoria = async (categoria, search = null) => {
  const response = await axios.delete(`vcard/${phone_number.value}/categories/` + categoria.id)
  toast.success('Category deleted successfully!');

  loadCategorias(search);
}
onMounted(() => {
  phone_number.value = router.currentRoute.value.params.phone_number;
  loadCategorias()
})

const page_changed = (page) => {

  currentPage.value = page
  loadCategorias();
}
</script>

<template>
  <h3 class="mt-5 mb-3">Categorias</h3>
  <button class="btn btn-secondary" type="button"> <router-link class="nav-link"
      :to="{ name: 'VcardCategoriesNew' } ">
      Adicionar Categoria
    </router-link>
</button>
  <hr>
  <category-table :categorias="categorias" :showType="true" :showName="true" @page-changed="page_changed" @search="search"
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

