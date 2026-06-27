<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { journalStories } from '@/data/navigation';
import { shoeImages } from '@/data/images';

const articles = [
    ...journalStories,
    {
        title: 'Goodyear Welt Explained',
        excerpt: 'Why the welt matters — and why our shoes are built to be resoled for decades.',
        image: shoeImages.workshop,
        route: 'about',
    },
];
</script>

<template>
    <Head title="The Journal" />

    <ShopLayout>
        <p class="section-label">The Journal</p>
        <h1 class="section-title mt-2">Behind The Craft</h1>
        <p class="mt-3 max-w-2xl text-brand-black/60">Stories from our workshop — the people, techniques, and leather that define Urban Cobblers.</p>

        <div class="mt-12 grid gap-8 md:grid-cols-2">
            <Link
                v-for="article in articles"
                :key="article.title"
                :href="article.route ? route(article.route) : route('shop.show', article.slug)"
                class="group card-hover overflow-hidden md:flex"
            >
                <div class="md:w-2/5 shrink-0">
                    <img :src="article.image" :alt="article.title" class="h-48 w-full object-cover md:h-full" />
                </div>
                <div class="flex flex-col justify-center p-6">
                    <h2 class="font-display text-xl text-brand-black group-hover:text-brand-copper transition">{{ article.title }}</h2>
                    <p class="mt-3 text-sm leading-relaxed text-brand-black/60">{{ article.excerpt }}</p>
                    <span class="mt-4 text-xs font-semibold uppercase tracking-wider text-brand-copper">Read more →</span>
                </div>
            </Link>
        </div>
    </ShopLayout>
</template>
