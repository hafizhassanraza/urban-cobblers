<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AnnouncementMarquee from '@/Components/AnnouncementMarquee.vue';
import MegaMenu from '@/Components/MegaMenu.vue';
import QuickViewModal from '@/Components/QuickViewModal.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { provideQuickView } from '@/composables/useQuickView';
import { popularSearches, ordersNav } from '@/data/navigation';
import { brandLogos } from '@/data/images';

provideQuickView();

const page = usePage();
const user = computed(() => page.props.auth.user);
const cartCount = computed(() => page.props.cartCount ?? 0);
const activeOrderCount = computed(() => page.props.activeOrderCount ?? 0);
const developer = computed(() => page.props.developer);
const isAdmin = computed(() => user.value?.is_admin);

const navOpen = ref(false);
const openMenu = ref(null);
const searchOpen = ref(false);
const searchQuery = ref('');

const submitSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('shop.index'), { search: searchQuery.value.trim() });
        searchOpen.value = false;
        navOpen.value = false;
    }
};

const searchPopular = (term) => {
    router.get(route('shop.index'), { search: term });
    searchOpen.value = false;
};

const newsletterEmail = ref('');
const joinNewsletter = () => {
    if (newsletterEmail.value) {
        router.get(route('contact'), { email: newsletterEmail.value });
    }
};

const shopQuery = () => new URLSearchParams(page.url.split('?')[1] ?? '');

const mobileLinkClass = (active) => (active ? 'block text-brand-copper font-semibold' : 'block');
</script>

