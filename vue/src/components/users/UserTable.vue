<script setup>
import { inject } from "vue";
import { useAdminStore } from "../../stores/admin.js"

import avatarNoneUrl from '@/assets/avatar-none.png'

const serverBaseUrl = inject("serverBaseUrl");
const adminStore = useAdminStore()

const props = defineProps({
  admins: {
    type: Array,
    default: () => [],
  },
  showId: {
    type: Boolean,
    default: true,
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
    default: true,
  },
  showEditButton: {
    type: Boolean,
    default: true,
  },
  showDeleteButton: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["edit"]);

const photoFullUrl = (admin) => {
  return admin.photo_url
    ? serverBaseUrl + "/storage/fotos/" + admin.photo_url
    : avatarNoneUrl;
};

const editClick = (admin) => {
  emit("edit", admin);
};
</script>

<template>
  <table class="table">
    <thead>
      <tr>
        <th v-if="showId" class="align-middle">#</th>
        <th v-if="showPhoto" class="align-middle">Photo</th>
        <th class="align-middle">Name</th>
        <th v-if="showEmail" class="align-middle">Email</th>
        <th v-if="showAdmin" class="align-middle">Admin?</th>
        <th v-if="showGender" class="align-middle">Gender</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="admin in admins" :key="admin.id">
        <td v-if="showId" class="align-middle">{{ admin.id }}</td>
        <td v-if="showPhoto" class="align-middle">
          <img :src="photoFullUrl(admin)" class="rounded-circle img_photo" />
        </td>
        <td class="align-middle">{{ admin.name }}</td>
        <td v-if="showEmail" class="align-middle">{{ admin.email }}</td>
        <td v-if="showAdmin" class="align-middle">{{ admin.type == "A" ? "Sim" : "" }}</td>
        <td v-if="showGender" class="align-middle">{{ admin.gender_name }}</td>
        <td class="text-end align-middle" v-if="showEditButton">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-light" @click="editClick(admin)" v-if="showEditButton">
              <i class="bi bi-xs bi-pencil"></i>
            </button>
          </div>
        </td>
        <td class="text-end align-middle" v-if="showDeleteButton">
          <div class="d-flex justify-content-end">
            <button class="btn btn-xs btn-light" @click="editClick(admin)" v-if="showDeleteButton">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}

.img_photo {
  width: 3.2rem;
  height: 3.2rem;
}
</style>
