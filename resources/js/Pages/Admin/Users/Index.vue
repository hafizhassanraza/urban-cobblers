<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const role = ref(props.filters.role || '');
const showForm = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    phone: '',
    address: '',
    is_admin: false,
});

const applyFilters = () => {
    router.get(route('admin.users.index'), {
        search: search.value || undefined,
        role: role.value || undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
};

const openCreate = () => {
    editingUser.value = null;
    form.reset();
    form.is_admin = false;
    showForm.value = true;
};

const openEdit = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.phone = user.phone || '';
    form.address = user.address || '';
    form.is_admin = user.is_admin;
    showForm.value = true;
};

const submit = () => {
    if (editingUser.value) {
        form.put(route('admin.users.update', editingUser.value.id), { onSuccess: () => { showForm.value = false; } });
    } else {
        form.post(route('admin.users.store'), { onSuccess: () => { showForm.value = false; form.reset(); } });
    }
};

const deleteUser = (user) => {
    if (confirm(`Delete ${user.name}?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};
</script>

<template>
    <Head title="Users" />

    <AdminLayout>
        <template #title>Users & Admins</template>

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap gap-3">
                <input v-model="search" type="search" placeholder="Search users..." class="input-field max-w-xs" @input="onSearch" />
                <select v-model="role" class="input-field w-auto" @change="applyFilters">
                    <option value="">All Roles</option>
                    <option value="admin">Admins</option>
                    <option value="customer">Customers</option>
                </select>
            </div>
            <button type="button" class="btn-primary" @click="openCreate">Add User</button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="card mb-6 max-w-lg space-y-4 p-6">
            <h3 class="font-display text-lg">{{ editingUser ? 'Edit User' : 'New User' }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input v-model="form.name" type="text" required class="input-field mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input v-model="form.email" type="email" required class="input-field mt-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">{{ editingUser ? 'New Password (optional)' : 'Password' }}</label>
                <input v-model="form.password" type="password" :required="!editingUser" class="input-field mt-1" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input v-model="form.phone" type="text" class="input-field mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium">Address</label>
                    <input v-model="form.address" type="text" class="input-field mt-1" />
                </div>
            </div>
            <label class="flex items-center gap-2">
                <input v-model="form.is_admin" type="checkbox" class="rounded text-brand-copper" />
                <span class="text-sm">Admin access</span>
            </label>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary" :disabled="form.processing">Save</button>
                <button type="button" class="btn-secondary" @click="showForm = false">Cancel</button>
            </div>
        </form>

        <div class="card overflow-hidden">
            <table class="admin-table">
                <thead class="admin-table-head">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Joined</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-beige/30">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-brand-beige/10">
                        <td class="px-6 py-4 font-medium">{{ user.name }}</td>
                        <td class="px-6 py-4">{{ user.email }}</td>
                        <td class="px-6 py-4">
                            <span :class="user.is_admin ? 'bg-brand-copper/20 text-brand-copper' : 'bg-brand-beige/40 text-brand-black/70'" class="rounded-full px-2 py-1 text-xs font-medium uppercase">
                                {{ user.is_admin ? 'Admin' : 'Customer' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4">
                            <button type="button" class="mr-3 text-brand-copper hover:underline" @click="openEdit(user)">Edit</button>
                            <button type="button" class="text-red-600 hover:underline" @click="deleteUser(user)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="users.links" />
    </AdminLayout>
</template>
