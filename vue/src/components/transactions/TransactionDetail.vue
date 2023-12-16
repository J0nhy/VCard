<script setup>
import { ref, watch, onMounted, inject } from "vue";
import { useRouter } from 'vue-router'
const router = useRouter()

const serverBaseUrl = inject("serverBaseUrl");
const axios = inject('axios');
const transaction = ref([]);
const categories = ref([]);
const categoryNameEditable = ref([]);
const categoryDescriptionEditable = ref([]);

const category_id = ref([]);
const description = ref([]);
const phone_number = ref(null);

const props = defineProps(['id']);
const editableCategoryName = () => {
    if (categoryNameEditable.value == 'true') categoryNameEditable.value = 'no';
    else categoryNameEditable.value = 'true';
};
const editableCategoryDescription = () => {
    if (categoryDescriptionEditable.value == 'true') categoryDescriptionEditable.value = 'no';
    else categoryDescriptionEditable.value = 'true';

};
const save = async () => {
    let categoriaID=null;
    if (category_id.value!="0")categoriaID=category_id.value;
   const response = await axios.patch(`vcard/${phone_number.value}/transactions/${props.id}`, {
    category_id: categoriaID,
    description: description.value,
  });
    loadTransaction();
};
const loadTransaction = async ()=>{
    categoryNameEditable.value = 'true';
    categoryDescriptionEditable.value = 'true';
    description.value='';
    category_id.value="0";
    phone_number.value = router.currentRoute.value.params.phone_number;

    const response = await axios.get(`vcard/${phone_number.value}/transactions/${props.id}`)
    transaction.value = await response.data.data;

    const response2 = await axios.get(`vcard/${phone_number.value}/categories`)
    categories.value = await response2.data.data;
}


onMounted( () => {
 loadTransaction();
})  
</script>

<template>
    <form class="row g-3 needs-validation" @click.prevent="" > 
        <h3 class="mt-5 mb-3">Transaction {{ props.id }}</h3>
        <hr />
        <div class="d-flex flex-wrap justify-content-between">
            <div class="w-75 pe-4">
                <div class="mb-3">
                    <label for="inputName" class="form-label">ID of the transaction</label>
                    <input disabled type="text" class="form-control" :value="transaction.id" />
                </div>
                <div class="mb-3" style="display: flex; flex-direction: column; ">
                    <label for="inputName" class="form-label">Category</label>
                    <div style="display: flex; ">
                        <input v-if="categoryNameEditable == 'true'" disabled type="text" class="form-control"
                            :value="transaction.category == null ? 'Sem Categoria' : transaction.category.name" />

                        <select  v-model="category_id" v-if="categoryNameEditable != 'true'" class="form-select" id="inputCategory">
                            <option value="0" selected>Selecione uma categoria</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <button class="btn btn-xs btn-light" @click="editableCategoryName()">
                            <i :class="categoryNameEditable == 'true' ? 'bi bi-xs bi-pencil' : 'bi bi-x'"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3" style="display: flex; flex-direction: column; ">
                    <label for="inputName" class="form-label">Description</label>
                    <div style="display: flex; ">
                        <input v-if="categoryDescriptionEditable == 'true'" disabled type="text" class="form-control"
                            :value="transaction.description == null ? '' : transaction.description" />
                        <input  v-model="description" v-if="categoryDescriptionEditable != 'true'" type="text" class="form-control"/>
                        <button class="btn btn-xs btn-light" @click="editableCategoryDescription()">
                            <i :class="categoryDescriptionEditable == 'true' ? 'bi bi-xs bi-pencil' : 'bi bi-x'"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="inputName" class="form-label">Date and Time</label>
                    <input disabled type="text" class="form-control" :value="transaction.datetime" />
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Type</label>
                    <input disabled type="text" class="form-control"
                        :value="transaction.type == 'D' ? 'Debit' : 'Credit'" />
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Value</label>
                    <input disabled type="text" class="form-control" :value="transaction.value" />
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Old Balance</label>
                    <input disabled type="text" class="form-control" :value="transaction.old_balance" />
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">New Balance</label>
                    <input disabled type="text" class="form-control" :value="transaction.new_balance" />
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Payment Type</label>
                    <input disabled type="text" class="form-control" :value="transaction.payment_type" />
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Payment Reference</label>
                    <input disabled type="text" class="form-control" :value="transaction.payment_reference" />
                </div>
            </div>
        </div>
        <div class="mt-2 d-flex justify-content-start">
            <button type="button" class="btn btn-light px-5"><router-link class="nav-link" :to="{ name: 'History' }">
                    Go back
                </router-link></button>

                <button v-if="categoryNameEditable!= 'true' || categoryDescriptionEditable!='true'" type="button" class="btn btn-light px-5" @click="save">Save</button>
        </div>
    </form>
    <br>
    <br>
</template>

<style scoped>
.total_hours {
    width: 26rem;
}
</style>
