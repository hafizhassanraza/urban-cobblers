<script setup>
const model = defineModel({ type: String, default: 'email' });
</script>

<template>
    <div class="inline-flex rounded-full border border-brand-beige/60 p-1">
        <button
            type="button"
            class="rounded-full px-4 py-1.5 text-xs font-medium transition"
            :class="model === 'email' ? 'bg-brand-black text-white' : 'text-brand-black/60 hover:text-brand-black'"
            @click="model = 'email'"
        >
            Email
        </button>
        <button
            type="button"
            class="rounded-full px-4 py-1.5 text-xs font-medium transition"
            :class="model === 'phone' ? 'bg-brand-black text-white' : 'text-brand-black/60 hover:text-brand-black'"
            @click="model = 'phone'"
        >
            Phone
        </button>
    </div>
</template>
