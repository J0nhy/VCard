<script setup>
import { ref, watch, computed, inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
  errors: {
    type: Object,
    required: false,
  },
});

const emit = defineEmits(["save", "cancel"]);

const editingVcard = ref(props.vcard)

watch(
  () => props.vcard,
  (newVcard) => {
    editingVcard.value = newVcard
  },
  { immediate: true }
)

const photoFullUrl = computed(() => {
  return editingVcard.value.photo_url
    ? serverBaseUrl + "/storage/fotos/" + editingVcard.value.photo_url
    : avatarNoneUrl
})

const save = () => {
  emit("save", editingVcard.value);
}

const cancel = () => {
  emit("cancel", editingVcard.value);
}
</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">Vcard #{{ editingVcard.phone_number }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['name'] : false }"
            id="inputName"
            placeholder="Vcard Name"
            required
            v-model="editingVcard.name"
          />
          <field-error-message :errors="errors" fieldName="name"></field-error-message>
        </div>

        <div class="mb-3 px-1">
          <label for="inputEmail" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['email'] : false }"
            id="inputEmail"
            placeholder="Email"
            required
            v-model="editingVcard.email"
          />
          <field-error-message :errors="errors" fieldName="email"></field-error-message>
        </div>

        <div class="mb-3 px-1">
          <label for="inputBalance" class="form-label">Balance</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['balance'] : false }"
            id="inputBalance"
            placeholder="Balance"
            required
            v-model="editingVcard.balance"
          />
          <field-error-message :errors="errors" fieldName="balance"></field-error-message>
        </div>
        <div class="mb-3 px-1">
          <label for="inputMaxDebit" class="form-label">Max Debit</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['max_debit'] : false }"
            id="inputMaxDebit"
            placeholder="max_debit"
            required
            v-model="editingVcard.max_debit"
          />
          <field-error-message :errors="errors" fieldName="max_debit"></field-error-message>
        </div>
      </div>
      <div class="w-25">
        <div class="mb-3">
          <label class="form-label">Photo</label>
          <div class="form-control text-center">
            <img :src="photoFullUrl" class="w-100" />
          </div>
        </div>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-start">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
