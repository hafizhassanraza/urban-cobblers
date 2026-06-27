<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import OrderTrackingTimeline from '@/Components/OrderTrackingTimeline.vue';

const props = defineProps({
    order: Object,
    statuses: Array,
    carriers: Array,
});

const fulfillmentForm = useForm({
    status: props.order.status,
    tracking_number: props.order.tracking_number || '',
    carrier: props.order.carrier || '',
    note: '',
});

const detailsForm = useForm({
    shipping_name: props.order.shipping_name,
    shipping_email: props.order.shipping_email,
    shipping_phone: props.order.shipping_phone || '',
    shipping_address: props.order.shipping_address,
    shipping_city: props.order.shipping_city,
    shipping_zip: props.order.shipping_zip,
    notes: props.order.notes || '',
    admin_notes: props.order.admin_notes || '',
    status: props.order.status,
    tracking_number: props.order.tracking_number || '',
    carrier: props.order.carrier || '',
    note: '',
});

const updateFulfillment = () => {
    fulfillmentForm.patch(route('admin.orders.update-status', props.order.id), {
        onSuccess: () => { fulfillmentForm.note = ''; },
    });
};

const saveDetails = () => {
    detailsForm.put(route('admin.orders.update', props.order.id), {
        onSuccess: () => { detailsForm.note = ''; },
    });
};

const quickStatus = (status) => {
    fulfillmentForm.status = status;
    updateFulfillment();
};

const statusColor = (s) => ({
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
}[s] || '');

