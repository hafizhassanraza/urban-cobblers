<script setup>
const items = [
    'FREE shipping on orders over $200',
    'SS26 Collection — Live Now',
    'Handcrafted in Brooklyn since 1989',
    'Ready to Wear — Ships within 48 hours',
    'Helpline: +1 (555) 123-4567',
    'Mon–Fri: 9:30 AM – 5:30 PM',
];
</script>

<template>
    <div class="overflow-hidden border-b border-brand-black/10 bg-brand-black py-2.5 text-white">
        <div class="marquee-track flex w-max items-center gap-0">
            <div v-for="n in 2" :key="n" class="flex shrink-0 items-center">
                <span
                    v-for="(item, index) in items"
                    :key="`${n}-${index}`"
                    class="flex shrink-0 items-center whitespace-nowrap px-6 font-nav text-xs font-medium tracking-wide"
                >
                    {{ item }}
                    <span class="mx-6 text-brand-copper">•</span>
                </span>
            </div>
        </div>
    </div>
</template>
