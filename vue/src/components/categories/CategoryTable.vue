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

});

const emit = defineEmits(["edit","page-changed"]);



const editClick = (admin) => {
    emit("edit", admin);
};

const pageChanged=(page)=>{
 emit("page-changed", page);
};
</script>

<template>
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
                <td v-if="showType" class="align-middle">{{ categoria.type }}</td>
            </tr>
        </tbody>
    </table>
    <Bootstrap5Pagination :data="categorias" @pagination-change-page="pageChanged"/>

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
