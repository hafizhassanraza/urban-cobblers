<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    activeOrders: Array,
    pastOrders: Object,
});

const statusColor = (status) => ({
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
}[status] || 'bg-gray-100 text-gray-800');
</script>

<template>
    <Head title="My Orders" />

    <ShopLayout>
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-4xl text-brand-black">My Orders</h1>
                <p class="mt-2 text-sm text-brand-black/60">Track current deliveries and browse your order history.</p>
            </div>
            <Link :href="route('track-order.index')" class="text-sm font-semibold text-brand-copper hover:underline">Track by order number →</Link>
        </div>

        <section id="current" class="mt-10 scroll-mt-28">
            <h2 class="font-display text-2xl text-brand-black">Current Orders</h2>
            <p class="mt-1 text-sm text-brand-black/60">Orders in progress with live tracking.</p>

            <div v-if="activeOrders.length" class="mt-6 space-y-4">
                <Link
                    v-for="order in activeOrders"
                    :key="order.id"
                    :href="route('orders.show', order.id)"
                    class="card block overflow-hidden transition hover:border-brand-copper/40"
                >
                    <div class="flex flex-wrap items-center justify-between gap-4 p-6">
                        <div>
                            <p class="font-medium text-brand-black">{{ order.order_number }}</p>
                            <p class="text-sm text-brand-black/60">
                                {{ new Date(order.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <span :class="statusColor(order.status)" class="rounded-full px-3 py-1 text-xs font-medium uppercase">{{ order.status_label }}</span>
                        <p class="font-semibold text-brand-black">${{ Number(order.total).toFixed(2) }}</p>
                        <span class="text-sm font-semibold text-brand-copper">View tracking →</span>
                    </div>
                </Link>
            </div>

            <div v-else class="mt-6 rounded-2xl border border-brand-beige/40 bg-brand-beige/10 py-12 text-center">
                <p class="text-brand-black/60">No active orders right now.</p>
                <Link :href="route('shop.index')" class="btn-primary mt-4 inline-flex">Continue Shopping</Link>
            </div>
        </section>

        <section id="history" class="mt-14 scroll-mt-28">
            <h2 class="font-display text-2xl text-brand-black">Order History</h2>
            <p class="mt-1 text-sm text-brand-black/60">Your previous completed and cancelled orders.</p>

            <div v-if="pastOrders.data.length" class="mt-6 space-y-4">
                <Link
                    v-for="order in pastOrders.data"
                    :key="order.id"
                    :href="route('orders.show', order.id)"
                    class="card block p-6 transition hover:border-brand-copper/40"
                >
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="font-medium text-brand-black">{{ order.order_number }}</p>
                            <p class="text-sm text-brand-black/60">
                                {{ new Date(order.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <span :class="statusColor(order.status)" class="rounded-full px-3 py-1 text-xs font-medium uppercase">{{ order.status_label }}</span>
                        <p class="font-semibold text-brand-black">${{ Number(order.total).toFixed(2) }}</p>
                        <span class="text-sm text-brand-copper">View order →</span>
                    </div>
                </Link>
                <Pagination :links="pastOrders.links" />
            </div>

            <div v-else class="mt-6 rounded-2xl border border-brand-beige/40 py-12 text-center text-brand-black/60">
                <p>No previous orders yet.</p>
            </div>
        </section>
    </ShopLayout>
</template>
