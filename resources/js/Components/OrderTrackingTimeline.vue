<script setup>
defineProps({
    progress: {
        type: Object,
        required: true,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div>
        <div v-if="progress.cancelled" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            This order has been cancelled.
        </div>

        <div v-else class="relative">
            <div
                class="absolute left-4 top-0 hidden h-full w-0.5 bg-brand-beige/50 sm:block"
                :class="compact ? 'left-3' : 'left-4'"
            />
            <ol class="space-y-0">
                <li
                    v-for="(step, index) in progress.steps"
                    :key="step.key"
                    class="relative flex gap-4"
                    :class="compact ? 'pb-6 last:pb-0' : 'pb-8 last:pb-0'"
                >
                    <div class="relative z-10 flex shrink-0 items-start">
                        <span
                            class="flex items-center justify-center rounded-full border-2 font-semibold transition"
                            :class="[
                                compact ? 'h-6 w-6 text-[10px]' : 'h-8 w-8 text-xs',
                                index <= progress.currentIndex
                                    ? 'border-brand-copper bg-brand-copper text-white'
                                    : 'border-brand-beige bg-brand-white text-brand-black/40',
                            ]"
                        >
                            <svg v-if="index < progress.currentIndex" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span v-else>{{ index + 1 }}</span>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <p
                            class="font-medium"
                            :class="[
                                compact ? 'text-sm' : 'text-base',
                                index <= progress.currentIndex ? 'text-brand-black' : 'text-brand-black/40',
                            ]"
                        >
                            {{ step.label }}
                        </p>
                        <p v-if="index === progress.currentIndex && !compact" class="mt-1 text-sm text-brand-black/60">
                            Current status
                        </p>
                    </div>
                </li>
            </ol>
        </div>
    </div>
</template>
