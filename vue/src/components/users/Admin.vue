<script setup>
import { useToast } from "vue-toastification"
import { useAdminStore } from '../../stores/admin.js'

import { ref, watch, inject } from 'vue'
import AdminDetail from "./AdminDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'

const toast = useToast()
const router = useRouter()
const adminStore = useAdminStore()
const axios = inject('axios')


const props = defineProps({
  id: {
    type: Number,
    default: null
  }
})

const newAdmin = () => {
  return {
    id: null,
    name: '',
    email: '',
  }
}

const admin = ref(newAdmin())
const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const inserting = (id) => !id || (id < 0)
const loadAdmin = async (id) => {
  originalValueStr = ''
  errors.value = null
  if (!id || (id < 0)) {
    admin.value = newAdmin()
  } else {  
    try {
      const response = await axios.get('admin/' + id)
      admin.value = response.data.data
      originalValueStr = JSON.stringify(admin.value)
    } catch (error) {
      console.log(error)
    }
  }
}

const routeName = router.currentRoute.value.name;
console.log("Rota Atual:", routeName);

const save = async (adminToSave) => {
  errors.value = null

  console.log(adminToSave)
  console.log(props.id)
  if (routeName == 'NewAdmin') {
    try {
      const response = await axios.post('admin', adminToSave)
      admin.value = response.data.data
      originalValueStr = JSON.stringify(admin.value)
      toast.success('Admin #' + admin.value.id + ' was registered successfully.')

      router.push({ name: 'Dashboard' })
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Admin was not registered due to validation errors!')
      } else {
        toast.error('Admin was not registered due to unknown server error!')
      }
    }
  } else {
    try {
      const response = await axios.put('admin/' + props.id, admin.value)
      admin.value = response.data.data
      originalValueStr = JSON.stringify(admin.value)
      toast.success('Admin #' + admin.value.id + ' was updated successfully.')

    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('Admin #' + props.id + ' was not updated due to validation errors!')
      } else {
        toast.error('Admin #' + props.id + ' was not updated due to unknown server error!')
      }
    }
  }
}

const cancel = () => {
  originalValueStr = JSON.stringify(admin.value)
  window.location.reload()
}

watch(
  () => props.id,
  (newValue) => {
    loadAdmin(newValue)
  },
  { immediate: true }
)

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = JSON.stringify(admin.value)
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
  <confirmation-dialog ref="confirmationLeaveDialog" confirmationBtn="Discard changes and leave" 
  msg="Do you really want to leave? You have unsaved changes!" @confirmed="leaveConfirmed">
  </confirmation-dialog>

  <admin-detail 
  :admin="admin" 
  :errors="errors" 
  :inserting="inserting(id)"
  @save="save" 
  @cancel="cancel"
  ></admin-detail>
</template>
