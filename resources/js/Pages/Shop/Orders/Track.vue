<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import OrderTrackingTimeline from '@/Components/OrderTrackingTimeline.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps({
    order: Object,
    lookup: Object,
});

const form = useForm({
    order_number: props.lookup?.order_number || '',
    email: props.lookup?.email || '',
});

const submit = () => {
    form.post(route('track-order.lookup'));
};

const statusColor = (status) => ({
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
}[status] || 'bg-gray-100 text-gray-800');
</script>

<template>
    <Head title="Track Your Order" />

    <ShopLayout>
        <div class="mx-auto max-w-3xl">
            <h1 class="font-display text-4xl text-brand-black">Track Your Order</h1>
            <p class="mt-2 text-brand-black/60">Enter your order number and email to see delivery status.</p>

            <FlashMessage />

            <form @submit.prevent="submit" class="card mt-8 p-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Order Number</label>
                        <input v-model="form.order_number" type="text" required placeholder="UC-XXXXXXXX" class="input-field mt-1 uppercase" />
                        <p v-if="form.errors.order_number" class="mt-1 text-xs text-red-600">{{ form.errors.order_number }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Email Address</label>
                        <input v-model="form.email" type="email" required placeholder="you@example.com" class="input-field mt-1" />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>
                </div>
                <button type="submit" class="btn-primary mt-6" :disabled="form.processing">
                    {{ form.processing ? 'Looking up...' : 'Track Order' }}
                </button>
            </form>

            <div v-if="order" class="mt-10 space-y-6">
                <div class="card p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-brand-copper">Order Found</p>
                            <h2 class="mt-1 font-display text-2xl text-brand-black">{{ order.order_number }}</h2>
                            <p class="mt-1 text-sm text-brand-black/60">Placed {{ new Date(order.created_at).toLocaleDateString() }}</p>
                        </div>
                        <span :class="statusColor(order.status)" class="rounded-full px-4 py-1.5 text-sm font-medium uppercase">
                            {{ order.status_label }}
                        </span>
                    </div>

                    <div class="mt-8">
                        <OrderTrackingTimeline :progress="order.status_progress" />
                    </div>

                    <div v-if="order.tracking_number" class="mt-8 rounded-lg border border-brand-beige/40 bg-brand-beige/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-brand-black/60">Shipment Tracking</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <span v-if="order.carrier_label" class="text-sm font-medium text-brand-black">{{ order.carrier_label }}</span>
                            <code class="rounded bg-brand-white px-2 py-1 text-sm font-mono">{{ order.tracking_number }}</code>
                            <a
                                v-if="order.tracking_url"
                                :href="order.tracking_url"
                                target="_blank"
                                rel="noopener"
                                class="text-sm font-semibold text-brand-copper hover:underline"
                            >
                                Track on carrier site →
                            </a>
                        </div>
                        <p v-if="order.shipped_at" class="mt-2 text-xs text-brand-black/60">
                            Shipped {{ new Date(order.shipped_at).toLocaleString() }}
                        </p>
                    </div>

                    <div class="mt-8 flex justify-between border-t border-brand-beige/40 pt-4 text-lg font-semibold">
                        <span>Total</span>
                        <span>${{ Number(order.total).toFixed(2) }}</span>
                    </div>
                </div>

                <div v-if="order.status_histories?.length" class="card p-6">
                    <h3 class="font-display text-lg text-brand-black">Activity</h3>
                    <ol class="mt-4 space-y-4">
                        <li v-for="event in order.status_histories" :key="event.id" class="border-l-2 border-brand-beige/50 pl-4">
                            <p class="text-sm font-medium text-brand-black">{{ event.status_label }}</p>
                            <p v-if="event.note" class="mt-1 text-sm text-brand-black/70">{{ event.note }}</p>
                            <p class="mt-1 text-xs text-brand-black/50">{{ new Date(event.created_at).toLocaleString() }}</p>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
