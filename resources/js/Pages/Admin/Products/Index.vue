<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { imageUrl } from '@/utils/image';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');
const status = ref(props.filters.status || '');
const lowStock = ref(props.filters.low_stock || false);
const selected = ref([]);

const applyFilters = () => {
    router.get(route('admin.products.index'), {
        search: search.value || undefined,
        category_id: categoryId.value || undefined,
        status: status.value || undefined,
        low_stock: lowStock.value ? 1 : undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
};

const toggleAll = (e) => {
    selected.value = e.target.checked ? props.products.data.map(p => p.id) : [];
};

const bulkAction = (action) => {
    if (! selected.value.length) return;
    if (action === 'delete' && ! confirm('Delete selected products?')) return;

    router.post(route('admin.products.bulk'), {
        ids: selected.value,
        action,
    }, { preserveScroll: true, onSuccess: () => { selected.value = []; } });
};
</script>

<template>
    <Head title="Products" />

    <AdminLayout>
        <template #title>Products</template>

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap gap-3">
                <input v-model="search" type="search" placeholder="Search name or SKU..." class="input-field max-w-xs" @input="onSearch" />
                <select v-model="categoryId" class="input-field w-auto" @change="applyFilters">
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <select v-model="status" class="input-field w-auto" @change="applyFilters">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="lowStock" type="checkbox" class="rounded text-brand-copper" @change="applyFilters" />
                    Low stock
                </label>
            </div>
            <Link :href="route('admin.products.create')" class="btn-primary">Add Product</Link>
        </div>

        <div v-if="selected.length" class="mb-4 flex flex-wrap items-center gap-3 rounded-lg bg-brand-beige/30 px-4 py-3 text-sm">
            <span>{{ selected.length }} selected</span>
            <button type="button" class="text-green-700 hover:underline" @click="bulkAction('activate')">Activate</button>
            <button type="button" class="text-amber-700 hover:underline" @click="bulkAction('deactivate')">Deactivate</button>
            <button type="button" class="text-red-600 hover:underline" @click="bulkAction('delete')">Delete</button>
        </div>

        <div class="card overflow-hidden">
            <table class="admin-table">
                <thead class="admin-table-head">
                    <tr>
                        <th class="px-4 py-3 w-10"><input type="checkbox" @change="toggleAll" /></th>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Stock</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-beige/30">
                    <tr v-for="product in products.data" :key="product.id" class="hover:bg-brand-beige/10">
                        <td class="px-4 py-4">
                            <input v-model="selected" type="checkbox" :value="product.id" />
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img v-if="product.image" :src="imageUrl(product.image)" class="h-10 w-10 rounded object-cover bg-brand-beige/30" />
                                <div v-else class="flex h-10 w-10 items-center justify-center rounded bg-brand-beige/30 text-xs text-brand-copper">UC</div>
                                <div>
                                    <p class="font-medium">{{ product.name }}</p>
                                    <p v-if="product.is_featured" class="text-xs text-brand-copper">Featured</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-brand-black/60">{{ product.sku || '—' }}</td>
                        <td class="px-6 py-4">{{ product.category?.name }}</td>
                        <td class="px-6 py-4">${{ Number(product.price).toFixed(2) }}</td>
                        <td class="px-6 py-4">
                            <span :class="product.stock <= 5 ? 'text-amber-600 font-semibold' : ''">{{ product.stock }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="product.is_active ? 'text-green-600' : 'text-red-600'" class="text-xs font-medium uppercase">
                                {{ product.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <Link :href="route('admin.products.edit', product.id)" class="mr-3 text-brand-copper hover:underline">Edit</Link>
                            <Link :href="route('shop.show', product.slug)" class="mr-3 text-brand-black/50 hover:underline" target="_blank">View</Link>
                            <Link
                                :href="route('admin.products.destroy', product.id)"
                                method="delete"
                                as="button"
                                class="text-red-600 hover:underline"
                                @click="(e) => { if (!confirm('Delete this product?')) e.preventDefault(); }"
                            >Delete</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="products.links" />
    </AdminLayout>
</template>
