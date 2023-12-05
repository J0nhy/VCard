<script setup>
import { ref, watch, computed, inject } from "vue";
import { useToast } from 'vue-toastification';
import avatarNoneUrl from '@/assets/avatar-none.png'

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
    admin: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        required: false,
    },
});

const emit = defineEmits(["save", "cancel"]);

const editingAdmin = ref(props.admin)

watch(
    () => props.admin,
    (newAdmin) => {
        editingAdmin.value = newAdmin
    },
    { immediate: true }
)


const save = () => {
    emit("save", editingAdmin.value);
}

const cancel = () => {
    emit("cancel", editingAdmin.value);
}

</script>

<template>
    <form class="row g-3 needs-validation" enctype="multipart/form-data" novalidate @submit.prevent="save">
        <h3 class="mt-5 mb-3">Admin #{{ editingAdmin.id }}</h3>
        <hr />
        <div class="d-flex flex-wrap justify-content-between">
            <div class="w-75 pe-4">
                <div class="mb-3">
                    <label for="inputPassword" class="form-label">Password Atual</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['password'] : false }"
                        id="inputPassword" placeholder="Admin Current Password" required v-model="editingAdmin.password" />
                    <field-error-message :errors="errors" fieldName="password"></field-error-message>
                </div>
                <div class="mb-3">
                    <label for="inputNewPassword" class="form-label">Nova Password</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['new_password'] : false }"
                        id="inputNewPassword" placeholder="Admin New Password" v-model="editingAdmin.new_password" />
                    <field-error-message :errors="errors" fieldName="new_password"></field-error-message>
                </div>
                <div class="mb-3">
                    <label for="inputNewPasswordConfirmation" class="form-label">Repetir Nova Password</label>
                    <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['new_password_confirmation'] : false }"
                        id="inputNewPasswordConfirmation" placeholder="Admin New Password" v-model="editingAdmin.new_password_confirmation" />
                    <field-error-message :errors="errors" fieldName="new_password_confirmation"></field-error-message>
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
