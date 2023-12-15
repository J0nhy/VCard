<script setup>
import { ref, watch, onMounted, inject } from "vue";
import { useToast } from "vue-toastification"
import { useRouter } from 'vue-router'
const router = useRouter()

const serverBaseUrl = inject("serverBaseUrl");
const CategoryName = ref('');
const CategoryType = ref('D'); //valor começa com debit
const axios = inject('axios');
const toast = useToast()

const props = defineProps({
  insertingCategory: {
    type: Boolean,
    default:false,
  },
  insertingDefaultCategory: {
    type: Boolean,
    default:false,
  },

});

//const emit = defineEmits(["save", "cancel"]);

const save = async () => {
  try {
    let response;
    //se for para adicionar aos default category
    if (props.insertingDefaultCategory) {
      response = await axios.post(`default_categories`, {
        name: CategoryName.value,
        type: CategoryType.value,
      });
    }
    //aqui adiciona uma categoria ao user
    else{
      const phone_number=router.currentRoute.value.params.phone_number;
      response = await axios.post(`vcard/${phone_number}/categories`, {
        name: CategoryName.value,
        type: CategoryType.value,
      });
    }
    toast.success("Categoria Criada!");

  } catch {
    // Handle request errors (e.g., network error, server error)
    toast.error("Erro ao criar categoria");
  }
}

const cancel = () => {
  emit("cancel");
}

</script>

<template>
  <form class="row g-3 needs-validation" @submit.prevent="save">
    <h3 class="mt-5 mb-3">{{ insertingDefaultCategory == true ? 'New Default Category' : 'New Category' }} </h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input v-model="CategoryName" type="text" class="form-control" placeholder="Category Name" required />
        </div>

        <div class="mb-3 px-1">
          <label for="inputEmail" class="form-label">Category</label>
          <select v-model="CategoryType" class="form-control" id="inputCategory" required>
            <option value="D">Debit</option>
            <option value="C">Credit</option>
          </select>
        </div>
      </div>
    </div>
    <div class="mt-2 d-flex justify-content-start">
      <button v-if="insertingDefaultCategory" type="button" class="btn btn-light px-5 mx-2"><router-link class="nav-link"
          :to="{ name: 'Default_Categories' }">
          Go back
        </router-link></button>
      <button v-if="insertingCategory" type="button" class="btn btn-light px-5 mx-2"><router-link class="nav-link"
          :to="{ name: 'Categories' }">
          Go back
        </router-link></button>
      <button type="button" class="btn btn-primary px-5 mx-2" @click="save">Save</button>

    </div>
  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
