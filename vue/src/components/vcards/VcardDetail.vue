<script setup>
import { ref, watch, computed, inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'
import { useVcardStore } from "../../stores/vcard";
import { useToast } from 'vue-toastification';
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import bcrypt from 'bcryptjs'
import { useUserStore } from '../../stores/user.js'



const socket = inject('socket')

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
  inserting: {
    type: Boolean,
    default: false,
  },
  errors: {
    type: Object,
    required: false,
  },
  inserindo: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["save", "cancel", "delete"]);

const editingVcard = ref(props.vcard)
const VcardToDelete = ref(null)
const deleteConfirmationDialog = ref(null)
const vcardStore = useVcardStore()
const toast = useToast()
const router = useRouter()
const deleteVcardConfirmed = ref(null)
const userStore = useUserStore()


//const inserindo = ref(props.inserting)
//const insert = inserindo.value
//
//console.log("inserindo: " + insert)
//console.log(props.inserindo)

const vCardToDeleteDescription = computed(() => VcardToDelete.value
    ? `#${VcardToDelete.value.id} (${VcardToDelete.value.name})`
    : "")

const inputPhotoFile = ref(null)
const editingImageAsBase64 = ref(null)
const deletePhotoOnTheServer = ref(false)

watch(
  () => props.vcard,
  (newVcard) => {
    editingVcard.value = newVcard
  },
  { immediate: true },
  () => props.inserting,
  (newInserting) => {
    console.log("Valor de inserting em VcardDetail.vue mudou para:", newInserting);
  }
  
)



const photoFullUrl = computed(() => {
  if (deletePhotoOnTheServer.value) {
    return avatarNoneUrl
  }
  if (editingImageAsBase64.value) {
    return editingImageAsBase64.value
  } else {
    return editingVcard.value.photo_url
      ? serverBaseUrl + "/storage/fotos/" + editingVcard.value.photo_url
      : avatarNoneUrl
  }
})

const save = () => {
  const userToSave = editingVcard.value
  userToSave.deletePhotoOnServer = deletePhotoOnTheServer.value
  userToSave.base64ImagePhoto = editingImageAsBase64.value
  emit("save", userToSave);
}

const cancel = () => {
  emit("cancel", editingVcard.value);
}

// When changing the photo file, change the editingImageAsBase64.value
const changePhotoFile = () => {
  try {
    const file = inputPhotoFile.value.files[0]
    if (!file) {
      editingImageAsBase64.value = null
    } else {
      const reader = new FileReader()
      reader.addEventListener(
          'load',
          () => {
            // convert image file to base64 string
            editingImageAsBase64.value = reader.result
            deletePhotoOnTheServer.value = false
          },
          false,
      )
      if (file) {
        reader.readAsDataURL(file)
      }
    }
  } catch (error) {
    editingImageAsBase64.value = null
  }
}

const resetToOriginalPhoto = () => {
  deletePhotoOnTheServer.value = false
  inputPhotoFile.value.value = ''
  changePhotoFile()
}

const cleanPhoto = () => {
  deletePhotoOnTheServer.value = true
}



//deletar vcard
const confirmDelete = ref(false);
const passwordToDelete = ref("");
const pinToDelete = ref("");

const openDeleteConfirmation = () => {
  confirmDelete.value = true;
};

const closeDeleteConfirmation = () => {
  confirmDelete.value = false;
  // Limpar os campos de senha e PIN quando o modal for fechado
  passwordToDelete.value = "";
  pinToDelete.value = "";
};


const deleteVcard = async (editingVcard, passwordToDelete, pinToDelete) => {
  closeDeleteConfirmation();


  // Executar a lógica de exclusão aqui, incluindo a verificação de senha e PIN
  try {
    // Supondo que você tenha armazenado a senha como hash no backend
    // função a ser implementada no store
    
    const passwordMatch = await bcrypt.compare(passwordToDelete, editingVcard.password);

    const pinMatch = await bcrypt.compare(pinToDelete, editingVcard.confirmation_code);

    if (passwordMatch && pinMatch) {
      await vcardStore.deleteVcard(editingVcard);
      toast.success(`Vcard ${editingVcard.phone_number} was deleted`);
      //remove token
      userStore.clearUser();
      //da logout
      router.push({ name: 'Login' });
    } else {
      // Se a verificação falhar, exibir uma mensagem de erro
      toast.warning("Senha ou PIN incorretos. Exclusão abortada.");
    }
  } catch (error) {
    console.error("Erro ao verificar senha e PIN:", error);
    toast.error("Erro ao verificar senha e PIN. Por favor, tente novamente.");
  }
};


</script>

