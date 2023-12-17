<script setup>
import { useToast } from "vue-toastification"
import { ref, watch, inject } from 'vue'
import PasswordChange from "./PasswordChange.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'

const toast = useToast()
const router = useRouter()
const axios = inject('axios')

const props = defineProps({
    phone_number: {
      type: Number,
      default: null
    }
})

const newVcard = () => {
    return {
      phone_number: '',
    }
}

const vcard = ref(newVcard())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const loadVcard = async (phone_number) => {
  originalValueStr = ''
  errors.value = null
  if (!phone_number || (phone_number < 0)) {
    vcard.value = newVcard()
  } else {
      try {
        const response = await axios.get('vcard/password/' + phone_number)
        vcard.value = response.data.data
        originalValueStr = JSON.stringify(vcard.value)
      } catch (error) {
        //console.log(error)
      }
  }
}

const save = async () => {
  errors.value = null
  try {
    const response = await axios.put('vcard/password/' + props.phone_number, vcard.value)
    vcard.value = response.data.data
    originalValueStr = JSON.stringify(vcard.value)
    toast.success('Vcard #' + vcard.value.phone_number + ' was updated successfully.')
    
  } catch (error) {
    if (error.response.status == 422) {
      errors.value = error.response.data.errors
      toast.error('Vcard #' + props.phone_number + ' was not updated due to validation errors!')
    } else if (error.response.status == 401) {
      toast.error('Vcard #' + props.phone_number + ' was not updated, password/pin is incorrect!')
    } else {
      toast.error('Vcard #' + props.phone_number + ' was not updated due to unknown server error!')
    }
  }
}

const cancel = () => {
  originalValueStr = JSON.stringify(vcard.value)
  window.location.reload()
}

watch(
  () => props.phone_number,
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

  <password-change
    :vcard="vcard"
    :errors="errors"
    @save="save"
    @cancel="cancel"
  ></password-change>
</template>
