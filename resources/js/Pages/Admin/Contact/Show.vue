<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    message: Object,
});

const deleteMessage = () => {
    if (confirm('Delete this message?')) {
        router.delete(route('admin.contact.destroy', props.message.id));
    }
};
</script>

<template>
    <Head :title="message.subject" />

    <AdminLayout>
        <template #title>{{ message.subject }}</template>

        <Link :href="route('admin.contact.index')" class="text-sm text-brand-copper hover:underline">← Back to Inbox</Link>

        <div class="card mt-6 max-w-2xl p-6">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-brand-beige/40 pb-4">
                <div>
                    <p class="font-semibold text-brand-black">{{ message.name }}</p>
                    <a :href="`mailto:${message.email}`" class="text-sm text-brand-copper hover:underline">{{ message.email }}</a>
                </div>
                <p class="text-sm text-brand-black/50">{{ new Date(message.created_at).toLocaleString() }}</p>
            </div>

            <div class="mt-6 whitespace-pre-wrap text-sm leading-relaxed text-brand-black/80">{{ message.message }}</div>

            <div class="mt-8 flex gap-3">
                <a :href="`mailto:${message.email}?subject=Re: ${encodeURIComponent(message.subject)}`" class="btn-primary">Reply via Email</a>
                <button type="button" class="btn-secondary" @click="deleteMessage">Delete</button>
            </div>
        </div>
    </AdminLayout>
</template>
