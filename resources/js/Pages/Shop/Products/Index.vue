<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Pagination from '@/Components/Pagination.vue';
import { imageUrl } from '@/utils/image';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    pageTitle: { type: String, default: 'Shop All Shoes' },
});

const search = ref(props.filters.search || '');
const sort = ref(props.filters.sort || 'latest');
const category = ref(props.filters.category || '');

const applyFilters = () => {
    router.get(route('shop.index'), {
        search: search.value || undefined,
        sort: sort.value || undefined,
        category: category.value || undefined,
    }, { preserveState: true, replace: true });
};

watch([sort, category], applyFilters);

const activeCategory = () => props.categories.find(c => c.slug === category.value);
</script>

<template>
    <Head title="Shop" />

    <ShopLayout>
        <div class="mb-10">
            <p class="section-label">Footwear</p>
            <h1 class="section-title mt-2">{{ pageTitle }}</h1>
            <p class="mt-2 text-brand-black/60">{{ products.total }} handcrafted styles</p>
        </div>

        <div class="flex flex-col gap-8 xl:flex-row">
            <!-- Sidebar -->
            <aside class="xl:w-72 shrink-0 space-y-6">
                <div class="card p-5">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-brand-black">Categories</h3>
                    <div class="mt-4 space-y-1">
                        <button
                            @click="category = ''; applyFilters()"
                            :class="[!category ? 'bg-brand-copper/10 text-brand-copper font-semibold' : 'text-brand-black/70 hover:bg-brand-beige/30', 'block w-full rounded-lg px-3 py-2.5 text-left text-sm transition']"
                        >
                            All Products
                        </button>
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            @click="category = cat.slug; applyFilters()"
                            :class="[category === cat.slug ? 'bg-brand-copper/10 text-brand-copper font-semibold' : 'text-brand-black/70 hover:bg-brand-beige/30', 'block w-full rounded-lg px-3 py-2.5 text-left text-sm transition']"
                        >
                            {{ cat.name }}
                        </button>
                    </div>
                </div>

                <Link :href="route('categories.index')" class="card-hover block p-5 text-center text-sm font-semibold text-brand-copper">
                    View All Categories →
                </Link>
            </aside>

            <!-- Main -->
            <div class="flex-1 min-w-0">
                <div v-if="activeCategory()" class="mb-6 flex items-center gap-4 rounded-2xl bg-brand-beige/20 p-4">
                    <img v-if="activeCategory().image" :src="imageUrl(activeCategory().image)" class="h-16 w-16 rounded-xl object-cover" />
                    <div>
                        <p class="font-display text-lg text-brand-black">{{ activeCategory().name }}</p>
                        <p class="text-sm text-brand-black/60 line-clamp-1">{{ activeCategory().description }}</p>
                    </div>
                    <button @click="category = ''; applyFilters()" class="ml-auto text-xs text-brand-copper hover:underline">Clear</button>
                </div>

                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <form @submit.prevent="applyFilters" class="flex flex-1 gap-2 max-w-md">
                        <input v-model="search" type="search" placeholder="Search products..." class="input-field" />
                        <button type="submit" class="btn-primary !px-5 shrink-0">Search</button>
                    </form>
                    <select v-model="sort" class="input-field w-full sm:w-auto">
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
                    <p class="text-brand-black/60">No products found.</p>
                    <button @click="category = ''; search = ''; applyFilters()" class="mt-4 text-brand-copper hover:underline">Clear all filters</button>
                </div>

                <Pagination :links="products.links" />
            </div>
        </div>
    </ShopLayout>
</template>
