<script setup>
import { computed } from 'vue';

const props = defineProps({
    rating: { type: Number, default: 0 },
    size: { type: String, default: 'md' },
    interactive: { type: Boolean, default: false },
});

const emit = defineEmits(['update:rating']);

const sizeClass = computed(() => ({
    sm: 'h-3.5 w-3.5',
    md: 'h-5 w-5',
    lg: 'h-6 w-6',
}[props.size] || 'h-5 w-5'));

const setRating = (value) => {
    if (props.interactive) {
        emit('update:rating', value);
    }
};
</script>

<template>
    <div class="inline-flex items-center gap-0.5" :class="interactive ? 'cursor-pointer' : ''">
        <button
            v-for="star in 5"
            :key="star"
            type="button"
            class="transition"
            :class="[sizeClass, interactive ? 'hover:scale-110' : 'cursor-default']"
            :disabled="!interactive"
            :aria-label="`${star} star${star > 1 ? 's' : ''}`"
            @click="setRating(star)"
        >
            <svg
                viewBox="0 0 20 20"
                :class="star <= Math.round(rating) ? 'text-brand-copper' : 'text-brand-beige'"
                fill="currentColor"
            >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        </button>
    </div>
</template>
