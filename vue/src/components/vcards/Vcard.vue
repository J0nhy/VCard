<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch } from 'vue'
import VcardDetail from "./VcardDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'

const toast = useToast()
const router = useRouter()

const props = defineProps({
    phoneNumber: {
      type: Number,
      default: null
    }
})

const newVcard = () => {
    return {
      phone_number: null,
      name: '',
      email: '',
      photo_url: '',
      balance: 0,
      max_debit: 0,
    }
}

const vcard = ref(newVcard())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const loadVcard = async (phoneNumber) => {
  originalValueStr = ''
  errors.value = null
  if (!phoneNumber || (phoneNumber < 0)) {
    vcard.value = newVcard()
  } else {
      try {
        const response = await axios.get('http://laravel.test/api/vcard/' + phoneNumber)
        vcard.value = response.data.data
        originalValueStr = JSON.stringify(vcard.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const save = async () => {
  errors.value = null
  try {
    const response = await axios.put('http://laravel.test/api/vcard/' + props.phoneNumber, vcard.value)
    vcard.value = response.data.data
    originalValueStr = JSON.stringify(vcard.value)
    toast.success('Vcard #' + vcard.value.phoneNumber + ' was updated successfully.')
    
  } catch (error) {
    if (error.response.status == 422) {
      errors.value = error.response.data.errors
      toast.error('Vcard #' + props.phoneNumber + ' was not updated due to validation errors!')
    } else {
      toast.error('Vcard #' + props.phoneNumber + ' was not updated due to unknown server error!')
    }
  }
}

const cancel = () => {
  originalValueStr = JSON.stringify(vcard.value)
  router.back()
}

watch(
  () => props.phoneNumber,
  (newValue) => {
      loadVcard(newValue)
    },
  {immediate: true}  
)

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = JSON.stringify(vcard.value)
  if (originalValueStr != newValueStr) {
    // Some value has changed - only leave after confirmation
    nextCallBack = next
    confirmationLeaveDialog.value.show()
  } else {
    // No value has changed, so we can leave the component without confirming
    next()
  }
})


</script>

<template>
  <confirmation-dialog
    ref="confirmationLeaveDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </confirmation-dialog>  

  <vcard-detail
    :vcard="vcard"
    :errors="errors"
    @save="save"
    @cancel="cancel"
  ></vcard-detail>
</template>
