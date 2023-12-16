<script setup>
import { ref, watch, computed, inject, onMounted } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'
import { useUserStore } from "../../stores/user";
import { useToast } from 'vue-toastification';
import { useRouter, onBeforeRouteLeave } from 'vue-router'

const serverBaseUrl = inject("serverBaseUrl");

const axios = inject('axios')
const props = defineProps({
  transaction: {
    type: Object,
    required: true,
  },
  inserting: {
    type: Boolean,
    default: false,
  },
  errors: {
    type: Object,
    required: false,
  },
});

const emit = defineEmits(["save", "cancel", "delete"]);

const editingTransaction = ref(props.transaction)
const userStore = useUserStore()
const toast = useToast()
const router = useRouter()
const categories = ref([]);
const loadingCategories = ref(false);



const loadCategories = async () => {
  try {
    let response;

    loadingCategories.value = true;
    // Substitua 'seu-endpoint-de-categorias' pelo endpoint real em seu backend
    response = await axios.get(`vcard/${userStore.userId}/categories`, {
      params: {
        disablePaginator: true,
      },
    });
    console.log(response.data.data);
    categories.value = response.data.data; // Assuming 'data' is the array of categories
  } catch (error) {
    console.error('Erro ao carregar categorias:', error.message);
  } finally {
    loadingCategories.value = false;
  }
};

watch(
  () => props.transaction,
  (newVcard) => {
    editingTransaction.value = newVcard
  },
  { immediate: true }
)

const save = () => {
  emit("save", editingTransaction.value);
}

const cancel = () => {
  emit("cancel", editingTransaction.value);
}

onMounted(loadCategories);



</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3" v-if="!inserting">Transaction #{{ editingTransaction.id }}</h3>
    <h3 class="mt-5 mb-3" v-if="inserting">Vcard #{{ userStore.userId }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3" v-if="inserting">
          <label for="paymentType" class="form-label">Tipo de Pagamento</label>
          <select class="form-select" :class="{ 'is-invalid': errors ? errors['payment_type'] : false }" id="paymentType"
            required v-model="editingTransaction.payment_type">
            <option value="VCARD" selected>VCARD</option>
            <option value="MBWAY">MBWAY</option>
            <option value="PAYPAL">PAYPAL</option>
            <option value="IBAN">IBAN</option>
            <option value="MB">MB</option>
            <option value="VISA">VISA</option>
          </select>
          <field-error-message :errors="errors" fieldName="payment_type"></field-error-message>
        </div>
        <div class="mb-3" v-if="inserting">
          <label for="inputPaymentReference" class="form-label">Destinatário</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['payment_reference'] : false }"
            id="inputPaymentReference" placeholder="Destinatário" required
            v-model="editingTransaction.payment_reference" />
          <field-error-message :errors="errors" fieldName="payment_reference"></field-error-message>
        </div>
        <div class="mb-3" v-if="inserting">
          <label for="inputValue" class="form-label">Valor</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['value'] : false }"
            id="inputValue" placeholder="Valor a enviar" required v-model="editingTransaction.value" />
          <field-error-message :errors="errors" fieldName="value"></field-error-message>
        </div>
        <div class="mb-3" v-if="inserting">
          <label for="inputCategory" class="form-label">Categoria</label>
          <select class="form-select" :class="{ 'is-invalid': errors ? errors['category_id'] : false }" id="inputCategory"
            required v-model="editingTransaction.category_id">
            <option v-if="loadingCategories" disabled>Carregando...</option>
            <option v-else value="" disabled>Selecione uma categoria</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          <field-error-message :errors="errors" fieldName="category_id"></field-error-message>
        </div>

        <div class="mb-3">
          <label for="inputDescription" class="form-label">Descrição</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['description'] : false }"
            id="inputDescription" placeholder="Descrição" v-model="editingTransaction.description" />
          <field-error-message :errors="errors" fieldName="description"></field-error-message>
        </div>

      </div>

    </div>
    <div class="mb-3 d-flex justify-content-start">
      <button v-if="inserting" type="button" class="btn btn-primary px-5" @click="save">Enviar</button>
      <button v-if="!inserting" type="button" class="btn btn-primary px-5" @click="save">Guardar</button>

      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
      <button type="button" class="btn btn-xs btn-light" @click="deleteClick(editingTransaction)"><i
          class="bi bi-xs bi-x-square-fill"></i></button>
    </div>

  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
