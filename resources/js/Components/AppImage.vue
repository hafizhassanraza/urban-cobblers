<script setup>
import { imageUrl, onImageError } from '@/utils/image';

defineProps({
    src: { type: String, default: null },
    alt: { type: String, default: '' },
    imgClass: { type: String, default: '' },
});
</script>

<template>
    <img
        v-if="src"
        :src="imageUrl(src)"
        :alt="alt"
        :class="imgClass"
        loading="lazy"
        @error="onImageError"
    />
</template>