<template>
    <div class="flex min-h-screen flex-col bg-brand-white font-body">
        <AnnouncementMarquee />

        <header class="sticky top-0 z-50 border-b border-brand-beige/40 bg-brand-white font-nav shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <Link :href="route('home')" class="shrink-0">
                    <img
                        :src="brandLogos.nav"
                        alt="Urban Cobblers"
                        class="h-9 w-auto sm:h-11"
                    />
                </Link>

                <MegaMenu v-model:open-menu="openMenu" />

                <div class="flex items-center gap-1 sm:gap-2">
                    <button
                        @click="searchOpen = !searchOpen"
                        class="rounded-full p-2 text-brand-black transition hover:bg-brand-beige/30"
                        aria-label="Search"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <Link :href="route('cart.index')" class="relative rounded-full p-2 text-brand-black transition hover:bg-brand-beige/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span v-if="cartCount > 0" class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-brand-copper text-[9px] font-bold text-white">{{ cartCount }}</span>
                    </Link>
                    <template v-if="user">
                        <Link v-if="isAdmin" :href="route('admin.dashboard')" class="hidden text-sm font-medium tracking-wide text-brand-copper transition hover:text-brand-black sm:block">Admin</Link>
                        <Link :href="route('profile.edit')" class="hidden rounded-full p-2 hover:bg-brand-beige/30 sm:block">
                            <svg class="h-5 w-5 text-brand-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="hidden text-sm font-medium tracking-wide text-brand-black transition hover:text-brand-copper sm:block">Login</Link>
                    </template>
                    <button class="rounded-full p-2 hover:bg-brand-beige/30 lg:hidden" @click="navOpen = !navOpen">
                        <svg class="h-6 w-6 text-brand-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <div v-if="searchOpen" class="border-t border-brand-beige/30 bg-brand-white px-4 py-4">
                <form @submit.prevent="submitSearch" class="mx-auto max-w-2xl">
                    <div class="flex gap-2">
                        <input v-model="searchQuery" type="search" placeholder="Search shoes..." class="input-field flex-1" autofocus />
                        <button type="submit" class="btn-primary !py-2.5 !px-6">Search</button>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="text-xs text-brand-black/50">Popular:</span>
                        <button
                            v-for="term in popularSearches"
                            :key="term"
                            type="button"
                            @click="searchPopular(term)"
                            class="rounded-full border border-brand-beige/60 px-3 py-1 text-xs text-brand-black transition hover:border-brand-copper hover:text-brand-copper"
                        >
                            {{ term }}
                        </button>
                    </div>
                </form>
            </div>

            <div v-if="navOpen" class="max-h-[70vh] overflow-y-auto border-t border-brand-beige/30 px-4 py-4 font-nav lg:hidden">
                <div class="space-y-3 text-sm font-medium tracking-wide">
                    <Link
                        :href="route('shop.index')"
                        :class="mobileLinkClass(route().current('shop.index') && !shopQuery().get('sale') && !shopQuery().get('ready'))"
                        @click="navOpen = false"
                    >
                        New Arrivals
                    </Link>
                    <Link :href="route('categories.show', 'loafers')" class="block" @click="navOpen = false">Men</Link>
                    <Link :href="route('categories.show', 'sandals')" class="block" @click="navOpen = false">Sandals</Link>
                    <Link
                        :href="route('shop.index', { sale: 1 })"
                        :class="mobileLinkClass(route().current('shop.index') && shopQuery().get('sale') === '1')"
                        @click="navOpen = false"
                    >
                        Sale
                    </Link>
                    <Link
                        :href="route(ordersNav.route)"
                        :class="mobileLinkClass(route().current('orders.*') || route().current('track-order.*'))"
                        @click="navOpen = false"
                    >
                        {{ ordersNav.label }}
                        <span v-if="activeOrderCount > 0" class="ml-1 text-brand-copper">({{ activeOrderCount }})</span>
                    </Link>
                    <Link :href="route('shop.index', { ready: 1 })" class="block" @click="navOpen = false">Ready to Wear</Link>
                    <Link :href="route('size-guide')" class="block" @click="navOpen = false">Size Guide</Link>
                    <Link :href="route('contact')" class="block" @click="navOpen = false">Contact</Link>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot name="full">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <FlashMessage />
                    <slot />
                </div>
            </slot>
        </main>

        <footer class="border-t border-brand-beige/30 bg-brand-black text-brand-beige">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <Link :href="route('home')">
                            <img
                                :src="brandLogos.footer"
                                alt="Urban Cobblers"
                                class="h-28 w-auto"
                            />
                        </Link>
                        <p class="mt-4 text-sm leading-relaxed text-brand-beige/70">Handmade luxury leather shoes for men and women. Crafted in our Brooklyn workshop.</p>
                        <p class="mt-4 text-sm">
                            <span class="text-brand-beige/50">Call:</span> +1 (555) 123-4567<br>
                            <span class="text-brand-beige/50">Email:</span> hello@urbancobblers.com
                        </p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-widest text-brand-white">Quick Links</h4>
                        <div class="mt-4 flex flex-col gap-2.5 text-sm">
                            <Link :href="route('shop.index')" class="hover:text-brand-copper transition">Shop All</Link>
                            <Link :href="route('shop.index', { sale: 1 })" class="hover:text-brand-copper transition">Sale Collection</Link>
                            <Link :href="route('orders.index')" class="hover:text-brand-copper transition">My Orders</Link>
                            <Link :href="route('track-order.index')" class="hover:text-brand-copper transition">Track Order</Link>
                            <Link :href="route('size-guide')" class="hover:text-brand-copper transition">Size Guide</Link>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-widest text-brand-white">Customer Service</h4>
                        <div class="mt-4 flex flex-col gap-2.5 text-sm">
                            <Link :href="route('contact')" class="hover:text-brand-copper transition">Contact Us</Link>
                            <Link :href="route('about')" class="hover:text-brand-copper transition">About Us</Link>
                            <Link :href="route('journal')" class="hover:text-brand-copper transition">The Journal</Link>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-widest text-brand-white">Newsletter</h4>
                        <p class="mt-4 text-sm text-brand-beige/70">Receive exclusive promotions and new arrivals.</p>
                        <form @submit.prevent="joinNewsletter" class="mt-4 flex gap-2">
                            <input v-model="newsletterEmail" type="email" required placeholder="Email address" class="input-field flex-1 !bg-white/5 !border-white/10 !text-white placeholder:!text-white/40 !py-2.5" />
                            <button type="submit" class="btn-primary !px-4 !py-2.5 text-xs">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class="mt-12 flex flex-wrap items-center justify-center gap-x-3 gap-y-1 border-t border-brand-beige/15 pt-8 text-center text-sm text-brand-beige/50">
                    <p>&copy; {{ new Date().getFullYear() }} Urban Cobblers. All rights reserved.</p>
                    <span v-if="developer?.url" class="hidden text-brand-beige/30 sm:inline" aria-hidden="true">|</span>
                    <span v-if="developer?.url">
                        <a
                            :href="developer.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="transition hover:text-brand-copper"
                        >
                            Developed By {{ developer.name }}
                        </a>
                        <span v-if="developer.phone"> ({{ developer.phone }})</span>
                    </span>
                </div>
            </div>
        </footer>

        <QuickViewModal />
    </div>
</template>
