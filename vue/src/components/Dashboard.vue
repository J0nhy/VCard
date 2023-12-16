<script setup>
import { useToast } from "vue-toastification"
import { ref, watch, inject } from 'vue'
import DashboardDetail from "./DashboardDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useVcardStore } from "../stores/vcard"
import { useUserStore } from "../stores/user"

const socket = inject('socket')

const toast = useToast()
const router = useRouter()
const vcardStore = useVcardStore()
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

const newVcard = () => {
  return {
      name: '',
      email: '',
      photo_url: '',
      password: '',
      confirmation_number: '',
      valor: 0,
      piggyBalance: userStore.vcardPiggy,
      userBalance: userStore.userBalance,
    }
  }

const vcard = ref(newVcard())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const routeName = router.currentRoute.value.name;

const ativarPiggy = async (data) => {
  originalValueStr = ''
  errors.value = null
  try {
    const response = await axios.put('activatePiggy/' + userStore.user.id)
    data.piggyBalance = response.data.data.custom_data
    data.userBalance = response.data.data.balance
    originalValueStr = JSON.stringify(vcard.value)
    toast.success('Vcard #' + vcard.value.phone_number + ', Piggy was updated successfully.')

  } catch (error) {
    if (error.response.status == 422) {
      errors.value = error.response.data.errors
      toast.error('Piggy was not updated due to validation errors!')
    } else {
      toast.error('Piggy was not updated due to unknown server error!')
    }
  }
}

const depositar = async (data) => {
  originalValueStr = ''  
  errors.value = null
  try {
    const response = await axios.put('depositar/' + userStore.user.id, {
      valor: data.valor,
    });
    data.piggyBalance = response.data.data.custom_data
    data.userBalance = response.data.data.balance
    //userStore.updateUserBalance(data.userBalance);
    
    originalValueStr = JSON.stringify(vcard.value)
    toast.success('Piggy was updated successfully.');

  } catch (error) {
    if (error.response.status == 422) {
      errors.value = error.response.data.errors
      toast.error('Piggy was not updated due to validation errors!')
    } else {
      toast.error('Piggy was not updated due to unknown server error!')
    }
  }
}

const debitar = async (data) => {
  originalValueStr = ''
  errors.value = null
  try {
    const response = await axios.put('debitar/' + userStore.user.id, {
      valor: data.valor,
    });

    data.piggyBalance = response.data.data.custom_data
    data.userBalance = response.data.data.balance
    originalValueStr = JSON.stringify(vcard.value)
    toast.success('Piggy was updated successfully.');
  } catch (error) {
    // Handle errors
    console.error(error);

    if (error.response && error.response.status === 401) {
      toast.error('Insufficient balance!');
    } else if (error.response && error.response.status === 422) {
      toast.error('Piggy was not updated due to validation errors!');
    } else {
      toast.error('Piggy was not updated due to an unknown server error!');
    }
  }
}


let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

watch(
  () => userStore.userBalance,
  (newVcard) => {
    console.log("balance Atual:", newVcard);
    //console.log("Valor :", router.currentRoute.value);
  },
  { immediate: true }
)






</script>

<template>
  <dashboard-detail :vcard="vcard" :errors="errors" @debitar="debitar" @depositar="depositar"
    @ativarPiggy="ativarPiggy"></dashboard-detail>
</template>
