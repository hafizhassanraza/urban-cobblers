<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

let searchTimeout;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.customers.index'), { search: search.value || undefined }, { preserveState: true, replace: true });
    }, 300);
};
</script>

<template>
    <Head title="Customers" />

    <AdminLayout>
        <template #title>Customers</template>

        <div class="mb-6">
            <input v-model="search" type="search" placeholder="Search customers..." class="input-field max-w-xs" @input="onSearch" />
        </div>

        <div class="card overflow-hidden">
            <table class="admin-table">
                <thead class="admin-table-head">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Orders</th>
                        <th class="px-6 py-3">Joined</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-beige/30">
                    <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-brand-beige/10">
                        <td class="px-6 py-4 font-medium">{{ customer.name }}</td>
                        <td class="px-6 py-4">{{ customer.email }}</td>
                        <td class="px-6 py-4">{{ customer.phone || '—' }}</td>
                        <td class="px-6 py-4">{{ customer.orders_count }}</td>
                        <td class="px-6 py-4">{{ new Date(customer.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4">
                            <Link :href="route('admin.customers.show', customer.id)" class="text-brand-copper hover:underline">View</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="customers.links" />
    </AdminLayout>
</template>
