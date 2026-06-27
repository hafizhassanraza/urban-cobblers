<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import ProductOptionsSelector from '@/Components/ProductOptionsSelector.vue';
import ProductReviews from '@/Components/ProductReviews.vue';
import StarRating from '@/Components/StarRating.vue';
import { imageUrl, discountPercent, onImageError } from '@/utils/image';
import { productColors, productSizes } from '@/utils/productOptions';

const props = defineProps({
    product: Object,
    relatedProducts: Array,
    reviews: { type: Array, default: () => [] },
    reviewStats: { type: Object, default: () => ({ average: 0, count: 0 }) },
    userReview: { type: Object, default: null },
});

const selectedSize = ref(null);
const selectedColor = ref(null);

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
});

const discount = discountPercent(props.product);
const sizes = computed(() => productSizes(props.product));
const colors = computed(() => productColors(props.product));

watch(selectedSize, () => form.clearErrors('size'));
watch(selectedColor, () => form.clearErrors('color'));

const addToCart = () => {
    let hasError = false;

    if (!selectedColor.value) {
        form.setError('color', 'Please select a color.');
        hasError = true;
    }

    if (!selectedSize.value) {
        form.setError('size', 'Please select a size.');
        hasError = true;
    }

    if (hasError) {
        return;
    }

    form.clearErrors();
    form.post(route('cart.store'), { preserveScroll: true });
};

const features = [
    'Hand-lasted and Goodyear welted construction',
    'Full-grain European calfskin uppers',
    'Free resoling for the life of your shoes',
    'US sizes 5–13 (women) and 7–13 (men)',
];
</script>

<template>
    <Head :title="product.name" />

    <ShopLayout>
        <nav class="mb-8 flex flex-wrap items-center gap-2 text-sm text-brand-black/50">
            <Link :href="route('home')" class="hover:text-brand-copper">Home</Link>
            <span>/</span>
            <Link :href="route('shop.index')" class="hover:text-brand-copper">Shop</Link>
            <span>/</span>
            <Link v-if="product.category" :href="route('categories.show', product.category.slug)" class="hover:text-brand-copper">{{ product.category.name }}</Link>
            <span v-if="product.category">/</span>
            <span class="text-brand-black">{{ product.name }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2 lg:gap-16">
            <!-- Gallery -->
            <div class="relative">
                <div class="sticky top-28 overflow-hidden rounded-3xl bg-brand-beige/20">
                    <div class="aspect-square">
                        <img
                            v-if="product.image"
                            :src="imageUrl(product.image)"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                            @error="onImageError"
                        />
                        <div v-else class="flex h-full flex-col items-center justify-center bg-gradient-to-br from-brand-beige/50 to-brand-beige/20">
                            <span class="font-display text-8xl text-brand-copper/30">UC</span>
                        </div>
                    </div>
                    <span v-if="discount" class="badge-sale !left-5 !top-5">-{{ discount }}% OFF</span>
                </div>
            </div>

            <!-- Info -->
            <div>
                <Link v-if="product.category" :href="route('categories.show', product.category.slug)" class="section-label hover:underline">{{ product.category.name }}</Link>
                <h1 class="mt-2 font-display text-4xl text-brand-black lg:text-5xl">{{ product.name }}</h1>
                <p v-if="product.short_description" class="mt-3 text-lg text-brand-black/60">{{ product.short_description }}</p>

                <div class="mt-6 flex items-baseline gap-3">
                    <span class="text-4xl font-bold text-brand-black">${{ Number(product.price).toFixed(2) }}</span>
                    <span v-if="product.compare_price" class="text-xl text-brand-black/40 line-through">${{ Number(product.compare_price).toFixed(2) }}</span>
                </div>

                <div v-if="reviewStats.count" class="mt-3 flex items-center gap-2">
                    <StarRating :rating="reviewStats.average" size="sm" />
                    <span class="text-sm text-brand-black/60">{{ reviewStats.average.toFixed(1) }} ({{ reviewStats.count }} review{{ reviewStats.count === 1 ? '' : 's' }})</span>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <span
                        :class="product.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                        class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wider"
                    >
                        {{ product.stock > 0 ? `${product.stock} in stock` : 'Out of stock' }}
                    </span>
                    <span v-if="product.sku" class="text-xs text-brand-black/40">SKU: {{ product.sku }}</span>
                </div>

                <div v-if="product.stock > 0" class="mt-8">
                    <ProductOptionsSelector
                        v-model:size="selectedSize"
                        v-model:color="selectedColor"
                        :sizes="sizes"
                        :colors="colors"
                        :size-error="form.errors.size"
                        :color-error="form.errors.color"
                    />

                    <form @submit.prevent="addToCart" class="mt-8 flex flex-wrap items-center gap-4">
                        <div class="flex items-center rounded-full border border-brand-beige/60">
                            <button type="button" @click="form.quantity = Math.max(1, form.quantity - 1)" class="px-4 py-3 text-lg hover:text-brand-copper">−</button>
                            <span class="w-10 text-center font-semibold">{{ form.quantity }}</span>
                            <button type="button" @click="form.quantity = Math.min(product.stock, form.quantity + 1)" class="px-4 py-3 text-lg hover:text-brand-copper">+</button>
                        </div>
                        <button type="submit" class="btn-primary flex-1 sm:flex-none" :disabled="form.processing">
                            {{ form.processing ? 'Adding...' : 'Add to Cart' }}
                        </button>
                    </form>
                </div>

                <ul class="mt-8 space-y-2.5 border-t border-brand-beige/40 pt-8">
                    <li v-for="f in features" :key="f" class="flex items-center gap-3 text-sm text-brand-black/70">
                        <svg class="h-4 w-4 shrink-0 text-brand-copper" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        {{ f }}
                    </li>
                </ul>

                <div class="mt-10 border-t border-brand-beige/40 pt-8">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-brand-black">Description</h2>
                    <p class="mt-4 text-sm leading-relaxed text-brand-black/70">{{ product.description }}</p>
                </div>

                <ProductReviews
                    :product="product"
                    :reviews="reviews"
                    :review-stats="reviewStats"
                    :user-review="userReview"
                />
            </div>
        </div>

        <section v-if="relatedProducts.length" class="mt-20 border-t border-brand-beige/40 pt-16">
            <h2 class="section-title">You May Also Like</h2>
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="p in relatedProducts" :key="p.id" :product="p" />
            </div>
        </section>
    </ShopLayout>
</template>
