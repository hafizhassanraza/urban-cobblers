<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    orders: Object,
    statuses: Array,
    statusCounts: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const applyFilters = () => {
    router.get(route('admin.orders.index'), {
        search: search.value || undefined,
        status: status.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
};

const filterByStatus = (s) => {
    status.value = status.value === s ? '' : s;
    applyFilters();
};

const statusColor = (s) => ({
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
}[s] || '');

const statusLabel = (s) => ({
    pending: 'Pending',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled',
}[s] || s);
</script>

<template>
    <Head title="Orders" />

    <AdminLayout>
        <template #title>Order Management</template>

        <div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <button
                v-for="s in statuses"
                :key="s"
                type="button"
                class="card p-4 text-left transition hover:border-brand-copper"
                :class="filters.status === s ? 'border-brand-copper ring-1 ring-brand-copper' : ''"
                @click="filterByStatus(s)"
            >
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-black/60">{{ statusLabel(s) }}</p>
                <p class="mt-1 text-2xl font-semibold">{{ statusCounts[s] || 0 }}</p>
            </button>
        </div>

        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Search</label>
                    <input v-model="search" type="search" placeholder="Order #, name, email, tracking..." class="input-field mt-1 max-w-xs" @input="onSearch" />
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Status</label>
                    <select v-model="status" class="input-field mt-1 w-auto" @change="applyFilters">
                        <option value="">All Statuses</option>
                        <option v-for="s in statuses" :key="s" :value="s">{{ statusLabel(s) }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">From</label>
                    <input v-model="dateFrom" type="date" class="input-field mt-1 w-auto" @change="applyFilters" />
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">To</label>
                    <input v-model="dateTo" type="date" class="input-field mt-1 w-auto" @change="applyFilters" />
                </div>
            </div>
            <a :href="route('admin.orders.export')" class="btn-secondary">Export CSV</a>
            <a :href="route('admin.reports.export.orders.pdf')" class="btn-secondary">Export PDF</a>
        </div>

        <div class="card overflow-hidden">
            <table class="admin-table">
                <thead class="admin-table-head">
                    <tr>
                        <th class="px-6 py-3">Order</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tracking</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-beige/30">
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-brand-beige/10">
                        <td class="px-6 py-4 font-medium">{{ order.order_number }}</td>
                        <td class="px-6 py-4">
                            <p>{{ order.user?.name ?? order.shipping_name }}</p>
                            <p class="text-xs text-brand-black/50">{{ order.shipping_email }}</p>
                        </td>
                        <td class="px-6 py-4">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4">
                            <span :class="statusColor(order.status)" class="rounded-full px-2 py-1 text-xs font-medium uppercase">{{ order.status }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-brand-black/60">
                            <span v-if="order.tracking_number">{{ order.tracking_number }}</span>
                            <span v-else>—</span>
                        </td>
                        <td class="px-6 py-4 font-medium">${{ Number(order.total).toFixed(2) }}</td>
                        <td class="px-6 py-4">
                            <Link :href="route('admin.orders.show', order.id)" class="text-brand-copper hover:underline">Manage</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="orders.links" />
    </AdminLayout>
</template>
