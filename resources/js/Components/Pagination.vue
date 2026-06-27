<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links.length > 3" class="mt-8 flex flex-wrap justify-center gap-1">
        <template v-for="(link, i) in links" :key="i">
            <span
                v-if="!link.url"
                class="px-3 py-1 text-sm text-brand-black/40"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                class="rounded px-3 py-1 text-sm transition"
                :class="link.active ? 'bg-brand-copper text-white' : 'text-brand-black hover:bg-brand-beige/50'"
                v-html="link.label"
            />
        </template>
    </div>
</template>
