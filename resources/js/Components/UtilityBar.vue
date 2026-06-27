<script setup>
import { Link } from '@inertiajs/vue3';
import { utilityLinks } from '@/data/navigation';
</script>

<template>
    <div class="border-b border-brand-beige/10 bg-brand-black/90 text-brand-beige/70">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2 px-4 py-2 text-[11px] sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                <Link
                    v-for="link in utilityLinks"
                    :key="link.label"
                    :href="route(link.route)"
                    class="uppercase tracking-wider transition hover:text-brand-copper"
                >
                    {{ link.label }}
                </Link>
            </div>
            <p class="hidden text-[11px] uppercase tracking-wider sm:block">
                Handmade Luxury Leather Shoes
            </p>
        </div>
    </div>
</template>
