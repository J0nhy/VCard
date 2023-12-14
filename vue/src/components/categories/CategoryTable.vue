<script setup>
import { inject, ref } from "vue";

import { Bootstrap5Pagination } from 'laravel-vue-pagination'

const serverBaseUrl = inject("serverBaseUrl");
const searchQuery = ref('');

const props = defineProps({
    categorias: {
        type: Object,
        default: () => [],
    },
    showName: {
        type: Boolean,
        default: true,
    },
    showType: {
        type: Boolean,
        default: true,
    },
    showSearch: {
        type: Boolean,
        default: true,
    },
    showDelete: {
        type: Boolean,
        default: true,
    },

});

const emit = defineEmits(["edit", "page-changed", "search","delete"]);



const editClick = (categoria) => {
    emit("edit", categoria);
};

const pageChanged = (page) => {
    emit("page-changed", page);
};
const search = () => {
    emit("search", searchQuery.value);
};
const deleteButton = (categoria) => {
    emit("delete", categoria,searchQuery.value);
};
</script>

<template>
    <div v-if="showSearch" class="input-group mb-3">
        <input v-model="searchQuery" type="text" class="form-control" placeholder="Search Name"
            aria-label="Recipient's username" aria-describedby="basic-addon2" @keyup.enter="search">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" @click="search" type="button">Search</button>
        </div>
    </div>
    <table class="table">


        <thead>
            <tr>
                <th v-if="showName" class="align-middle">Name</th>
                <th v-if="showType" class="align-middle">Type</th>


            </tr>
        </thead>
        <tbody>
            <tr v-for="categoria in categorias.data" :key="categoria.id">
                <td v-if="showName" class="align-middle">{{ categoria.name }}</td>
                <td v-if="showType" class="align-middle">{{ categoria.type == "D" ? "Debit" : "Credit" }}</td>
                <td class="text-end align-middle" v-if="showDelete">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-xs btn-light" @click="deleteButton(categoria)"
                            v-if="showDelete">
                            <i class="bi bi-trash"></i>
                        </button>

                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <Bootstrap5Pagination :data="categorias" @pagination-change-page="pageChanged" />
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
