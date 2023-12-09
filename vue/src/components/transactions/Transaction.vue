<script setup>
import { useToast } from "vue-toastification"
import { ref, watch, inject } from 'vue'
import CreateTransaction from "./CreateTransaction.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "../../stores/user"

const toast = useToast()
const router = useRouter()
const userStore = useUserStore()

const axios = inject('axios')

const props = defineProps({
    phone_number: {
      type: Number,
      default: null
    },
    id: {
      type: Number,
      default: null
    },

})

const newTransaction = () => {
    return {
      phone_number: props.phone_number,
      vcard: '',
      type: 'D',
      value: '',
      old_balance: '',
      new_balance: '',
      payment_type: '',
      payment_reference: '',
      pair_transaction: '',
      pair_vcard: '',
      category_id: '',
      description: '',
    }
}

const transaction = ref(newTransaction())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const inserting = (id) => !id || (id < 0)
const loadTransaction = async () => {
  originalValueStr = ''
  console.log("loadTransaction -> id: ", props.id + " | phone_number: " + userStore.userId)
  errors.value = null
  if (!props.id || (props.id < 0)) {
    transaction.value = newTransaction()
  } else {
      try {
        const response = await axios.get('transaction/' + props.id)
        transaction.value = response.data.data
        originalValueStr = JSON.stringify(transaction.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const routeName = router.currentRoute.value.name;
console.log("Rota Atual:", routeName);

const save = async (transactionToSave) => {
  errors.value = null
  console.log(transactionToSave)
  console.log(props.id)
  if (routeName == 'NewTransaction') {
    try {
      const response = await axios.post('transaction', transactionToSave)
      transaction.value = response.data.data
      originalValueStr = JSON.stringify(transaction.value)
      toast.success('Transaction #' + transaction.value.id + ' was registered successfully.')
      router.push({name: 'Dashboard'})
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('VCard was not registered due to validation errors!')
      } else {
        toast.error('Transaction was not registered due to unknown server error!')
      }
    }
  } else {
    try {
      const response = await axios.put('transaction/' + props.id, transactionToSave)
      transaction.value = response.data.data
      originalValueStr = JSON.stringify(transaction.value)
      toast.success('Transaction #' + transaction.value.id + ' was updated successfully.')
      if (transaction.value.id == vcardStore.transactionId) {
        await vcardStore.loadUser()
      }
      } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Transaction #' + props.id + ' was not updated due to validation errors!')
      } else {
        toast.error('Transaction #' + props.id + ' was not updated due to unknown server error!')
      }
    }
  }
}

const cancel = () => {
  originalValueStr = JSON.stringify(transaction.value)
  window.location.reload()
}

watch(
  () => props.phone_number,
  (newValue) => {
      loadTransaction(newValue)
    },
  {immediate: true}  
)

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

</script>

<template>

  <create-transaction
    :transaction="transaction"
    :errors="errors"
    :inserting="inserting(id)"
    @save="save"
    @cancel="cancel"
  ></create-transaction>
</template>
