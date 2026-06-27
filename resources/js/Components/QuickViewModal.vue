<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import ProductOptionsSelector from '@/Components/ProductOptionsSelector.vue';
import { useQuickView } from '@/composables/useQuickView';
import { discountPercent, imageUrl, onImageError } from '@/utils/image';
import { productColors, productSizes } from '@/utils/productOptions';

const { isOpen, product, loading, closeQuickView } = useQuickView();

const selectedSize = ref(null);
const selectedColor = ref(null);
const quantity = ref(1);

const form = useForm({
    product_id: null,
    quantity: 1,
});

const discount = computed(() => (product.value ? discountPercent(product.value) : null));
const sizes = computed(() => (product.value ? productSizes(product.value) : []));
const colors = computed(() => (product.value ? productColors(product.value) : []));

const resetForm = () => {
    selectedSize.value = null;
    selectedColor.value = null;
    quantity.value = 1;
    form.reset();
    form.clearErrors();
};

watch(isOpen, (open) => {
    if (open) {
        document.body.style.overflow = 'hidden';
        resetForm();
    } else {
        document.body.style.overflow = '';
    }
});

watch(product, (value) => {
    if (value) {
        form.product_id = value.id;
    }
});

watch(selectedSize, () => form.clearErrors('size'));
watch(selectedColor, () => form.clearErrors('color'));

const addToCart = () => {
    if (!product.value || product.value.stock <= 0) {
        return;
    }

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
    form.quantity = quantity.value;
    form.post(route('cart.store'), {
        preserveScroll: true,
        onSuccess: () => closeQuickView(),
    });
};

const onKeydown = (event) => {
    if (event.key === 'Escape' && isOpen.value) {
        closeQuickView();
    }
};

onMounted(() => window.addEventListener('keydown', onKeydown));
onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition name="quick-view">
            <div
                v-if="isOpen"
                class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center sm:p-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="quick-view-title"
            >
                <div class="absolute inset-0 bg-brand-black/60 backdrop-blur-sm" @click="closeQuickView" />

                <div class="relative z-10 max-h-[92vh] w-full max-w-4xl overflow-y-auto bg-brand-white shadow-2xl sm:rounded-2xl">
                    <button
                        type="button"
                        class="absolute right-4 top-4 z-20 flex h-9 w-9 items-center justify-center rounded-full bg-brand-white/90 text-brand-black shadow transition hover:bg-brand-black hover:text-white"
                        aria-label="Close"
                        @click="closeQuickView"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <div v-if="loading" class="flex min-h-[320px] items-center justify-center p-12">
                        <div class="h-10 w-10 animate-spin rounded-full border-2 border-brand-copper border-t-transparent" />
                    </div>

                    <div v-else-if="product" class="grid md:grid-cols-2">
                        <div class="relative aspect-square bg-brand-beige/20 md:aspect-auto md:min-h-[420px]">
                            <img
                                v-if="product.image"
                                :src="imageUrl(product.image)"
                                :alt="product.name"
                                class="h-full w-full object-cover"
                                @error="onImageError"
                            />
                            <span v-if="discount" class="badge-sale !left-4 !top-4">Save {{ discount }}%</span>
                            <span v-else-if="product.is_featured" class="badge-new !left-4 !top-4">New In</span>
                        </div>

                        <div class="flex flex-col p-6 sm:p-8">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-copper">
                                {{ product.category?.name }}
                            </p>
                            <h2 id="quick-view-title" class="mt-2 font-display text-2xl text-brand-black">
                                {{ product.name }}
                            </h2>
                            <p v-if="product.short_description" class="mt-2 text-sm text-brand-black/60">
                                {{ product.short_description }}
                            </p>

                            <div class="mt-4 flex items-center gap-3">
                                <span class="text-xl font-bold text-brand-black">${{ Number(product.price).toFixed(2) }}</span>
                                <span v-if="product.compare_price" class="text-sm text-brand-black/40 line-through">
                                    ${{ Number(product.compare_price).toFixed(2) }}
                                </span>
                            </div>

                            <ProductOptionsSelector
                                v-model:size="selectedSize"
                                v-model:color="selectedColor"
                                :sizes="sizes"
                                :colors="colors"
                                :size-error="form.errors.size"
                                :color-error="form.errors.color"
                            />

                            <div class="mt-6">
                                <p class="text-xs font-semibold uppercase tracking-wider text-brand-black">Quantity</p>
                                <div class="mt-3 inline-flex items-center rounded-full border border-brand-beige/60">
                                    <button type="button" class="px-4 py-2 text-lg" @click="quantity = Math.max(1, quantity - 1)">−</button>
                                    <span class="w-10 text-center text-sm font-semibold">{{ quantity }}</span>
                                    <button type="button" class="px-4 py-2 text-lg" @click="quantity = Math.min(product.stock, quantity + 1)">+</button>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                <button
                                    type="button"
                                    class="btn-primary flex-1"
                                    :disabled="form.processing || product.stock <= 0"
                                    @click="addToCart"
                                >
                                    {{ form.processing ? 'Adding...' : product.stock > 0 ? 'Add to Cart' : 'Sold Out' }}
                                </button>
                                <Link
                                    :href="route('shop.show', product.slug)"
                                    class="btn-secondary flex-1 text-center"
                                    @click="closeQuickView"
                                >
                                    View Details
                                </Link>
                            </div>

                            <p v-if="product.is_ready_to_wear" class="mt-4 text-xs text-brand-copper">
                                ✓ Ready to Wear — ships within 48 hours
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.quick-view-enter-active,
.quick-view-leave-active {
    transition: opacity 0.25s ease;
}
.quick-view-enter-active > div:last-child,
.quick-view-leave-active > div:last-child {
    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.25s ease;
}
.quick-view-enter-from,
.quick-view-leave-to {
    opacity: 0;
}
.quick-view-enter-from > div:last-child {
    transform: translateY(100%);
    opacity: 0;
}
@media (min-width: 640px) {
    .quick-view-enter-from > div:last-child {
        transform: translateY(16px) scale(0.98);
    }
}
</style>
