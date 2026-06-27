<script setup>
defineProps({
    label: String,
    title: String,
    subtitle: String,
});
</script>

<template>
    <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p v-if="label" class="section-label">{{ label }}</p>
            <h2 class="section-title" :class="label ? 'mt-2' : ''">{{ title }}</h2>
            <p v-if="subtitle" class="mt-2 max-w-xl text-brand-black/60">{{ subtitle }}</p>
        </div>
        <div v-if="$slots.action">
            <slot name="action" />
        </div>
    </div>
</template>
