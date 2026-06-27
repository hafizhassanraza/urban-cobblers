<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import CategoryCard from '@/Components/CategoryCard.vue';
import SectionHeading from '@/Components/SectionHeading.vue';

defineProps({
    categories: Array,
});
</script>

<template>
    <Head title="Categories" />

    <ShopLayout>
        <div class="mb-10">
            <p class="section-label">Browse</p>
            <h1 class="section-title mt-2">Shoe Collections</h1>
            <p class="mt-3 max-w-2xl text-brand-black/60">Men's and women's footwear — dress shoes, boots, loafers, sneakers, bespoke, and care essentials.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <CategoryCard v-for="category in categories" :key="category.id" :category="category" />
        </div>

        <div class="mt-16 rounded-2xl bg-brand-black p-10 text-center lg:p-14">
            <h2 class="font-display text-3xl text-brand-white">Need a custom pair?</h2>
            <p class="mx-auto mt-3 max-w-md text-brand-beige/70">Our bespoke shoe service creates made-to-measure footwear built on your personal last.</p>
            <Link :href="route('contact')" class="btn-primary mt-8 inline-flex">Get in Touch</Link>
        </div>
    </ShopLayout>
</template>
