<script setup>
import { useToast } from "vue-toastification"
import { ref, watch, inject } from 'vue'
import VcardDetail from "./VcardDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useVcardStore } from "../../stores/vcard"

const socket = inject('socket')

const toast = useToast()
const router = useRouter()
const vcardStore = useVcardStore()

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
      phone_number: '',
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

const phone_number = props.phone_number

const routeName = router.currentRoute.value.name;
//console.log("Rota Atual:", routeName);

const inserindo = ref(false)

const inserting = (id) => !id || (id < 0)

const loadVcard = async (phoneNumber) => {
  originalValueStr = ''
  errors.value = null
  
  if(routeName == 'Vcard') {
    //inserting.value = false
    inserindo.value = false
    //console.log('Vcard.vue: inserting.value = false', inserting.value)
  }else if(routeName == 'NewVcard'){
    //inserting.value = true  
    inserindo.value = true
    //console.log('Vcard.vue: inserting.value = true', inserting.value)
  }

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
  //console.log(vcardToSave)
  //console.log(props.id)
  if (routeName == 'NewVcard') {
    try {
      const response = await axios.post('vcard', vcardToSave)
      vcard.value = response.data.data
      originalValueStr = JSON.stringify(vcard.value)
      toast.success('User #' + vcard.value.phone_number + ' was registered successfully.')
      //do login
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
      toast.success('User #' + vcard.value.phone_number + ' was updated successfully.')
      if (vcard.value.id == vcardStore.vcardId) {
        await vcardStore.loadUser()
      }
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




</script>

<template>

  <vcard-detail
    :vcard="vcard"
    :errors="errors"
    :inserting="inserting(id)"
    :inserindo="inserindo"
    @save="save"
    @cancel="cancel"
  ></vcard-detail>
</template>
