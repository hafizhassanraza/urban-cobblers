<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { megaMenu, ordersNav } from '@/data/navigation';

const openMenu = defineModel('openMenu', { default: null });

const page = usePage();
const activeOrderCount = computed(() => page.props.activeOrderCount ?? 0);

const shopQuery = () => new URLSearchParams(page.url.split('?')[1] ?? '');

const isShopIndex = () => route().current('shop.index');

const newArrivalsActive = () => isShopIndex() && !shopQuery().get('sale') && !shopQuery().get('ready');

const saleActive = () => isShopIndex() && shopQuery().get('sale') === '1';

const ordersActive = () => route().current('orders.*') || route().current('track-order.*');

const megaMenuActive = (menu) => {
    if (!route().current('categories.show')) {
        return false;
    }

    const slug = route().params.slug;
    return menu.columns.some((col) => col.links.some((link) => link.slug === slug));
};

const categoryHref = (link) => {
    if (link.query) {
        return route('categories.show', link.slug) + '?' + new URLSearchParams(link.query).toString();
    }
    return route('categories.show', link.slug);
};
</script>

<template>
    <nav class="font-nav hidden items-center gap-1 lg:flex">
        <Link
            :href="route('shop.index')"
            :class="[newArrivalsActive() ? 'nav-link-active' : 'nav-link', 'px-3 py-2']"
        >
            New Arrivals
        </Link>

        <div
            v-for="menu in megaMenu"
            :key="menu.label"
            class="relative"
            @mouseenter="openMenu = menu.label"
            @mouseleave="openMenu = null"
        >
            <button
                :class="[
                    openMenu === menu.label || megaMenuActive(menu) ? 'nav-link-active' : 'nav-link',
                    'px-3 py-2',
                ]"
            >
                {{ menu.label }}
            </button>

            <div
                v-show="openMenu === menu.label"
                class="absolute left-0 top-full z-50 w-[640px] border border-brand-beige/40 bg-brand-white p-6 shadow-xl"
            >
                <div class="grid grid-cols-3 gap-6">
                    <div v-for="col in menu.columns" :key="col.title">
                        <p class="mb-3 font-nav text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-copper">{{ col.title }}</p>
                        <div class="space-y-2">
                            <Link
                                v-for="link in col.links"
                                :key="link.label"
                                :href="categoryHref(link)"
                                class="block font-nav text-sm font-medium text-brand-black/70 transition hover:text-brand-copper"
                            >
                                {{ link.label }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Link
            :href="route('shop.index', { sale: 1 })"
            :class="[saleActive() ? 'nav-link-active' : 'nav-link', 'px-3 py-2']"
        >
            Sale
        </Link>

        <Link
            :href="route(ordersNav.route)"
            :class="[ordersActive() ? 'nav-link-active' : 'nav-link', 'relative px-3 py-2']"
        >
            {{ ordersNav.label }}
            <span
                v-if="activeOrderCount > 0"
                class="absolute -right-0.5 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-brand-copper px-1 text-[9px] font-bold text-white"
            >
                {{ activeOrderCount }}
            </span>
        </Link>
    </nav>
</template>
