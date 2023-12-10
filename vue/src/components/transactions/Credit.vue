<script setup>
import { ref, watch, computed, inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'
import { useUserStore } from "../../stores/user";
import { useVcardStore } from "../../stores/vcard";
import { useToast } from 'vue-toastification';
import { useRouter, onBeforeRouteLeave } from 'vue-router'


const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
    vcard: {
    type: Object,
    required: true,
  },
  transaction: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    required: false,
  },
  
});

const emit = defineEmits(["save", "cancel", "delete"]);

const editingTransaction = ref(props.transaction)

const vCard = ref(props.vcard)
const userStore = useUserStore()
const toast = useToast()
const router = useRouter()


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

</script>

<template>

  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3" >Credit {{ vCard.vcardId }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3" >
          <label for="paymentType" class="form-label">Tipo de Crédito</label>
          <select class="form-select" :class="{ 'is-invalid': errors ? errors['payment_type'] : false }" id="paymentType"
            required >
            <option value="VCARD" selected>VCARD</option>
            <option value="MBWAY">MBWAY</option>
            <option value="PAYPAL">PAYPAL</option>
            <option value="IBAN">IBAN</option>
            <option value="MB">MB</option>
            <option value="VISA">VISA</option>
          </select>
          <field-error-message :errors="errors" fieldName="payment_type"></field-error-message>
        </div>

        <div class="mb-3" >
          <label for="inputValue" class="form-label">Valor</label>
          <input type="text" 
          class="form-control" 
          :class="{ 'is-invalid': errors ? errors['value'] : false }"
            id="inputValue" 
            placeholder="Valor a enviar" 
            required  />
          <field-error-message :errors="errors" fieldName="value"></field-error-message>
        </div>

      </div>

    </div>
    <div class="mb-3 d-flex justify-content-start">
      <button type="button" class="btn btn-primary px-5" @click="save">Enviar</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>

  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
