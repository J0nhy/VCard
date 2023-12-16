<script setup>
import { ref, watch, onMounted, inject } from "vue";
import { useRouter } from 'vue-router'
const router = useRouter()

const serverBaseUrl = inject("serverBaseUrl");
const axios = inject('axios');
const transaction = ref([])
const phone_number = ref(null);

const props = defineProps(['id']);

onMounted(async ()  => {
    phone_number.value=router.currentRoute.value.params.phone_number;

    const response = await axios.get(`vcard/${phone_number.value}/transactions/${props.id}`)
    transaction.value= await response.data.data;
})  
</script>

<template>
    <form class="row g-3 needs-validation" @submit.prevent="save">
        <h3 class="mt-5 mb-3">Transaction {{props.id}}</h3>
        <hr />
        <div class="d-flex flex-wrap justify-content-between">
            <div class="w-75 pe-4">
                <div class="mb-3">
                    <label for="inputName" class="form-label">ID of the transaction</label>
                    <input disabled type="text" class="form-control" :value="transaction.id" />
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Category Name</label>
                    <input disabled type="text" class="form-control" :value="transaction.category == null ? 'Sem Categoria' : transaction.category.name" />
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Date and Time</label>
                    <input disabled type="text" class="form-control" :value="transaction.datetime"/>
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Type</label>
                    <input disabled type="text" class="form-control" :value="transaction.type=='D' ? 'Debit' : 'Credit'"/>
                </div><div class="mb-3">
                    <label for="inputName" class="form-label">Value</label>
                    <input disabled type="text" class="form-control" :value="transaction.value"/>
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Old Balance</label>
                    <input disabled type="text" class="form-control" :value="transaction.old_balance"/>
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">New Balance</label>
                    <input disabled type="text" class="form-control" :value="transaction.new_balance"/>
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Payment Type</label>
                    <input disabled type="text" class="form-control" :value="transaction.payment_type"/>
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Payment Reference</label>
                    <input disabled type="text" class="form-control" :value="transaction.payment_reference"/>
                </div>
            </div>
        </div>
        <div class="mt-2 d-flex justify-content-start">
            <button  type="button" class="btn btn-primary px-5 mx-2"><router-link
                    class="nav-link" :to="{ name: 'History' }">
                    Go back
                </router-link></button>
    

        </div>
    </form>
</template>

<style scoped>
.total_hours {
    width: 26rem;
}
</style>
