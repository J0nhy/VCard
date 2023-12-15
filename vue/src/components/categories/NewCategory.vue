<script setup>
import { ref, watch, computed, inject } from "vue";

const serverBaseUrl = inject("serverBaseUrl");
const CategoryName = ref('');
const CategoryType = ref('Debit'); //valor começa com debit
const axios = inject('axios');

const props = defineProps({
  userID: {
    type: Number,
    default: false,
  },
  insertingCategory: {
    type: Boolean,
    default: false,
  },
  insertingDefaultCategory: {
    type: Boolean,
    required: false,
  },
});

//const emit = defineEmits(["save", "cancel"]);

const save = async () => {
  //emit("save", CategoryName, CategoryType);
  const response = await axios.post(`default_categories`)
}

const cancel = () => {
  emit("cancel");
}

</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
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
            <option  value="Debit">Debit</option>
            <option value="Credit">Credit</option>
          </select>
        </div>
      </div>
    </div>
    <div class="mt-2 d-flex justify-content-start">
      <button type="button" class="btn btn-primary px-5 mx-2" @click="save">Save</button>
      <!----><button type="button" class="btn btn-light px-5 mx-2" @click="cancel">Cancel</button>
    </div>
  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
