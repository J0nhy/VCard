<script setup>
import { ref, watch, computed, inject } from "vue";
import { useToast } from 'vue-toastification';
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


const save = () => {
    emit("save", editingVcard.value);
}

const cancel = () => {
    emit("cancel", editingVcard.value);
}

</script>

<template>
    <form class="row g-3 needs-validation" enctype="multipart/form-data" novalidate @submit.prevent="save">
        <h3 class="mt-5 mb-3">Vcard #{{ editingVcard.phone_number }}</h3>
        <hr />
        <div class="d-flex flex-wrap justify-content-between">
            <div class="w-75 pe-4">
                <div class="mb-3">
                    <label for="inputPassword" class="form-label">Password Atual</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['password'] : false }"
                        id="inputPassword" placeholder="Vcard Current Password" required v-model="editingVcard.password" />
                    <field-error-message :errors="errors" fieldName="password"></field-error-message>
                </div>
                <div class="mb-3">
                    <label for="inputNewPassword" class="form-label">Nova Password</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['new_password'] : false }"
                        id="inputNewPassword" placeholder="Vcard New Password" v-model="editingVcard.new_password" />
                    <field-error-message :errors="errors" fieldName="new_password"></field-error-message>
                </div>
                <div class="mb-3">
                    <label for="inputNewPasswordConfirmation" class="form-label">Repetir Nova Password</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['new_password_confirmation'] : false }"
                        id="inputNewPasswordConfirmation" placeholder="Vcard New Password" v-model="editingVcard.new_password_confirmation" />
                    <field-error-message :errors="errors" fieldName="new_password_confirmation"></field-error-message>
                </div>
                <hr>
                <div class="mb-3">
                    <label for="inputConfirmationCode" class="form-label">Pin Atual</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['confirmation_code'] : false }"
                        id="inputConfirmationCode" placeholder="Vcard Current Pin" required v-model="editingVcard.confirmation_code" />
                    <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
                </div>
                <div class="mb-3">
                    <label for="inputNewConfirmationCode" class="form-label">Novo Pin</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['new_confirmation_code'] : false }"
                        id="inputNewConfirmationCode" placeholder="Vcard New Pin" v-model="editingVcard.new_confirmation_code" />
                    <field-error-message :errors="errors" fieldName="new_confirmation_code"></field-error-message>
                </div>
                <div class="mb-3">
                    <label for="inputNewConfirmationCodeConfirmation" class="form-label">Repetir Novo Pin</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['new_confirmation_code_confirmation'] : false }"
                        id="inputNewConfirmationCodeConfirmation" placeholder="Vcard New Pin" v-model="editingVcard.new_confirmation_code_confirmation" />
                    <field-error-message :errors="errors" fieldName="new_confirmation_code_confirmation"></field-error-message>
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
