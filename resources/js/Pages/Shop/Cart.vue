<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { imageUrl, onImageError } from '@/utils/image';

defineProps({
    items: Array,
    subtotal: Number,
});

const updateQuantity = (item, delta) => {
    const newQty = item.quantity + delta;
    router.patch(route('cart.update', item.id), { quantity: Math.max(0, newQty) }, { preserveScroll: true });
};

const removeItem = (item) => {
    router.delete(route('cart.destroy', item.id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Cart" />

    <ShopLayout>
        <p class="section-label">Your Bag</p>
        <h1 class="section-title mt-2">Shopping Cart</h1>

        <div v-if="items.length" class="mt-10 grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4">
                <div v-for="item in items" :key="item.id" class="card-hover flex gap-5 p-5">
                    <Link :href="route('shop.show', item.product.slug)" class="h-28 w-28 shrink-0 overflow-hidden rounded-xl bg-brand-beige/20">
                        <img v-if="item.product.image" :src="imageUrl(item.product.image)" class="h-full w-full object-cover" @error="onImageError" />
                        <div v-else class="flex h-full items-center justify-center font-display text-brand-copper/40">UC</div>
                    </Link>
                    <div class="flex flex-1 flex-col justify-between sm:flex-row sm:items-center">
                        <div>
                            <Link :href="route('shop.show', item.product.slug)" class="font-display text-lg hover:text-brand-copper transition">{{ item.product.name }}</Link>
                            <p class="mt-1 text-xs uppercase tracking-wider text-brand-copper">{{ item.product.category?.name }}</p>
                            <p class="mt-2 font-semibold">${{ Number(item.product.price).toFixed(2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-5 sm:mt-0">
                            <div class="flex items-center rounded-full border border-brand-beige/60">
                                <button @click="updateQuantity(item, -1)" class="px-3 py-1.5 hover:text-brand-copper">−</button>
                                <span class="w-8 text-center text-sm font-semibold">{{ item.quantity }}</span>
                                <button @click="updateQuantity(item, 1)" class="px-3 py-1.5 hover:text-brand-copper">+</button>
                            </div>
                            <span class="w-20 text-right font-bold">${{ (item.product.price * item.quantity).toFixed(2) }}</span>
                            <button @click="removeItem(item)" class="text-brand-black/30 hover:text-red-500 transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card h-fit p-6 lg:sticky lg:top-28">
                <h2 class="font-display text-xl text-brand-black">Order Summary</h2>
                <div class="mt-6 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-brand-black/60">Subtotal ({{ items.reduce((s, i) => s + i.quantity, 0) }} items)</span>
                        <span class="font-semibold">${{ Number(subtotal).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-brand-black/60">Shipping</span>
                        <span class="text-green-600 font-medium">{{ subtotal >= 200 ? 'FREE' : 'From $15' }}</span>
                    </div>
                    <div v-if="subtotal < 200" class="rounded-lg bg-brand-beige/30 px-3 py-2 text-xs text-brand-black/60">
                        Add ${{ (200 - subtotal).toFixed(2) }} more for free shipping
                    </div>
                </div>
                <div class="mt-6 border-t border-brand-beige/40 pt-4 flex justify-between text-xl font-bold">
                    <span>Total</span>
                    <span>${{ Number(subtotal).toFixed(2) }}</span>
                </div>
                <Link :href="route('checkout.index')" class="btn-primary mt-6 block w-full text-center">Checkout</Link>
                <Link :href="route('shop.index')" class="mt-4 block text-center text-sm text-brand-copper hover:underline">← Continue Shopping</Link>
            </div>
        </div>

        <div v-else class="mt-16 text-center">
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-brand-beige/30">
                <svg class="h-10 w-10 text-brand-copper/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <p class="mt-6 text-lg text-brand-black/60">Your cart is empty</p>
            <Link :href="route('shop.index')" class="btn-primary mt-6 inline-flex">Start Shopping</Link>
        </div>
    </ShopLayout>
</template>
