<script setup>
import { Link } from '@inertiajs/vue3';
import { brandLogos } from '@/data/images';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-brand-beige/30 pt-6 sm:justify-center sm:pt-0">
        <div>
            <Link :href="route('home')">
                <img
                    :src="brandLogos.nav"
                    alt="Urban Cobblers"
                    class="h-12 w-auto"
                />
            </Link>
        </div>

        <div class="mt-6 w-full overflow-hidden border border-brand-beige/40 bg-brand-white px-6 py-6 shadow-sm sm:max-w-md sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
