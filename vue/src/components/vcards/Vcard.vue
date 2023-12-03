<script setup>

import { useToast } from "vue-toastification"
import { ref, watch, inject } from 'vue'
import VcardDetail from "./VcardDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useVcardStore } from "../../stores/vcard"

const toast = useToast()
const router = useRouter()
const vcardStore = useVcardStore()

const axios = inject('axios')

const props = defineProps({
    phoneNumber: {
      type: Number,
      default: null
    },
    id: {
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
      password: '',
      confirmation_number: '',
    }
}

const vcard = ref(newVcard())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const inserting = (id) => !id || (id < 0)
const loadVcard = async (phoneNumber) => {
  originalValueStr = ''
  errors.value = null
  if (!phoneNumber || (phoneNumber < 0)) {
    vcard.value = newVcard()
  } else {
      try {
        const response = await axios.get('vcard/' + phoneNumber)
        vcard.value = response.data.data
        originalValueStr = JSON.stringify(vcard.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const save = async (vcardToSave) => {
  errors.value = null
  console.log(vcardToSave)
  console.log(props.id)
  if (inserting(props.id)) {
    try {
      const response = await axios.post('vcard', vcardToSave)
      vcard.value = response.data.data
      originalValueStr = JSON.stringify(vcard.value)
      toast.success('User #' + vcard.value.id + ' was registered successfully.')
      
      router.push({name: 'Dashboard'})
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('VCard was not registered due to validation errors!')
      } else {
        toast.error('Vcard was not registered due to unknown server error!')
      }
    }
  } else {
    try {
      const response = await axios.put('vcard/' + props.id, vcardToSave)
      vcard.value = response.data.data
      originalValueStr = JSON.stringify(vcard.value)
      toast.success('User #' + vcard.value.id + ' was updated successfully.')
      if (vcard.value.id == vcardStore.vcardId) {
        await vcardStore.loadUser()
      }
      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Vcard #' + props.id + ' was not updated due to validation errors!')
      } else {
        toast.error('Vcard #' + props.id + ' was not updated due to unknown server error!')
      }
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
    :inserting="inserting(id)"
    @save="save"
    @cancel="cancel"
  ></vcard-detail>
</template>
