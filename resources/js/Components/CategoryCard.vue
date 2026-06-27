<script setup>
import { Link } from '@inertiajs/vue3';
import { imageUrl, onImageError } from '@/utils/image';

defineProps({
    category: Object,
    size: { type: String, default: 'default' },
});
</script>

<template>
    <Link
        :href="route('categories.show', category.slug)"
        class="group relative overflow-hidden rounded-2xl"
        :class="size === 'large' ? 'aspect-[3/4]' : 'aspect-[4/5]'"
    >
        <img
            v-if="category.image"
            :src="imageUrl(category.image)"
            :alt="category.name"
            class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110"
            loading="lazy"
            @error="onImageError"
        />
        <div v-else class="absolute inset-0 bg-gradient-to-br from-brand-beige to-brand-copper/30" />

        <div class="absolute inset-0 bg-gradient-to-t from-brand-black/80 via-brand-black/20 to-transparent" />

        <div class="absolute inset-x-0 bottom-0 p-6">
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-copper">{{ category.products_count }} items</p>
            <h3 class="mt-1 font-display text-2xl text-white transition group-hover:text-brand-beige">{{ category.name }}</h3>
            <p class="mt-2 line-clamp-2 text-sm text-white/70 opacity-0 transition-opacity duration-300 group-hover:opacity-100">{{ category.description }}</p>
            <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider text-brand-copper opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                Shop Now
                <svg class="h-3 w-3 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        </div>
    </Link>
</template>
