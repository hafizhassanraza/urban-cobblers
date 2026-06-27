<script setup>
import ShopLayout from '@/Layouts/ShopLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="My Profile" />

    <ShopLayout>
        <div class="mb-8">
            <h1 class="font-display text-4xl text-brand-black">My Profile</h1>
            <p class="mt-2 text-sm text-brand-black/60">Manage your account details, password, and preferences.</p>
            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                <Link :href="route('orders.index')" class="font-semibold text-brand-copper hover:underline">My Orders →</Link>
            </div>
        </div>

        <div class="mx-auto max-w-2xl space-y-6">
            <div class="card p-6 sm:p-8">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <div class="card p-6 sm:p-8">
                <UpdatePasswordForm />
            </div>

            <div class="card p-6 sm:p-8">
                <DeleteUserForm />
            </div>
        </div>
    </ShopLayout>
</template>
