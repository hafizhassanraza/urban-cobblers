<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ProductCard from '@/Components/ProductCard.vue';

defineProps({
    products: { type: Array, default: () => [] },
    title: String,
    subtitle: String,
    viewAllHref: String,
    viewAllLabel: { type: String, default: 'View all' },
    dark: { type: Boolean, default: false },
});

const track = ref(null);

const scroll = (direction) => {
    if (!track.value) return;
    const amount = track.value.clientWidth * 0.85;
    track.value.scrollBy({ left: direction * amount, behavior: 'smooth' });
};
</script>

<template>
    <section :class="dark ? 'bg-brand-black py-12' : 'py-12'">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2
                        class="font-display text-2xl sm:text-3xl"
                        :class="dark ? 'text-brand-white' : 'text-brand-black'"
                    >
                        {{ title }}
                    </h2>
                    <p v-if="subtitle" class="mt-1 text-sm" :class="dark ? 'text-brand-white/60' : 'text-brand-black/60'">
                        {{ subtitle }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        v-if="viewAllHref"
                        :href="viewAllHref"
                        class="text-xs font-semibold uppercase tracking-wider text-brand-copper hover:text-brand-black transition"
                        :class="dark && 'hover:text-brand-white'"
                    >
                        {{ viewAllLabel }}
                    </Link>
                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-brand-beige/40 transition hover:border-brand-copper hover:text-brand-copper"
                        :class="dark ? 'border-white/20 text-white hover:text-brand-copper' : 'text-brand-black'"
                        aria-label="Previous"
                        @click="scroll(-1)"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-brand-beige/40 transition hover:border-brand-copper hover:text-brand-copper"
                        :class="dark ? 'border-white/20 text-white hover:text-brand-copper' : 'text-brand-black'"
                        aria-label="Next"
                        @click="scroll(1)"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <div
                ref="track"
                class="carousel-track flex gap-4 overflow-x-auto pb-2 scroll-smooth sm:gap-5"
            >
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="w-[calc(50%-0.5rem)] shrink-0 sm:w-[calc(33.333%-0.85rem)] lg:w-[calc(25%-0.95rem)] xl:w-[calc(20%-1rem)]"
                >
                    <ProductCard :product="product" />
                </div>
            </div>
        </div>
    </section>
</template>
