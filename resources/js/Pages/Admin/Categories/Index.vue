<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { imageUrl } from '@/utils/image';

defineProps({
    categories: Array,
});

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    description: '',
    image: '',
    image_file: null,
    sort_order: 0,
    is_active: true,
});

const editCategory = (category) => {
    editingId.value = category.id;
    form.name = category.name;
    form.description = category.description || '';
    form.image = category.image || '';
    form.image_file = null;
    form.sort_order = category.sort_order || 0;
    form.is_active = category.is_active;
    showForm.value = true;
};

const resetForm = () => {
    editingId.value = null;
    form.reset();
    form.is_active = true;
    showForm.value = false;
};

const submit = () => {
    const options = { forceFormData: true, onSuccess: resetForm };

    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.categories.update', editingId.value), options);
    } else {
        form.post(route('admin.categories.store'), options);
    }
};

const deleteCategory = (category) => {
    if (confirm('Delete this category?')) {
        router.delete(route('admin.categories.destroy', category.id));
    }
};
</script>

<template>
    <Head title="Categories" />

    <AdminLayout>
        <template #title>Categories</template>

        <div class="mb-6">
            <button v-if="!showForm" @click="showForm = true" class="btn-primary">Add Category</button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="card mb-6 max-w-lg space-y-4 p-6">
            <h3 class="font-display text-lg">{{ editingId ? 'Edit' : 'New' }} Category</h3>
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input v-model="form.name" type="text" required class="input-field mt-1" />
            </div>
            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea v-model="form.description" rows="2" class="input-field mt-1" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Image URL</label>
                    <input v-model="form.image" type="text" placeholder="/images/shoes/..." class="input-field mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Or Upload Image</label>
                    <input type="file" accept="image/*" class="mt-1" @input="form.image_file = $event.target.files[0]" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">Sort Order</label>
                <input v-model.number="form.sort_order" type="number" min="0" class="input-field mt-1 max-w-[120px]" />
            </div>
            <label class="flex items-center gap-2">
                <input v-model="form.is_active" type="checkbox" class="rounded text-brand-copper focus:ring-brand-copper" />
                <span class="text-sm">Active</span>
            </label>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary" :disabled="form.processing">Save</button>
                <button type="button" @click="resetForm" class="btn-secondary">Cancel</button>
            </div>
        </form>

        <div class="card overflow-hidden">
            <table class="admin-table">
                <thead class="admin-table-head">
                    <tr>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Products</th>
                        <th class="px-6 py-3">Sort</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-beige/30">
                    <tr v-for="category in categories" :key="category.id" class="hover:bg-brand-beige/10">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img v-if="category.image" :src="imageUrl(category.image)" class="h-10 w-10 rounded object-cover" />
                                <div>
                                    <p class="font-medium">{{ category.name }}</p>
                                    <p class="text-xs text-brand-black/50">{{ category.slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ category.products_count }}</td>
                        <td class="px-6 py-4">{{ category.sort_order }}</td>
                        <td class="px-6 py-4">
                            <span :class="category.is_active ? 'text-green-600' : 'text-red-600'" class="text-xs font-medium uppercase">
                                {{ category.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="editCategory(category)" class="mr-3 text-brand-copper hover:underline">Edit</button>
                            <button @click="deleteCategory(category)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
