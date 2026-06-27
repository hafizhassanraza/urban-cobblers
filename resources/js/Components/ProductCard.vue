<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useQuickView } from '@/composables/useQuickView';
import { discountPercent, imageUrl, onImageError } from '@/utils/image';

const props = defineProps({
    product: Object,
});

const { openQuickView } = useQuickView();

const discount = computed(() => discountPercent(props.product));

const openQuickViewModal = (event) => {
    event.preventDefault();
    event.stopPropagation();
    openQuickView(props.product.slug);
};
</script>

<template>
    <article class="product-card group relative">
        <div class="relative overflow-hidden bg-brand-beige/10">
            <Link :href="route('shop.show', product.slug)" class="block">
                <div class="relative aspect-square overflow-hidden">
                    <img
                        v-if="product.image"
                        :src="imageUrl(product.image)"
                        :alt="product.name"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        loading="lazy"
                        @error="onImageError"
                    />
                    <div v-else class="flex h-full items-center justify-center bg-brand-beige/20">
                        <span class="font-display text-4xl text-brand-copper/30">UC</span>
                    </div>

                    <span v-if="discount" class="product-badge product-badge-sale">Save {{ discount }}%</span>
                    <span v-else-if="product.is_featured" class="product-badge product-badge-new">New In</span>
                </div>
            </Link>

            <div
                v-if="product.stock > 0"
                class="product-card-actions absolute inset-x-0 bottom-0 translate-y-full p-3 transition-transform duration-300 group-hover:translate-y-0"
            >
                <button
                    type="button"
                    class="w-full bg-brand-white py-2.5 text-[11px] font-bold uppercase tracking-wider text-brand-black transition hover:bg-brand-black hover:text-white"
                    @click="openQuickViewModal"
                >
                    Quick View
                </button>
            </div>
        </div>

        <div class="pt-3 text-center sm:pt-4">
            <Link :href="route('shop.show', product.slug)" class="block">
                <h3 class="text-sm font-medium leading-snug text-brand-black transition group-hover:text-brand-copper sm:text-base">
                    {{ product.name }}
                </h3>
                <div class="mt-2 flex items-center justify-center gap-2">
                    <span class="text-sm font-bold text-brand-black sm:text-base">${{ Number(product.price).toFixed(2) }}</span>
                    <span v-if="product.compare_price" class="text-xs text-brand-black/40 line-through sm:text-sm">
                        ${{ Number(product.compare_price).toFixed(2) }}
                    </span>
                </div>
            </Link>

            <p v-if="product.stock <= 0" class="mt-2 text-[10px] font-bold uppercase tracking-wider text-red-500">Sold Out</p>
        </div>
    </article>
</template>
