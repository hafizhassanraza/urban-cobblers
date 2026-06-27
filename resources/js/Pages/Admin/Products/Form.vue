<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { imageUrl } from '@/utils/image';

const props = defineProps({
    product: Object,
    categories: Array,
});

const form = useForm({
    category_id: props.product?.category_id || props.categories[0]?.id || '',
    name: props.product?.name || '',
    sku: props.product?.sku || '',
    short_description: props.product?.short_description || '',
    description: props.product?.description || '',
    price: props.product?.price || '',
    compare_price: props.product?.compare_price || '',
    stock: props.product?.stock ?? 0,
    is_featured: props.product?.is_featured ?? false,
    is_ready_to_wear: props.product?.is_ready_to_wear ?? false,
    is_active: props.product?.is_active ?? true,
    image: null,
});

const submit = () => {
    if (props.product) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.products.update', props.product.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('admin.products.store'), { forceFormData: true });
    }
};
</script>

<template>
    <Head :title="product ? 'Edit Product' : 'Add Product'" />

    <AdminLayout>
        <template #title>{{ product ? 'Edit Product' : 'Add Product' }}</template>

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div>
                <label class="block text-sm font-medium">Category</label>
                <select v-model="form.category_id" required class="input-field mt-1">
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input v-model="form.name" type="text" required class="input-field mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium">SKU</label>
                    <input v-model="form.sku" type="text" class="input-field mt-1" placeholder="Auto-generated if empty" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Short Description</label>
                <input v-model="form.short_description" type="text" maxlength="500" class="input-field mt-1" />
            </div>

            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea v-model="form.description" rows="5" class="input-field mt-1" />
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium">Price ($)</label>
                    <input v-model="form.price" type="number" step="0.01" min="0" required class="input-field mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Compare Price ($)</label>
                    <input v-model="form.compare_price" type="number" step="0.01" min="0" class="input-field mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Stock</label>
                    <input v-model="form.stock" type="number" min="0" required class="input-field mt-1" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Image</label>
                <input type="file" accept="image/*" class="mt-1" @input="form.image = $event.target.files[0]" />
                <img v-if="product?.image" :src="imageUrl(product.image)" class="mt-2 h-24 rounded object-cover" />
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2">
                    <input v-model="form.is_featured" type="checkbox" class="rounded border-brand-beige text-brand-copper focus:ring-brand-copper" />
                    <span class="text-sm">Featured</span>
                </label>
                <label class="flex items-center gap-2">
                    <input v-model="form.is_ready_to_wear" type="checkbox" class="rounded border-brand-beige text-brand-copper focus:ring-brand-copper" />
                    <span class="text-sm">Ready to Wear</span>
                </label>
                <label class="flex items-center gap-2">
                    <input v-model="form.is_active" type="checkbox" class="rounded border-brand-beige text-brand-copper focus:ring-brand-copper" />
                    <span class="text-sm">Active</span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="btn-primary" :disabled="form.processing">
                    {{ product ? 'Update Product' : 'Create Product' }}
                </button>
                <Link :href="route('admin.products.index')" class="btn-secondary">Cancel</Link>
            </div>
        </form>
    </AdminLayout>
</template>
