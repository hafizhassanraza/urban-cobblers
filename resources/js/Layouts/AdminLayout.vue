<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const navSections = [
    {
        title: 'Overview',
        items: [
            { name: 'Dashboard', route: 'admin.dashboard' },
        ],
    },
    {
        title: 'Catalog',
        items: [
            { name: 'Products', route: 'admin.products.index' },
            { name: 'Categories', route: 'admin.categories.index' },
            { name: 'Reviews', route: 'admin.reviews.index' },
        ],
    },
    {
        title: 'Sales',
        items: [
            { name: 'Orders', route: 'admin.orders.index' },
            { name: 'Customers', route: 'admin.customers.index' },
            { name: 'Reports', route: 'admin.reports.index' },
        ],
    },
    {
        title: 'Communication',
        items: [
            { name: 'Contact Inbox', route: 'admin.contact.index' },
        ],
    },
    {
        title: 'System',
        items: [
            { name: 'Users', route: 'admin.users.index' },
            { name: 'Settings', route: 'admin.settings.index' },
        ],
    },
];

const isActive = (routeName) => {
    return route().current(routeName) || route().current(routeName.replace('.index', '.*'));
};

const sidebarOpen = defineModel('sidebarOpen', { default: false });
</script>

<template>
    <div class="min-h-screen bg-brand-beige/20">
        <div class="flex">
            <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-brand-beige/40 bg-brand-black text-brand-white lg:flex">
                <div class="border-b border-brand-beige/20 px-6 py-6">
                    <Link :href="route('admin.dashboard')" class="font-display text-xl">
                        Admin <span class="text-brand-copper">Panel</span>
                    </Link>
                    <p class="mt-1 text-xs text-brand-beige/60">Urban Cobblers</p>
                </div>
                <nav class="flex-1 overflow-y-auto px-4 py-4">
                    <div v-for="section in navSections" :key="section.title" class="mb-6">
                        <p class="mb-2 px-4 text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-beige/40">{{ section.title }}</p>
                        <div class="space-y-1">
                            <Link
                                v-for="item in section.items"
                                :key="item.route"
                                :href="route(item.route)"
                                :class="[
                                    isActive(item.route)
                                        ? 'bg-brand-copper text-white'
                                        : 'text-brand-beige hover:bg-brand-white/10',
                                    'block rounded-md px-4 py-2 text-sm font-medium transition',
                                ]"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>
                </nav>
                <div class="border-t border-brand-beige/20 px-4 py-4">
                    <Link :href="route('home')" class="block text-sm text-brand-beige transition hover:text-brand-copper">← Back to Store</Link>
                    <p class="mt-2 text-xs text-brand-beige/50">{{ user?.name }}</p>
                </div>
            </aside>

            <div class="flex flex-1 flex-col lg:pl-64">
                <header class="sticky top-0 z-20 flex items-center justify-between border-b border-brand-beige/40 bg-brand-white px-4 py-4 lg:px-8">
                    <button class="lg:hidden" @click="sidebarOpen = !sidebarOpen">
                        <svg class="h-6 w-6 text-brand-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="font-display text-xl text-brand-black"><slot name="title" /></h1>
                    <Link :href="route('logout')" method="post" as="button" class="text-sm uppercase tracking-wider text-brand-copper transition hover:text-brand-black">Logout</Link>
                </header>

                <div v-if="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
                    <div class="absolute inset-0 bg-brand-black/50" />
                    <aside class="relative h-full w-64 overflow-y-auto bg-brand-black p-4 text-brand-white" @click.stop>
                        <nav class="mt-8 space-y-6">
                            <div v-for="section in navSections" :key="section.title">
                                <p class="mb-2 px-2 text-[10px] uppercase tracking-wider text-brand-beige/40">{{ section.title }}</p>
                                <Link
                                    v-for="item in section.items"
                                    :key="item.route"
                                    :href="route(item.route)"
                                    class="block rounded-md px-4 py-2 text-sm hover:bg-brand-copper"
                                    @click="sidebarOpen = false"
                                >
                                    {{ item.name }}
                                </Link>
                            </div>
                        </nav>
                    </aside>
                </div>

                <main class="flex-1 p-4 lg:p-8">
                    <FlashMessage />
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
