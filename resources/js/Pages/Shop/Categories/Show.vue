<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Pagination from '@/Components/Pagination.vue';
import { imageUrl, onImageError } from '@/utils/image';

const props = defineProps({
    category: Object,
    products: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const sort = ref(props.filters.sort || 'latest');

const applyFilters = () => {
    router.get(route('categories.show', props.category.slug), {
        search: search.value || undefined,
        sort: sort.value || undefined,
    }, { preserveState: true, replace: true });
};

watch(sort, applyFilters);
</script>

<template>
    <Head :title="category.name" />

    <ShopLayout>
        <!-- Category hero -->
        <div class="relative -mx-4 -mt-8 mb-10 overflow-hidden rounded-b-3xl sm:-mx-6 lg:-mx-8">
            <div class="relative h-64 sm:h-80">
                <img
                    v-if="category.image"
                    :src="imageUrl(category.image)"
                    :alt="category.name"
                    class="absolute inset-0 h-full w-full object-cover"
                    @error="onImageError"
                />
                <div v-else class="absolute inset-0 bg-gradient-to-br from-brand-beige to-brand-copper/40" />
                <div class="absolute inset-0 bg-gradient-to-t from-brand-black/80 via-brand-black/40 to-brand-black/20" />
                <div class="relative flex h-full flex-col justify-end px-4 pb-10 sm:px-6 lg:px-8">
                    <nav class="mb-4 text-sm text-white/60">
                        <Link :href="route('home')" class="hover:text-white">Home</Link>
                        <span class="mx-2">/</span>
                        <Link :href="route('categories.index')" class="hover:text-white">Categories</Link>
                        <span class="mx-2">/</span>
                        <span class="text-white">{{ category.name }}</span>
                    </nav>
                    <h1 class="font-display text-4xl text-white sm:text-5xl">{{ category.name }}</h1>
                    <p class="mt-2 max-w-2xl text-brand-beige/80">{{ category.description }}</p>
                    <p class="mt-3 text-sm text-brand-copper">{{ products.total }} products</p>
                </div>
            </div>
        </div>

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <form @submit.prevent="applyFilters" class="flex gap-2">
                <input v-model="search" type="search" placeholder="Search in category..." class="input-field max-w-xs" />
                <button type="submit" class="btn-primary !px-5 !py-2.5">Search</button>
            </form>
            <select v-model="sort" class="input-field w-auto">
                <option value="latest">Newest</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="name">Name A-Z</option>
            </select>
        </div>

        <div v-if="products.data.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
        </div>
        <div v-else class="rounded-2xl border border-brand-beige/40 py-20 text-center">
            <p class="text-brand-black/60">No products in this category yet.</p>
            <Link :href="route('shop.index')" class="mt-4 inline-block text-brand-copper hover:underline">Browse all products</Link>
        </div>

        <Pagination :links="products.links" />
    </ShopLayout>
</template>
