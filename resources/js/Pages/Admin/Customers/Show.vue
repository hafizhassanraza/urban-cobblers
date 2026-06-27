<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    customer: Object,
    orders: Object,
    totalSpent: Number,
});

const statusColor = (s) => ({
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
}[s] || '');
</script>

<template>
    <Head :title="customer.name" />

    <AdminLayout>
        <template #title>{{ customer.name }}</template>

        <Link :href="route('admin.customers.index')" class="text-sm text-brand-copper hover:underline">← Back to Customers</Link>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="card p-6 lg:col-span-2">
                <h2 class="font-display text-lg">Order History</h2>
                <table class="admin-table mt-4">
                    <thead class="admin-table-head">
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-beige/30">
                        <tr v-for="order in orders.data" :key="order.id">
                            <td class="py-3">
                                <Link :href="route('admin.orders.show', order.id)" class="font-medium text-brand-copper hover:underline">{{ order.order_number }}</Link>
                            </td>
                            <td class="py-3">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                            <td class="py-3">
                                <span :class="statusColor(order.status)" class="rounded-full px-2 py-0.5 text-xs font-medium uppercase">{{ order.status }}</span>
                            </td>
                            <td class="py-3 text-right font-medium">${{ Number(order.total).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
                <Pagination :links="orders.links" class="mt-4" />
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h2 class="font-display text-lg">Profile</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-brand-black/50">Email</dt><dd class="font-medium">{{ customer.email }}</dd></div>
                        <div><dt class="text-brand-black/50">Phone</dt><dd>{{ customer.phone || '—' }}</dd></div>
                        <div><dt class="text-brand-black/50">Address</dt><dd>{{ customer.address || '—' }}</dd></div>
                        <div><dt class="text-brand-black/50">Member since</dt><dd>{{ new Date(customer.created_at).toLocaleDateString() }}</dd></div>
                    </dl>
                </div>

                <div class="card p-6">
                    <h2 class="font-display text-lg">Stats</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-brand-black/50">Total orders</dt><dd class="font-semibold">{{ customer.orders_count }}</dd></div>
                        <div class="flex justify-between"><dt class="text-brand-black/50">Reviews</dt><dd class="font-semibold">{{ customer.reviews_count }}</dd></div>
                        <div class="flex justify-between border-t border-brand-beige/40 pt-3"><dt class="text-brand-black/50">Lifetime spend</dt><dd class="font-display text-xl text-brand-copper">${{ Number(totalSpent).toFixed(2) }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
