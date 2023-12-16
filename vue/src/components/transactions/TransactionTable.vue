<script setup>
import { inject, ref } from "vue";

import avatarNoneUrl from '@/assets/avatar-none.png'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

const serverBaseUrl = inject("serverBaseUrl");
const searchQuery = ref('');
const newMaxDebit = ref('');

const props = defineProps({
    transactions: {
        type: Object,
        default: () => [],
    },
    show: {
        type: Boolean,
        default: true,
    },
    showSearch: {
        type: Boolean,
        default: true,
    },

});

const emit = defineEmits(["search", "page-changed","show"]);

const search = () => {
    emit("search", searchQuery.value);
};

const show = (transaction) => {
    emit("show", transaction);
};
const pageChanged = (page) => {
    emit("page-changed", page);
};
</script>

<template>
    <div v-if="showSearch" class="input-group mb-3">
        <input v-model="searchQuery" type="text" class="form-control" placeholder="Search To/From"
            aria-label="Recipient's username" aria-describedby="basic-addon2" @keyup.enter="search">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" @click="search" type="button">Search</button>
        </div>
    </div>
    <table class="table">

        <thead>
            <tr>
                <th v-if="show" class="align-middle">Payment Type</th>
                <th v-if="show" class="align-middle">To/From</th>
                <th v-if="show" class="align-middle">Type</th>

                <th v-if="show" class="align-middle">Amount</th>
                <th v-if="show" class="align-middle">Category</th>
                <th v-if="show" class="align-middle">Date</th>

            </tr>
        </thead>
        <tbody>
            <tr v-for="transaction in transactions.data" :key="transaction.id">
                <td v-if="show" class="align-middle">{{ transaction.payment_type }}</td>
                <td v-if="show" class="align-middle">{{ transaction.payment_reference }}</td>
                <td v-if="show" class="align-middle"> {{ transaction.type == 'D' ? 'Debit' : 'Credit' }}</td>
                <td v-if="show" class="align-middle">
                    {{ transaction.type == 'D' ? '-' : '+' }}{{ transaction.value }}
                    <!-- Use a dynamic style binding to set the color -->
                    (<span :style="{ color: transaction.type == 'D' ? 'red' : 'green' }">{{ transaction.new_balance
                    }}</span>)
                </td>
                <td v-if="show" class="align-middle">{{ transaction.category_id }}</td>
                <td v-if="show" class="align-middle">{{ transaction.datetime }}</td>
                <td class="text-end align-middle" v-if="show">
                    <button class="btn btn-xs btn-light" @click="show(transaction)">
                        <i class="bi bi-search"></i>
                    </button>

                </td>
            </tr>
        </tbody>
    </table>
    <Bootstrap5Pagination :data="transactions" @pagination-change-page="pageChanged" />
</template>

<style scoped>
button {
    margin-left: 3px;
    margin-right: 3px;
}
</style>