const carrierLabel = (c) => ({
    usps: 'USPS', ups: 'UPS', fedex: 'FedEx', dhl: 'DHL', other: 'Other',
}[c] || c);
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />

    <AdminLayout>
        <template #title>Order {{ order.order_number }}</template>

        <Link :href="route('admin.orders.index')" class="text-sm text-brand-copper hover:underline">← Back to Orders</Link>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <span :class="statusColor(order.status)" class="rounded-full px-3 py-1 text-xs font-semibold uppercase">{{ order.status_label }}</span>
            <span class="text-sm text-brand-black/60">Placed {{ new Date(order.created_at).toLocaleString() }}</span>
            <span v-if="order.shipped_at" class="text-sm text-brand-black/60">· Shipped {{ new Date(order.shipped_at).toLocaleDateString() }}</span>
            <span v-if="order.delivered_at" class="text-sm text-brand-black/60">· Delivered {{ new Date(order.delivered_at).toLocaleDateString() }}</span>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-6">
                    <h2 class="font-display text-lg">Order Items</h2>
                    <table class="admin-table mt-4">
                        <thead class="admin-table-head">
                            <tr>
                                <th class="pb-2">Product</th>
                                <th class="pb-2">Price</th>
                                <th class="pb-2">Qty</th>
                                <th class="pb-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-beige/30">
                            <tr v-for="item in order.items" :key="item.id">
                                <td class="py-3">
                                    <Link v-if="item.product" :href="route('admin.products.edit', item.product.id)" class="hover:text-brand-copper">{{ item.product_name }}</Link>
                                    <span v-else>{{ item.product_name }}</span>
                                </td>
                                <td class="py-3">${{ Number(item.price).toFixed(2) }}</td>
                                <td class="py-3">{{ item.quantity }}</td>
                                <td class="py-3 text-right font-medium">${{ Number(item.total).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4 space-y-1 border-t border-brand-beige/40 pt-4 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>${{ Number(order.subtotal).toFixed(2) }}</span></div>
                        <div class="flex justify-between"><span>Shipping</span><span>${{ Number(order.shipping).toFixed(2) }}</span></div>
                        <div class="flex justify-between text-lg font-semibold"><span>Total</span><span>${{ Number(order.total).toFixed(2) }}</span></div>
                    </div>
                </div>

                <div class="card p-6">
                    <h2 class="font-display text-lg">Delivery Progress</h2>
                    <div class="mt-6">
                        <OrderTrackingTimeline :progress="order.status_progress" compact />
                    </div>
                </div>

                <div v-if="order.status_histories?.length" class="card p-6">
                    <h2 class="font-display text-lg">Status History</h2>
                    <ol class="mt-4 space-y-4">
                        <li v-for="event in order.status_histories" :key="event.id" class="border-l-2 border-brand-beige/50 pl-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span :class="statusColor(event.status)" class="rounded-full px-2 py-0.5 text-xs font-medium uppercase">{{ event.status_label }}</span>
                                <span class="text-xs text-brand-black/50">{{ new Date(event.created_at).toLocaleString() }}</span>
                                <span v-if="event.updated_by" class="text-xs text-brand-black/50">by {{ event.updated_by }}</span>
                            </div>
                            <p v-if="event.note" class="mt-1 text-sm text-brand-black/70">{{ event.note }}</p>
                            <p v-if="event.tracking_number" class="mt-1 text-xs text-brand-black/60">
                                {{ event.carrier_label }} · {{ event.tracking_number }}
                            </p>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h2 class="font-display text-lg">Quick Actions</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button
                            v-for="s in ['processing', 'shipped', 'delivered']"
                            :key="s"
                            type="button"
                            class="rounded border border-brand-beige/60 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition hover:border-brand-copper hover:text-brand-copper disabled:opacity-40"
                            :disabled="order.status === s || order.status === 'cancelled'"
                            @click="quickStatus(s)"
                        >
                            Mark {{ s }}
                        </button>
                    </div>
                </div>

                <div class="card p-6">
                    <h2 class="font-display text-lg">Fulfillment</h2>
                    <form @submit.prevent="updateFulfillment" class="mt-4 space-y-3">
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Status</label>
                            <select v-model="fulfillmentForm.status" class="input-field mt-1">
                                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Carrier</label>
                            <select v-model="fulfillmentForm.carrier" class="input-field mt-1">
                                <option value="">Select carrier</option>
                                <option v-for="c in carriers" :key="c" :value="c">{{ carrierLabel(c) }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Tracking Number</label>
                            <input v-model="fulfillmentForm.tracking_number" type="text" class="input-field mt-1" placeholder="Tracking number" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Customer Note</label>
                            <textarea v-model="fulfillmentForm.note" rows="2" class="input-field mt-1" placeholder="Visible to customer in order activity" />
                        </div>
                        <p v-if="fulfillmentForm.status === 'cancelled'" class="text-xs text-amber-700">Cancelling will restore product stock.</p>
                        <a
                            v-if="order.tracking_url"
                            :href="order.tracking_url"
                            target="_blank"
                            rel="noopener"
                            class="block text-xs text-brand-copper hover:underline"
                        >
                            Open carrier tracking →
                        </a>
                        <button type="submit" class="btn-primary w-full" :disabled="fulfillmentForm.processing">Update Fulfillment</button>
                    </form>
                </div>

                <div class="card p-6">
                    <h2 class="font-display text-lg">Customer</h2>
                    <div class="mt-3 space-y-1 text-sm">
                        <p class="font-medium">{{ order.user?.name ?? order.shipping_name }}</p>
                        <p class="text-brand-black/60">{{ order.user?.email ?? order.shipping_email }}</p>
                        <Link v-if="order.user" :href="route('admin.customers.show', order.user.id)" class="mt-2 inline-block text-brand-copper hover:underline">View profile</Link>
                    </div>
                </div>

                <div class="card p-6">
                    <h2 class="font-display text-lg">Shipping & Notes</h2>
                    <form @submit.prevent="saveDetails" class="mt-4 space-y-3">
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Name</label>
                            <input v-model="detailsForm.shipping_name" type="text" class="input-field mt-1" required />
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Email</label>
                            <input v-model="detailsForm.shipping_email" type="email" class="input-field mt-1" required />
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Phone</label>
                            <input v-model="detailsForm.shipping_phone" type="text" class="input-field mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Address</label>
                            <textarea v-model="detailsForm.shipping_address" rows="2" class="input-field mt-1" required />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">City</label>
                                <input v-model="detailsForm.shipping_city" type="text" class="input-field mt-1" required />
                            </div>
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">ZIP</label>
                                <input v-model="detailsForm.shipping_zip" type="text" class="input-field mt-1" required />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Customer Notes</label>
                            <textarea v-model="detailsForm.notes" rows="2" class="input-field mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium uppercase tracking-wider text-brand-black/60">Internal Admin Notes</label>
                            <textarea v-model="detailsForm.admin_notes" rows="3" class="input-field mt-1" placeholder="Only visible to admins" />
                        </div>
                        <button type="submit" class="btn-secondary w-full" :disabled="detailsForm.processing">Save Details</button>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
