<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    active: Boolean,
});
</script>

<template>
    <Link
        :href="href"
        :class="[
            active
                ? 'text-brand-copper border-b-2 border-brand-copper'
                : 'text-brand-black hover:text-brand-copper',
            'px-1 py-2 text-sm font-medium uppercase tracking-wider transition',
        ]"
    >
        <slot />
    </Link>
</template>
