<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { shoeImages } from '@/data/images';
import { onImageError } from '@/utils/image';

const slides = [
    {
        image: shoeImages.boot,
        eyebrow: 'Formal Collection',
        title: 'Grounded in Craft',
        subtitle: 'Croc-embossed bit loafers and exotic textures — signature Urban Cobblers style.',
        cta: { label: 'Shop Boots', href: 'categories.show', params: 'boots' },
        secondary: { label: 'Our Story', href: 'about', params: null },
    },
    {
        image: shoeImages.hero,
        eyebrow: 'Spring / Summer 2026',
        title: 'Handmade Luxury for Every Step',
        subtitle: 'Peshawari chappals, bit loafers, and office sneakers — crafted with centuries-old techniques.',
        cta: { label: 'Shop Collection', href: 'shop.index', params: {} },
        secondary: { label: 'Ready to Wear', href: 'shop.index', params: { ready: 1 } },
    },
    {
        image: shoeImages.sandal,
        eyebrow: 'Summer Edit',
        title: 'The Art of the Peshawari',
        subtitle: 'Croc, ostrich, and classic leather chappals — handcrafted for warm weather elegance.',
        cta: { label: 'Shop Sandals', href: 'categories.show', params: 'sandals' },
        secondary: { label: 'Size Guide', href: 'size-guide', params: null },
    },
];

const current = ref(0);
const animKey = ref(0);
const prefersReducedMotion = ref(false);
let timer = null;

const activeSlide = computed(() => slides[current.value]);

const slideHref = (link) => {
    if (link.params === null || link.params === undefined) {
        return route(link.href);
    }
    if (typeof link.params === 'object' && !Array.isArray(link.params)) {
        return route(link.href, link.params);
    }
    return route(link.href, link.params);
};

const goTo = (index) => {
    current.value = index;
    animKey.value += 1;
    resetTimer();
};

const next = () => goTo((current.value + 1) % slides.length);

onMounted(() => {
    prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    resetTimer();
});

const resetTimer = () => {
    if (timer) {
        clearInterval(timer);
    }
    if (!prefersReducedMotion.value) {
        timer = setInterval(next, 7000);
    }
};

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
});
</script>

<template>
    <section
        class="hero-banner relative w-full overflow-hidden bg-brand-black"
        aria-label="Featured collections"
    >
        <!-- Background slides -->
        <div class="absolute inset-0">
            <div
                v-for="(slide, index) in slides"
                :key="slide.image"
                class="absolute inset-0 transition-opacity duration-[1400ms] ease-in-out"
                :class="index === current ? 'opacity-100 z-[1]' : 'opacity-0 z-0'"
            >
                <img
                    :src="slide.image"
                    :alt="slide.title"
                    class="h-full w-full object-cover object-center"
                    :class="index === current && !prefersReducedMotion ? 'hero-ken-burns' : 'scale-105'"
                    @error="onImageError"
                />
            </div>
            <div class="pointer-events-none absolute inset-0 z-[2] bg-gradient-to-r from-brand-black/85 via-brand-black/50 to-brand-black/20" />
            <div class="pointer-events-none absolute inset-0 z-[2] bg-gradient-to-t from-brand-black/60 via-transparent to-brand-black/30" />
        </div>

        <!-- Content -->
        <div class="relative z-10 flex min-h-[88vh] items-center sm:min-h-[92vh]">
            <div class="mx-auto w-full max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
                <div :key="animKey" class="max-w-2xl">
                    <p class="hero-animate hero-animate-1 text-xs font-semibold uppercase tracking-[0.35em] text-brand-copper">
                        {{ activeSlide.eyebrow }}
                    </p>
                    <h1 class="hero-animate hero-animate-2 mt-5 font-display text-4xl leading-[1.1] text-brand-white sm:text-5xl lg:text-6xl xl:text-7xl">
                        {{ activeSlide.title }}
                    </h1>
                    <p class="hero-animate hero-animate-3 mt-6 max-w-lg text-base leading-relaxed text-brand-white/75 sm:text-lg">
                        {{ activeSlide.subtitle }}
                    </p>
                    <div class="hero-animate hero-animate-4 mt-10 flex flex-wrap items-center gap-4">
                        <Link :href="slideHref(activeSlide.cta)" class="btn-primary group">
                            <span>{{ activeSlide.cta.label }}</span>
                            <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                        <Link :href="slideHref(activeSlide.secondary)" class="btn-hero-outline">
                            {{ activeSlide.secondary.label }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide controls -->
        <div class="absolute bottom-8 left-0 right-0 z-20 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between gap-6">
                <div class="flex items-center gap-3" role="tablist" aria-label="Hero slides">
                    <button
                        v-for="(_, index) in slides"
                        :key="index"
                        type="button"
                        role="tab"
                        :aria-selected="index === current"
                        :aria-label="`Go to slide ${index + 1}`"
                        class="group relative h-1 overflow-hidden rounded-full bg-white/25 transition-all duration-500"
                        :class="index === current ? 'w-12 bg-white/40' : 'w-6 hover:bg-white/40'"
                        @click="goTo(index)"
                    >
                        <span
                            v-if="index === current && !prefersReducedMotion"
                            class="absolute inset-y-0 left-0 rounded-full bg-brand-copper hero-progress"
                        />
                    </button>
                </div>
                <div class="hidden items-center gap-2 text-xs uppercase tracking-[0.2em] text-white/50 sm:flex">
                    <span class="text-brand-copper">{{ String(current + 1).padStart(2, '0') }}</span>
                    <span>/</span>
                    <span>{{ String(slides.length).padStart(2, '0') }}</span>
                </div>
            </div>
        </div>
    </section>
</template>
