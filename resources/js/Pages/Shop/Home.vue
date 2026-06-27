<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCarousel from '@/Components/ProductCarousel.vue';
import CategoryCard from '@/Components/CategoryCard.vue';
import SectionHeading from '@/Components/SectionHeading.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import HeroBanner from '@/Components/HeroBanner.vue';
import TrustBadges from '@/Components/TrustBadges.vue';
import { computed } from 'vue';
import { shoeImages } from '@/data/images';

const props = defineProps({
    newArrivals: Array,
    categories: Array,
    readyToWear: Array,
    saleProducts: Array,
});

const featuredCategories = computed(() => (props.categories ?? []).slice(0, 6));

const categoryRowClass = (index) => {
    if (index < 2) return '';
    if (index < 4) return 'hidden sm:block';
    return 'hidden lg:block';
};

const testimonials = [
    { quote: 'Very comfortable and perfect for everyday wear. Great quality.', author: 'James R.' },
    { quote: 'Perfect mix of casual and formal. My go-to oxfords.', author: 'Michael T.' },
    { quote: 'I wear them every day to work. Still feel like new.', author: 'Sarah M.' },
    { quote: 'Didn\'t expect this level of comfort. Really impressed.', author: 'David L.' },
];
</script>

<template>
    <Head title="Handmade Luxury Leather Shoes" />

    <ShopLayout>
        <template #full>
            <div class="relative">
                <div class="pointer-events-none absolute inset-x-0 top-4 z-30 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="pointer-events-auto">
                        <FlashMessage />
                    </div>
                </div>
                <HeroBanner />
            </div>

            <ProductCarousel
                v-if="saleProducts?.length"
                title="Special Sale"
                subtitle="Flat discounts on selected styles"
                :products="saleProducts"
                :view-all-href="route('shop.index', { sale: 1 })"
            />

            <section v-if="categories?.length" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <SectionHeading title="Shop by Category" subtitle="Browse our full range of handmade footwear and care essentials">
                    <template #action>
                        <Link :href="route('categories.index')" class="text-sm font-semibold uppercase tracking-wider text-brand-copper hover:underline">View All</Link>
                    </template>
                </SectionHeading>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <CategoryCard
                        v-for="(category, index) in featuredCategories"
                        :key="category.id"
                        :category="category"
                        :class="categoryRowClass(index)"
                    />
                </div>
            </section>

            <section class="bg-brand-beige/20 py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-brand-copper">Urban Cobblers Lives</p>
                            <h2 class="mt-3 font-display text-3xl text-brand-black sm:text-4xl">Polished. Poised. Perfectly Crafted.</h2>
                            <p class="mt-4 text-brand-black/60">The kind of comfort that goes with everything and everywhere — handmade in Brooklyn with full-grain leather and Goodyear welt construction.</p>
                            <Link :href="route('about')" class="btn-primary mt-8 inline-flex">Our Story</Link>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <img :src="shoeImages.oxford" alt="Oxford" class="aspect-square object-cover" />
                            <img :src="shoeImages.boot" alt="Boot" class="aspect-square object-cover mt-6" />
                        </div>
                    </div>
                </div>
            </section>

            <ProductCarousel
                title="New Arrivals"
                subtitle="Discover what just landed"
                :products="newArrivals"
                :view-all-href="route('shop.index')"
            />

            <section class="py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 class="text-center font-display text-2xl text-brand-black sm:text-3xl">Happy Customers</h2>
                    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <blockquote v-for="item in testimonials" :key="item.author" class="border border-brand-beige/40 bg-brand-white p-6">
                            <p class="text-sm leading-relaxed text-brand-black/70">"{{ item.quote }}"</p>
                            <footer class="mt-4 text-xs font-semibold uppercase tracking-wider text-brand-copper">{{ item.author }}</footer>
                        </blockquote>
                    </div>
                </div>
            </section>

            <TrustBadges />

            <section class="border-t border-brand-beige/30 bg-brand-black py-16">
                <div class="mx-auto max-w-xl px-4 text-center sm:px-6">
                    <h2 class="font-display text-2xl text-brand-white">Keep Me Updated</h2>
                    <p class="mt-2 text-sm text-brand-beige/60">Receive exclusive promotions, private sales and news.</p>
                    <Link :href="route('contact')" class="btn-primary mt-6 inline-flex">Subscribe</Link>
                </div>
            </section>
        </template>
    </ShopLayout>
</template>