<template>
  <confirmation-dialog
    ref="deleteConfirmationDialog"
    confirmationBtn="Delete vcard"
    :msg="`Do you really want to delete the vcard ${vCardToDeleteDescription}?`"
    @confirmed="deleteVcardConfirmed"
  >
  </confirmation-dialog>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">Vcard of {{ editingVcard.phone_number  }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['name'] : false }"
            id="inputName" placeholder="Vcard Name" required v-model="editingVcard.name" />
          <field-error-message :errors="errors" fieldName="name"></field-error-message>
        </div>
        <div class="mb-3">
          <label for="inputPhoneNumber" class="form-label">Phone Number</label>
          <input v-show="!inserindo" disabled
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['phone_number'] : false }"
            id="inputPhoneNumber"
            placeholder="Vcard Phone Number"
            required
            v-model="editingVcard.phone_number"
            :readonly="vcardStore.user"
          
          />
          <input v-show="inserindo" 
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['phone_number'] : false }"
            id="inputPhoneNumber"
            placeholder="Vcard Phone Number"
            required
            v-model="editingVcard.phone_number"
            :readonly="false"
          />
          <field-error-message :errors="errors" fieldName="phone_number"></field-error-message>
        </div>

        <div class="mb-3 px-1">
          <label for="inputEmail" class="form-label">Email</label>
          <input type="email" class="form-control" :class="{ 'is-invalid': errors ? errors['email'] : false }"
            id="inputEmail" placeholder="Email" required v-model="editingVcard.email" />
          <field-error-message :errors="errors" fieldName="email"></field-error-message>
        </div>

        <div class="mb-3"  v-if="inserindo === true">
          <label for="inputPassword" class="form-label">Password</label>
          <input
              type="password"
              class="form-control"
              :class="{ 'is-invalid': errors ? errors['password'] : false }"
              id="inputPassword"
              v-model="editingVcard.password"
          />
          <field-error-message :errors="errors" fieldName="password"></field-error-message>
        </div>
        <div class="mb-3"   v-if="inserindo === true">
          <label for="inputPasswordConfirmation" class="form-label">Password Confirmation</label>
          <input
              type="password"
              class="form-control"
              :class="{ 'is-invalid': errors ? errors['password_confirmation'] : false }"
              id="inputPasswordConfirmation"
              v-model="editingVcard.password_confirmation"
          />
          <field-error-message :errors="errors" fieldName="password_confirmation"></field-error-message>
        </div>
        <div class="mb-3"  v-if="inserindo === true">
          <label for="inputConfirmation_Code" class="form-label">Confirmation Code</label>
          <input
              type="password"
              class="form-control"
              :class="{ 'is-invalid': errors ? errors['confirmation_code'] : false }"
              id="inputConfirmation_Code"
              v-model="editingVcard.confirmation_code"
          />
          <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
        </div>

      </div>
      <div class="w-25">
        <div class="d-flex flex-column">
          <label class="form-label">Photo</label>
          <div class="form-control text-center">
            <img :src="photoFullUrl" class="w-100" />
          </div>
          <div class="mt-3 d-flex justify-content-between flex-wrap">
            <label for="inputPhoto" class="btn btn-dark flex-grow-1 mx-1">Carregar</label>
            <button class="btn btn-secondary flex-grow-1 mx-1" @click.prevent="resetToOriginalPhoto" v-if="editingVcard.photo_url">Repor</button>
            <button class="btn btn-danger flex-grow-1 mx-1" @click.prevent="cleanPhoto" v-show="editingVcard.photo_url || editingImageAsBase64">Apagar</button>
          </div>
          <div>
            <field-error-message :errors="errors" fieldName="base64ImagePhoto"></field-error-message>
          </div>
        </div>
      </div>
      <input type="file" style="visibility:hidden;" id="inputPhoto" ref="inputPhotoFile" @change="changePhotoFile" />
    </div>
    <div class="mb-3 d-flex justify-content-start">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    
    <!-- Botão de exclusão -->
  <button type="button" class="btn btn-xs btn-light" @click="openDeleteConfirmation" v-if="inserindo === false">
    <i class="bi bi-xs bi-x-square-fill"></i>
  </button>

    
      
    </div>
     
  <!-- Modal de confirmação de exclusão -->
  <modal v-if="confirmDelete" @close="closeDeleteConfirmation">
    <h3 class="mb-3">Confirme a exclusão</h3>
    <div class="mb-3">
      <label for="inputPasswordToDelete" class="form-label">Password:</label>
      <input
        type="password"
        class="form-control"
        id="inputPasswordToDelete"
        v-model="passwordToDelete"
        required
      />
    </div>
    <div class="mb-3">
      <label for="inputPinToDelete" class="form-label">PIN:</label>
      <input
        type="password"
        class="form-control"
        id="inputPinToDelete"
        v-model="pinToDelete"
        required
      />
    </div>
    <button type="button" class="btn btn-danger" @click="deleteVcard(editingVcard, passwordToDelete,pinToDelete)">Confirmar exclusão</button>
  </modal>
  
    
  </form>
  <br><br>

</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
