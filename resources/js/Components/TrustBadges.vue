<script setup>
const badges = [
    { icon: 'truck', title: 'Free Delivery', desc: 'On orders over $200' },
    { icon: 'refresh', title: 'Easy Returns', desc: '30-day exchange policy' },
    { icon: 'chat', title: 'Expert Support', desc: 'Mon–Fri 9:30–5:30' },
    { icon: 'lock', title: 'Secure Checkout', desc: 'Encrypted payments' },
];
</script>

<template>
    <div class="border-y border-brand-beige/30 bg-brand-beige/15">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-8 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div v-for="badge in badges" :key="badge.title" class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-white text-brand-copper shadow-sm">
                    <svg v-if="badge.icon === 'truck'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10m10 0h4m-4 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4m8-8V4a1 1 0 00-1-1h-2"/></svg>
                    <svg v-else-if="badge.icon === 'refresh'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <svg v-else-if="badge.icon === 'chat'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-black">{{ badge.title }}</p>
                    <p class="text-[11px] text-brand-black/50">{{ badge.desc }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
