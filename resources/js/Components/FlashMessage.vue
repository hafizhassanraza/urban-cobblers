<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash);
</script>

<template>
    <div v-if="flash?.success" class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        {{ flash.success }}
    </div>
    <div v-if="flash?.error" class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        {{ flash.error }}
    </div>
</template>
