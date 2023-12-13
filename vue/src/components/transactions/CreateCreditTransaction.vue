<script setup>
import { ref, watch, computed, inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'
import { useUserStore } from "../../stores/user";
import { useToast } from 'vue-toastification';
import { useRouter, onBeforeRouteLeave } from 'vue-router'

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  transactionCredit: {
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
const editingTransaction = ref(props.transactionCredit)
const userStore = useUserStore()
const toast = useToast()
const router = useRouter()


watch(
  () => props.transactionCredit,
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

</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3" v-if="inserting">Vcard #{{ editingTransaction.phone_number }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3" v-if="inserting">
          <label for="paymentType" class="form-label">Tipo de Pagamento</label>
          <select class="form-select" :class="{ 'is-invalid': errors ? errors['payment_type'] : false }" id="paymentType"
            required v-model="editingTransaction.payment_type">
            <option value="MBWAY" selected>MBWAY</option>
            <option value="PAYPAL">PAYPAL</option>
            <option value="IBAN">IBAN</option>
            <option value="MB">MB</option>
            <option value="VISA">VISA</option>
          </select>
          <field-error-message :errors="errors" fieldName="payment_type"></field-error-message>
        </div>
        <div class="mb-3" v-if="inserting">
          <label for="inputPaymentReference" class="form-label">Referência de pagamento</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['payment_reference'] : false }"
            id="inputPaymentReference" placeholder="Referência de pagamento" required
            v-model="editingTransaction.payment_reference" />
          <field-error-message :errors="errors" fieldName="payment_reference"></field-error-message>
        </div>
        <!--
        <div class="mb-3" >
          <label for="inputCategory_id" class="form-label">ID da Categoria</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['category_id'] : false }"
            id="inputCategory_id" placeholder="ID da Categoria" v-model="editingTransaction.category_id" />
          <field-error-message :errors="errors" fieldName="category_id"></field-error-message>
        </div>

        <div class="mb-3" >
          <label for="inputDescription" class="form-label">Descrição</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['description'] : false }"
            id="inputDescription" placeholder="Descrição" v-model="editingTransaction.description" />
          <field-error-message :errors="errors" fieldName="description"></field-error-message>
        </div>-->
        <div class="mb-3" v-if="inserting">
          <label for="inputValue" class="form-label">Valor</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['value'] : false }"
            id="inputValue" placeholder="Valor a enviar" required v-model="editingTransaction.value" />
          <field-error-message :errors="errors" fieldName="value"></field-error-message>
        </div>

        <input type="hidden" class="form-control" id="hiddenPhone" required v-model="editingTransaction.phone_number" />


      </div>

    </div>
    <div class="mb-3 d-flex justify-content-start">
      <button type="button" class="btn btn-primary px-5" @click="save">Enviar</button>
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

