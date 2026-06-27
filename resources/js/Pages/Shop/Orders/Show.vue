<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import OrderTrackingTimeline from '@/Components/OrderTrackingTimeline.vue';

defineProps({
    order: Object,
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
    <Head :title="`Order ${order.order_number}`" />

    <ShopLayout>
        <Link :href="route('orders.index')" class="text-sm text-brand-copper hover:underline">← Back to Orders</Link>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl text-brand-black">Order {{ order.order_number }}</h1>
                <p class="text-sm text-brand-black/60">Placed on {{ new Date(order.created_at).toLocaleDateString() }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <span :class="statusColor(order.status)" class="rounded-full px-4 py-1.5 text-sm font-medium uppercase">{{ order.status_label }}</span>
                <p class="text-lg font-semibold text-brand-black">${{ Number(order.total).toFixed(2) }}</p>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <h2 class="font-display text-lg text-brand-black">Delivery Status</h2>
                    <div class="mt-6">
                        <OrderTrackingTimeline :progress="order.status_progress" />
                    </div>

                    <div v-if="order.tracking_number" class="mt-8 rounded-lg border border-brand-beige/40 bg-brand-beige/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-brand-black/60">Tracking Number</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <span v-if="order.carrier_label" class="text-sm font-medium">{{ order.carrier_label }}</span>
                            <code class="rounded bg-brand-white px-2 py-1 text-sm font-mono">{{ order.tracking_number }}</code>
                            <a
                                v-if="order.tracking_url"
                                :href="order.tracking_url"
                                target="_blank"
                                rel="noopener"
                                class="text-sm font-semibold text-brand-copper hover:underline"
                            >
                                Track shipment →
                            </a>
                        </div>
                    </div>
                </div>

                <div v-if="order.status_histories?.length" class="card p-6">
                    <h2 class="font-display text-lg text-brand-black">Order Activity</h2>
                    <ol class="mt-4 space-y-4">
                        <li v-for="event in order.status_histories" :key="event.id" class="border-l-2 border-brand-beige/50 pl-4">
                            <p class="text-sm font-medium text-brand-black">{{ event.status_label }}</p>
                            <p v-if="event.note" class="mt-1 text-sm text-brand-black/70">{{ event.note }}</p>
                            <p class="mt-1 text-xs text-brand-black/50">{{ new Date(event.created_at).toLocaleString() }}</p>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card h-fit p-6">
                <h2 class="font-display text-lg text-brand-black">Order Summary</h2>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-brand-black/60">Subtotal</span><span>${{ Number(order.subtotal).toFixed(2) }}</span></div>
                    <div class="flex justify-between"><span class="text-brand-black/60">Shipping</span><span>${{ Number(order.shipping).toFixed(2) }}</span></div>
                    <div class="flex justify-between border-t border-brand-beige/40 pt-2 text-base font-semibold"><span>Total</span><span>${{ Number(order.total).toFixed(2) }}</span></div>
                </div>

                <h2 class="font-display mt-8 text-lg text-brand-black">Shipping Address</h2>
                <div class="mt-4 space-y-1 text-sm text-brand-black/70">
                    <p class="font-medium text-brand-black">{{ order.shipping_name }}</p>
                    <p>{{ order.shipping_email }}</p>
                    <p v-if="order.shipping_phone">{{ order.shipping_phone }}</p>
                    <p>{{ order.shipping_address }}</p>
                    <p>{{ order.shipping_city }}, {{ order.shipping_zip }}</p>
                    <p v-if="order.notes" class="mt-4 border-t border-brand-beige/30 pt-4"><strong>Notes:</strong> {{ order.notes }}</p>
                </div>
                <Link :href="route('track-order.index')" class="mt-6 inline-block text-sm text-brand-copper hover:underline">Track another order</Link>
            </div>
        </div>
    </ShopLayout>
</template>
