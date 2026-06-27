<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminChart from '@/Components/Admin/AdminChart.vue';
import { imageUrl } from '@/utils/image';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    lowStockProducts: Array,
    topProducts: Array,
    revenueByMonth: Array,
    ordersByDay: Array,
    salesByStatus: Object,
});

const statusColor = (status) => ({
    pending: 'text-yellow-600 bg-yellow-50',
    processing: 'text-blue-600 bg-blue-50',
    shipped: 'text-purple-600 bg-purple-50',
    delivered: 'text-green-600 bg-green-50',
    cancelled: 'text-red-600 bg-red-50',
}[status] || 'text-brand-black/60 bg-brand-beige/20');

const revenueChart = computed(() => ({
    labels: props.revenueByMonth.map((item) => item.label),
    datasets: [
        {
            label: 'Revenue ($)',
            data: props.revenueByMonth.map((item) => item.revenue),
            borderColor: '#B87333',
            backgroundColor: 'rgba(184, 115, 51, 0.12)',
            fill: true,
            tension: 0.35,
        },
    ],
}));

const ordersChart = computed(() => ({
    labels: props.ordersByDay.map((item) => item.label),
    datasets: [
        {
            label: 'Orders',
            data: props.ordersByDay.map((item) => item.orders),
            backgroundColor: '#111111',
            borderRadius: 6,
        },
    ],
}));

const statusChart = computed(() => {
    const labels = Object.keys(props.salesByStatus || {});
    const values = Object.values(props.salesByStatus || {});

    return {
        labels: labels.map((s) => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [
            {
                data: values,
                backgroundColor: ['#EAB308', '#3B82F6', '#A855F7', '#22C55E', '#EF4444'],
                borderWidth: 0,
            },
        ],
    };
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #title>Dashboard</template>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Total Revenue</p>
                <p class="mt-2 font-display text-3xl text-brand-copper">${{ Number(stats.revenue).toFixed(2) }}</p>
                <p class="mt-1 text-xs text-brand-black/50">${{ Number(stats.revenue_month).toFixed(2) }} this month</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Orders</p>
                <p class="mt-2 font-display text-3xl text-brand-black">{{ stats.orders }}</p>
                <p class="mt-1 text-xs text-amber-600">{{ stats.pending_orders }} pending</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Products</p>
                <p class="mt-2 font-display text-3xl text-brand-black">{{ stats.active_products }}</p>
                <p class="mt-1 text-xs text-brand-black/50">{{ stats.products }} total</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Customers</p>
                <p class="mt-2 font-display text-3xl text-brand-black">{{ stats.customers }}</p>
                <Link :href="route('admin.customers.index')" class="mt-1 inline-block text-xs text-brand-copper hover:underline">View all</Link>
            </div>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <Link :href="route('admin.products.index', { low_stock: 1 })" class="card p-4 transition hover:border-amber-300">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Low Stock</p>
                <p class="mt-1 text-2xl font-bold" :class="stats.low_stock ? 'text-amber-600' : 'text-brand-black'">{{ stats.low_stock }}</p>
            </Link>
            <Link :href="route('admin.reviews.index', { status: 'hidden' })" class="card p-4 transition hover:border-brand-copper/40">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Hidden Reviews</p>
                <p class="mt-1 text-2xl font-bold text-brand-black">{{ stats.pending_reviews }}</p>
            </Link>
            <Link :href="route('admin.contact.index', { read: 'unread' })" class="card p-4 transition hover:border-brand-copper/40">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Unread Messages</p>
                <p class="mt-1 text-2xl font-bold" :class="stats.unread_messages ? 'text-brand-copper' : 'text-brand-black'">{{ stats.unread_messages }}</p>
            </Link>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-3">
            <div class="card p-6 xl:col-span-2">
                <h2 class="font-display text-lg text-brand-black">Revenue Trend</h2>
                <p class="mt-1 text-xs text-brand-black/50">Last 6 months</p>
                <div class="mt-4">
                    <AdminChart type="line" :labels="revenueChart.labels" :datasets="revenueChart.datasets" :height="280" />
                </div>
            </div>

            <div class="card p-6">
                <h2 class="font-display text-lg text-brand-black">Orders by Status</h2>
                <div class="mt-4">
                    <AdminChart type="doughnut" :labels="statusChart.labels" :datasets="statusChart.datasets" :height="280" />
                </div>
            </div>
        </div>

        <div class="mt-6 card p-6">
            <h2 class="font-display text-lg text-brand-black">Daily Orders</h2>
            <p class="mt-1 text-xs text-brand-black/50">Last 14 days</p>
            <div class="mt-4">
                <AdminChart type="bar" :labels="ordersChart.labels" :datasets="ordersChart.datasets" :height="260" />
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <div class="card lg:col-span-2">
                <div class="flex items-center justify-between border-b border-brand-beige/40 px-6 py-4">
                    <h2 class="font-display text-xl text-brand-black">Recent Orders</h2>
                    <div class="flex gap-3">
                        <Link :href="route('admin.reports.index')" class="text-sm text-brand-black/60 hover:text-brand-copper">Reports</Link>
                        <a :href="route('admin.orders.export')" class="text-sm text-brand-black/60 hover:text-brand-copper">Export CSV</a>
                        <Link :href="route('admin.orders.index')" class="text-sm text-brand-copper hover:underline">View All</Link>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead class="admin-table-head">
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-beige/30">
                            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-brand-beige/10">
                                <td class="px-6 py-4">
                                    <Link :href="route('admin.orders.show', order.id)" class="font-medium text-brand-copper hover:underline">{{ order.order_number }}</Link>
                                </td>
                                <td class="px-6 py-4">{{ order.user?.name ?? order.shipping_name }}</td>
                                <td class="px-6 py-4">
                                    <span :class="statusColor(order.status)" class="rounded-full px-2 py-1 text-xs font-medium capitalize">{{ order.status }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium">${{ Number(order.total).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h2 class="font-display text-lg text-brand-black">Top Sellers</h2>
                    <ul class="mt-4 space-y-3">
                        <li v-for="(item, i) in topProducts" :key="i" class="flex items-center justify-between text-sm">
                            <span class="line-clamp-1 pr-2">{{ item.product_name }}</span>
                            <span class="shrink-0 font-medium text-brand-copper">{{ item.total_qty }} sold</span>
                        </li>
                        <li v-if="!topProducts.length" class="text-sm text-brand-black/50">No sales data yet.</li>
                    </ul>
                </div>

                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-lg text-brand-black">Low Stock</h2>
                        <Link :href="route('admin.products.index', { low_stock: 1 })" class="text-xs text-brand-copper hover:underline">View all</Link>
                    </div>
                    <ul class="mt-4 space-y-3">
                        <li v-for="product in lowStockProducts" :key="product.id" class="flex items-center gap-3">
                            <img v-if="product.image" :src="imageUrl(product.image)" class="h-8 w-8 rounded object-cover" />
                            <div class="min-w-0 flex-1">
                                <Link :href="route('admin.products.edit', product.id)" class="block truncate text-sm font-medium hover:text-brand-copper">{{ product.name }}</Link>
                            </div>
                            <span class="text-xs font-semibold text-amber-600">{{ product.stock }} left</span>
                        </li>
                        <li v-if="!lowStockProducts.length" class="text-sm text-brand-black/50">All products well stocked.</li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
