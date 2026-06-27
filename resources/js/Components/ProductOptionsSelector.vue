<script setup>
defineProps({
    sizes: { type: Array, required: true },
    colors: { type: Array, required: true },
    sizeError: { type: String, default: '' },
    colorError: { type: String, default: '' },
});

const selectedSize = defineModel('size', { type: Number, default: null });
const selectedColor = defineModel('color', { type: Object, default: null });
</script>

<template>
    <div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-black">
                Color
                <span v-if="selectedColor" class="normal-case text-brand-copper">— {{ selectedColor.name }}</span>
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
                <button
                    v-for="color in colors"
                    :key="color.name"
                    type="button"
                    class="h-9 w-9 rounded-full border-2 transition hover:scale-105"
                    :class="selectedColor?.name === color.name ? 'border-brand-black ring-2 ring-brand-copper ring-offset-2' : 'border-brand-black/10'"
                    :style="{ backgroundColor: color.hex }"
                    :title="color.name"
                    :aria-label="color.name"
                    @click="selectedColor = color"
                />
            </div>
            <p v-if="colorError" class="mt-2 text-xs text-red-600">{{ colorError }}</p>
        </div>

        <div class="mt-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-black">Select Size</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <button
                    v-for="row in sizes"
                    :key="row.us"
                    type="button"
                    class="min-w-[4.5rem] rounded border px-3 py-2 text-center transition"
                    :class="selectedSize === row.us
                        ? 'border-brand-black bg-brand-black text-white'
                        : 'border-brand-beige/60 text-brand-black hover:border-brand-copper'"
                    @click="selectedSize = row.us"
                >
                    <span class="block text-sm font-semibold leading-tight">US {{ row.us }}</span>
                    <span
                        class="block text-[10px] leading-tight"
                        :class="selectedSize === row.us ? 'text-white/75' : 'text-brand-black/50'"
                    >
                        UK {{ row.uk }}
                    </span>
                </button>
            </div>
            <p v-if="sizeError" class="mt-2 text-xs text-red-600">{{ sizeError }}</p>
        </div>
    </div>
</template>
