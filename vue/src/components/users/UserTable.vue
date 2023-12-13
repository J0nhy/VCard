<script setup>
import { inject, ref } from "vue";
import { useAdminStore } from "../../stores/admin.js"
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

import avatarNoneUrl from '@/assets/avatar-none.png'

const serverBaseUrl = inject("serverBaseUrl");
const adminStore = useAdminStore()
const searchQuery = ref('');
const newMaxDebit = ref('');

const props = defineProps({
  admins: {
    type: Object,
    default: () => [],
  },
  showId: {
    type: Boolean,
    default: true,
  },
  showPhoneNumber: {
    type: Boolean,
    default: false,
  },
  showEmail: {
    type: Boolean,
    default: true,
  },
  showAdmin: {
    type: Boolean,
    default: true,
  },
  showGender: {
    type: Boolean,
    default: false,
  },
  showPhoto: {
    type: Boolean,
    default: false,
  },
  showEditButton: {
    type: Boolean,
    default: true,
  },
  showDeleteButton: {
    type: Boolean,
    default: false,
  },
  showSearchVCard: {
    type: Boolean,
    default: false,
  },
  showSaldo: {
    type: Boolean,
    default: false,
  },
  showLimiteDebito: {
    type: Boolean,
    default: false,
  },
  showBloqueado: {
    type: Boolean,
    default: false,
  },
  showApagado: {
    type: Boolean,
    default: false,
  }
});

const emit = defineEmits(["edit", "search", "delete", "updateBlockedStatus", "page-changed"]);


const photoFullUrl = (admin) => {
  return admin.photo_url
    ? serverBaseUrl + "/storage/fotos/" + admin.photo_url
    : avatarNoneUrl;
};

const editClick = (admin) => {
  emit("edit", admin);
};
const editMaxDebit = (admin) => {
  emit("edit", admin, newMaxDebit);
};
const deleteClick = (admin) => {
  emit("delete", admin);
};
const search_vcard = () => {
  emit("search", searchQuery.value);
};

const updateBlockedStatusClick = (admin) => {
  emit("updateBlockedStatus", admin);
};
const resetInputBoxModalClick = () => {
  newMaxDebit.value = '';
};
const pageChanged = (page) => {
  emit("page-changed", page, searchQuery.value);
};

</script>

<template>
 <div v-if="showSearchVCard" class="input-group mb-3">
  <input v-model="searchQuery" type="text" class="form-control" placeholder="Search Name/Phone Number"
    aria-label="Recipient's username" aria-describedby="basic-addon2" @keyup.enter="search_vcard">
  <div class="input-group-append">
    <button class="btn btn-outline-secondary" @click="search_vcard" type="button">Search</button>
  </div>
</div>

  <table class="table">
    <thead>
      <tr>
        <th v-if="showId" class="align-middle">#</th>
        <th v-if="showPhoneNumber" class="align-middle">Phone Number</th>
        <th class="align-middle">Name</th>
        <th v-if="showEmail" class="align-middle">Email</th>
        <th v-if="showAdmin" class="align-middle">Admin?</th>
        <th v-if="showGender" class="align-middle">Gender</th>


        <th v-if="showSaldo" class="align-middle">Saldo</th>
        <th v-if="showLimiteDebito" class="align-middle">Limite Debito</th>
        <th v-if="showBloqueado" class="align-middle">Bloqueado?</th>
        <th v-if="showApagado" class="align-middle">Apagado?</th>
        <th v-if="showPhoto" class="align-middle">Photo</th>



      </tr>
    </thead>
    <tbody>
      <tr v-for="admin in admins.data" :key="admin.id">
        <td v-if="showId" class="align-middle">{{ admin.id }}</td>
        <td v-if="showPhoneNumber" class="align-middle">{{ admin.phone_number }}</td>


        <td class="align-middle">{{ admin.name }}</td>
        <td v-if="showEmail" class="align-middle">{{ admin.email }}</td>
        <td v-if="showAdmin" class="align-middle">{{ admin.type == "A" ? "Sim" : "" }}</td>
        <td v-if="showGender" class="align-middle">{{ admin.gender_name }}</td>


        <td v-if="showSaldo" class="align-middle">
          <div class="d-flex align-items-center">
            <span class="mr-2">{{ admin.balance }}€</span>
            <router-link class="dropdown-item"
              :class="{ active: $route.name === 'Credit' && $route.params.phone_number == admin.phone_number }"
              :to="{ name: 'Credit', params: { phone_number: admin.phone_number } }" @click="clickMenuOption">
              <i class="bi bi-plus-square-fill" style="color: green;"></i>
            </router-link>
          </div>
        </td>




        <td v-if="showLimiteDebito" class="align-middle">{{ admin.max_debit }}€
          <button @click="resetInputBoxModalClick()" class="btn btn-xs btn-light" data-bs-toggle="modal"
            :data-bs-target="'#editModal' + admin.phone_number"
            v-if="showLimiteDebito && admin.blocked == 0 && admin.deleted_at == null">
            <i class="bi bi-xs bi-pencil"></i>
          </button>

          <!-- Modal -->
          <div class="modal fade" :id="'editModal' + admin.phone_number" tabindex="-1" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel">Edit Max Debit</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <!-- Input box -->
                  <input v-model="newMaxDebit" type="text" class="form-control" @keyup.enter="editMaxDebit(admin)" placeholder="Enter new Max Debit...">
                </div>
                <div class="modal-footer">
                  <!-- Confirm button -->
                  <button type="button" class="btn btn-primary" @click="editMaxDebit(admin)"
                    data-bs-dismiss="modal">Confirm</button>
                  <!-- Close button -->
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        </td>





        <td v-if="showBloqueado" class="align-middle">
          {{ admin.blocked == 1 ? 'Sim' : 'Não' }}
        </td>
        <td v-if="showApagado" class="align-middle">
          {{ admin.deleted_at ? 'Sim' : 'Não' }}
        </td>


        <td v-if="showPhoto" class="align-middle">
          <img :src="photoFullUrl(admin)" class="rounded-circle img_photo" />
        </td>

        <td class="text-end align-middle" v-if="showEditButton">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-light" @click="editClick(admin)" v-if="showEditButton">
              <i class="bi bi-xs bi-pencil"></i>
            </button>
          </div>
        </td>
        <td class="text-end align-middle" v-if="showDeleteButton">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-light" @click="deleteClick(admin)"
              v-if="showDeleteButton && admin.deleted_at == null">
              <i class="bi bi-trash"></i>
            </button>
            <button class="btn btn-xs btn-light" @click="updateBlockedStatusClick(admin)"
              v-if="showDeleteButton && admin.blocked == 0 && admin.deleted_at == null">
              <i class="bi bi-lock"></i>
            </button>
            <button class="btn btn-xs btn-light" @click="updateBlockedStatusClick(admin)"
              v-if="showDeleteButton && admin.blocked == 1 && admin.deleted_at == null">
              <i class="bi bi-unlock"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <Bootstrap5Pagination :data="admins" @pagination-change-page="pageChanged" />
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}

.img_photo {
  width: 3.2rem;
  height: 3.2rem;
}</style>
