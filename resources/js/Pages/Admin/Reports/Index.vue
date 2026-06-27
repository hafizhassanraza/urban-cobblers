<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminChart from '@/Components/Admin/AdminChart.vue';

const props = defineProps({
    filters: Object,
    summary: Object,
    revenueByMonth: Array,
    ordersByDay: Array,
    salesByStatus: Object,
    topProducts: Array,
});

const dateFrom = ref(props.filters.date_from);
const dateTo = ref(props.filters.date_to);

const applyFilters = () => {
    router.get(route('admin.reports.index'), {
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, { preserveState: true, replace: true });
};

const exportQuery = computed(() => {
    const params = new URLSearchParams({
        date_from: dateFrom.value,
        date_to: dateTo.value,
    });
    return params.toString();
});

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
        {
            label: 'Orders',
            data: props.revenueByMonth.map((item) => item.orders),
            borderColor: '#111111',
            backgroundColor: 'rgba(17, 17, 17, 0.05)',
            fill: false,
            tension: 0.35,
            yAxisID: 'y1',
        },
    ],
}));

const ordersChart = computed(() => ({
    labels: props.ordersByDay.map((item) => item.label),
    datasets: [
        {
            label: 'Daily Orders',
            data: props.ordersByDay.map((item) => item.orders),
            backgroundColor: '#B87333',
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
    <Head title="Reports" />

    <AdminLayout>
        <template #title>Reports & Analytics</template>

        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">From</label>
                    <input v-model="dateFrom" type="date" class="input-field mt-1 w-auto" @change="applyFilters" />
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">To</label>
                    <input v-model="dateTo" type="date" class="input-field mt-1 w-auto" @change="applyFilters" />
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a :href="`${route('admin.reports.export.sales.pdf')}?${exportQuery}`" class="btn-secondary !py-2.5 !px-4 !text-xs">Sales PDF</a>
                <a :href="`${route('admin.reports.export.orders.pdf')}?${exportQuery}`" class="btn-secondary !py-2.5 !px-4 !text-xs">Orders PDF</a>
                <a :href="route('admin.orders.export')" class="btn-secondary !py-2.5 !px-4 !text-xs">Orders CSV</a>
                <a :href="route('admin.reports.export.products.pdf')" class="btn-secondary !py-2.5 !px-4 !text-xs">Products PDF</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Total Orders</p>
                <p class="mt-2 font-display text-3xl text-brand-black">{{ summary.total_orders }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Revenue</p>
                <p class="mt-2 font-display text-3xl text-brand-copper">${{ Number(summary.total_revenue).toFixed(2) }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Avg. Order</p>
                <p class="mt-2 font-display text-3xl text-brand-black">${{ Number(summary.average_order).toFixed(2) }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Delivered</p>
                <p class="mt-2 font-display text-3xl text-green-600">{{ summary.delivered_orders }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wider text-brand-black/60">Cancelled</p>
                <p class="mt-2 font-display text-3xl text-red-600">{{ summary.cancelled_orders }}</p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-3">
            <div class="card p-6 xl:col-span-2">
                <h2 class="font-display text-lg text-brand-black">Revenue & Orders</h2>
                <p class="mt-1 text-xs text-brand-black/50">Monthly performance</p>
                <div class="mt-4">
                    <AdminChart type="line" :labels="revenueChart.labels" :datasets="revenueChart.datasets" :height="300" />
                </div>
            </div>
            <div class="card p-6">
                <h2 class="font-display text-lg text-brand-black">Status Breakdown</h2>
                <p class="mt-1 text-xs text-brand-black/50">Selected date range</p>
                <div class="mt-4">
                    <AdminChart type="doughnut" :labels="statusChart.labels" :datasets="statusChart.datasets" :height="300" />
                </div>
            </div>
        </div>

        <div class="mt-6 card p-6">
            <h2 class="font-display text-lg text-brand-black">Daily Order Volume</h2>
            <div class="mt-4">
                <AdminChart type="bar" :labels="ordersChart.labels" :datasets="ordersChart.datasets" :height="260" />
            </div>
        </div>

        <div class="mt-8 card overflow-hidden">
            <div class="border-b border-brand-beige/40 px-6 py-4">
                <h2 class="font-display text-lg text-brand-black">Top Products by Revenue</h2>
                <p class="mt-1 text-xs text-brand-black/50">Within selected date range</p>
            </div>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead class="admin-table-head">
                        <tr>
                            <th>Product</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-beige/30">
                        <tr v-for="(product, i) in topProducts" :key="i" class="hover:bg-brand-beige/10">
                            <td class="px-6 py-4 font-medium">{{ product.product_name }}</td>
                            <td class="px-6 py-4">{{ product.total_qty }}</td>
                            <td class="px-6 py-4 font-medium text-brand-copper">${{ Number(product.total_revenue).toFixed(2) }}</td>
                        </tr>
                        <tr v-if="!topProducts.length">
                            <td colspan="3" class="px-6 py-8 text-center text-brand-black/50">No product sales in this period.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
