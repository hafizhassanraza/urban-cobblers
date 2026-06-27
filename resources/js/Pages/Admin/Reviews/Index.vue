<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import StarRating from '@/Components/StarRating.vue';

const props = defineProps({
    reviews: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
    router.get(route('admin.reviews.index'), {
        search: search.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
};

let searchTimeout;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
};

const toggleApproval = (review) => {
    router.patch(route('admin.reviews.update-approval', review.id), {}, { preserveScroll: true });
};

const deleteReview = (review) => {
    if (! confirm('Delete this review permanently?')) {
        return;
    }

    router.delete(route('admin.reviews.destroy', review.id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Reviews" />

    <AdminLayout>
        <template #title>Client Reviews</template>

        <div class="mb-6 flex flex-wrap gap-4">
            <input v-model="search" type="search" placeholder="Search reviews..." class="input-field max-w-xs" @input="onSearch" />
            <select v-model="status" class="input-field w-auto" @change="applyFilters">
                <option value="">All Reviews</option>
                <option value="approved">Approved</option>
                <option value="hidden">Hidden</option>
            </select>
        </div>

        <div class="card overflow-hidden">
            <table class="admin-table">
                <thead class="admin-table-head">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Rating</th>
                        <th class="px-6 py-3">Review</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-beige/30">
                    <tr v-for="review in reviews.data" :key="review.id" class="hover:bg-brand-beige/10">
                        <td class="px-6 py-4">
                            <Link :href="route('shop.show', review.product.slug)" class="font-medium text-brand-copper hover:underline">
                                {{ review.product.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ review.user.name }}</p>
                            <p class="text-xs text-brand-black/50">{{ review.user.email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <StarRating :rating="review.rating" size="sm" />
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <p v-if="review.title" class="font-medium">{{ review.title }}</p>
                            <p class="line-clamp-2 text-brand-black/70">{{ review.body }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                :class="review.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                class="rounded-full px-2 py-1 text-xs font-medium uppercase"
                            >
                                {{ review.is_approved ? 'Live' : 'Hidden' }}
                            </span>
                            <span v-if="review.is_verified_purchase" class="ml-1 rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">Verified</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <button type="button" class="text-left text-brand-copper hover:underline" @click="toggleApproval(review)">
                                    {{ review.is_approved ? 'Hide' : 'Approve' }}
                                </button>
                                <button type="button" class="text-left text-red-600 hover:underline" @click="deleteReview(review)">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination v-if="reviews.links" :links="reviews.links" class="mt-6" />
    </AdminLayout>
</template>
