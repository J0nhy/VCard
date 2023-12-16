<script setup>
import { useToast } from "vue-toastification"
import { ref, watch, inject } from 'vue'
import CreateTransaction from "./CreateTransaction.vue"
import Credit from "./CreateCreditTransaction.vue"
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
const newTransactionCredit = () => {
  return {
    phone_number: props.phone_number,
    vcard: '',
    type: 'C',
    value: '',
    old_balance: '',
    new_balance: '',
    payment_type: '',
    payment_reference: '',
  }
}


const transaction = ref(newTransaction())
const transactionCredit = ref(newTransactionCredit())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const routeName = router.currentRoute.value.name;
console.log("Rota Atual:", routeName);

const inserting = (id) => !id || (id < 0)
const loadTransaction = async () => {
  originalValueStr = ''
  errors.value = null
  if (!props.id || (props.id < 0)) {
    if (routeName == 'Credit') {
      transactionCredit.value = newTransactionCredit()
    } else
      transaction.value = newTransaction()

  } else {
    try {
      if (routeName == 'Credit') {
        const response = await axios.get('transaction/' + props.id)
        transactionCredit.value = response.data.data
        originalValueStr = JSON.stringify(transactionCredit.value)
      } else if (routeName == 'NewTransaction' || routeName == 'Transaction') {
        const response = await axios.get('transaction/' + props.id)
        transaction.value = response.data.data
        originalValueStr = JSON.stringify(transaction.value)
      }
    } catch (error) {
      console.log(error)
    }
  }
}



const save = async (transactionToSave) => {
  errors.value = null
  //console.log(transactionToSave)
  //console.log(props.id)
  if (routeName == 'NewTransaction') {
    try {
      const response = await axios.post('transaction', transactionToSave)
      transaction.value = response.data.data
      originalValueStr = JSON.stringify(transaction.value)
      toast.success('Transaction #' + transaction.value.id + ' was registered successfully.')

      console.log("new balance" + response.data.data.new_balance)
      router.push({
        name: 'Dashboard',
        params: { new_balance: response.data.data.new_balance, },
      });
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('VCard was not registered due to validation errors!')
      } else {
        toast.error('Transaction was not registered due to unknown server error!')
      }
    }
  } else if (routeName == 'Credit') {
    try {
      const response = await axios.post('transactionCredit', transactionToSave)
      transaction.value = response.data.data
      originalValueStr = JSON.stringify(transaction.value)
      console.log(response.data.data.date)
      toast.success('Transaction #' + transaction.value.id + ' was registered successfully.')
      router.push({ name: 'Dashboard' })
    } catch (error) {
      console.log(error)
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Transaction was not registered due to validation errors!')
      } else {
        toast.error('Transaction was not registered due to unknown server error!')
      }
    }
  } else if (routeName == 'Transaction') {
    try {
      console.log('transactionToSave:', transactionToSave)
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
  { immediate: true }
)

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

</script>

<template>
  <create-transaction v-if="routeName == 'NewTransaction'" :transaction="transaction" :errors="errors"
    :inserting="inserting(id)" @save="save" @cancel="cancel"></create-transaction>

    <create-transaction v-if="routeName == 'Transaction'" :transaction="transaction" :errors="errors"
    :inserting="inserting(id)" @save="save" @cancel="cancel"></create-transaction>

  <credit v-if="routeName == 'Credit'" :transactionCredit="transactionCredit" :errors="errors" :inserting="inserting(id)"
    @save="save" @cancel="cancel"></credit>
</template>
