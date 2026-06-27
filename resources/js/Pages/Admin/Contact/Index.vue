<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    messages: Object,
    filters: Object,
    unreadCount: Number,
});

const search = ref(props.filters.search || '');
const read = ref(props.filters.read || '');

const applyFilters = () => {
    router.get(route('admin.contact.index'), {
        search: search.value || undefined,
        read: read.value || undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
};

const deleteMessage = (message) => {
    if (confirm('Delete this message?')) {
        router.delete(route('admin.contact.destroy', message.id));
    }
};
</script>

<template>
    <Head title="Contact Messages" />

    <AdminLayout>
        <template #title>Contact Inbox <span v-if="unreadCount" class="ml-2 rounded-full bg-brand-copper px-2 py-0.5 text-xs text-white">{{ unreadCount }} unread</span></template>

        <div class="mb-6 flex flex-wrap gap-4">
            <input v-model="search" type="search" placeholder="Search messages..." class="input-field max-w-xs" @input="onSearch" />
            <select v-model="read" class="input-field w-auto" @change="applyFilters">
                <option value="">All Messages</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>

        <div class="card overflow-hidden">
            <table class="admin-table">
                <thead class="admin-table-head">
                    <tr>
                        <th class="px-6 py-3">From</th>
                        <th class="px-6 py-3">Subject</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-beige/30">
                    <tr v-for="message in messages.data" :key="message.id" :class="[!message.is_read ? 'bg-brand-copper/5 font-medium' : '', 'hover:bg-brand-beige/10']">
                        <td class="px-6 py-4">
                            <p>{{ message.name }}</p>
                            <p class="text-xs text-brand-black/50">{{ message.email }}</p>
                        </td>
                        <td class="px-6 py-4">{{ message.subject }}</td>
                        <td class="px-6 py-4">{{ new Date(message.created_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4">
                            <span :class="message.is_read ? 'text-brand-black/50' : 'text-brand-copper'" class="text-xs uppercase">
                                {{ message.is_read ? 'Read' : 'New' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <Link :href="route('admin.contact.show', message.id)" class="mr-3 text-brand-copper hover:underline">View</Link>
                            <button type="button" class="text-red-600 hover:underline" @click="deleteMessage(message)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="messages.links" />
    </AdminLayout>
</template>
