<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({ ...props.settings });

const submit = () => {
    form.put(route('admin.settings.update'));
};
</script>

<template>
    <Head title="Settings" />

    <AdminLayout>
        <template #title>Store Settings</template>

        <form @submit.prevent="submit" class="max-w-xl space-y-6">
            <div class="card space-y-4 p-6">
                <h2 class="font-display text-lg">General</h2>
                <div>
                    <label class="block text-sm font-medium">Store Name</label>
                    <input v-model="form.store_name" type="text" required class="input-field mt-1" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium">Store Email</label>
                        <input v-model="form.store_email" type="email" required class="input-field mt-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Store Phone</label>
                        <input v-model="form.store_phone" type="text" class="input-field mt-1" />
                    </div>
                </div>
            </div>

            <div class="card space-y-4 p-6">
                <h2 class="font-display text-lg">Commerce</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium">Shipping Rate ($)</label>
                        <input v-model="form.shipping_rate" type="number" step="0.01" min="0" required class="input-field mt-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Currency Symbol</label>
                        <input v-model="form.currency_symbol" type="text" required class="input-field mt-1 max-w-[80px]" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium">Low Stock Threshold</label>
                    <input v-model="form.low_stock_threshold" type="number" min="0" required class="input-field mt-1 max-w-[120px]" />
                    <p class="mt-1 text-xs text-brand-black/50">Products at or below this quantity trigger low-stock alerts.</p>
                </div>
            </div>

            <button type="submit" class="btn-primary" :disabled="form.processing">Save Settings</button>
        </form>
    </AdminLayout>
</template>
