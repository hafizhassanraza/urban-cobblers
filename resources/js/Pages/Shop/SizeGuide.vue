<script setup>
import { Head } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';

const mensSizes = [
    { us: 7, uk: 6, eu: 40, cm: 25.0 },
    { us: 8, uk: 7, eu: 41, cm: 26.0 },
    { us: 9, uk: 8, eu: 42, cm: 27.0 },
    { us: 10, uk: 9, eu: 43, cm: 28.0 },
    { us: 11, uk: 10, eu: 44, cm: 29.0 },
    { us: 12, uk: 11, eu: 45, cm: 30.0 },
    { us: 13, uk: 12, eu: 46, cm: 31.0 },
];

const womensSizes = [
    { us: 5, uk: 3, eu: 35, cm: 22.0 },
    { us: 6, uk: 4, eu: 36, cm: 23.0 },
    { us: 7, uk: 5, eu: 37, cm: 24.0 },
    { us: 8, uk: 6, eu: 38, cm: 25.0 },
    { us: 9, uk: 7, eu: 39, cm: 26.0 },
    { us: 10, uk: 8, eu: 40, cm: 27.0 },
    { us: 11, uk: 9, eu: 41, cm: 28.0 },
];
</script>

<template>
    <Head title="Size Guide" />

    <ShopLayout>
        <p class="section-label">Fit Guide</p>
        <h1 class="section-title mt-2">Size Guide</h1>
        <p class="mt-3 max-w-2xl text-brand-black/60">Our shoes run true to US sizing. Measure your foot length and match to the chart below.</p>

        <div class="mt-10 grid gap-10 lg:grid-cols-2">
            <div class="card overflow-hidden">
                <h2 class="bg-brand-black px-6 py-4 font-display text-lg text-white">Men's Sizes</h2>
                <table class="w-full text-sm">
                    <thead class="bg-brand-beige/30 text-left text-xs uppercase tracking-wider">
                        <tr><th class="px-6 py-3">US</th><th class="px-6 py-3">UK</th><th class="px-6 py-3">EU</th><th class="px-6 py-3">CM</th></tr>
                    </thead>
                    <tbody class="divide-y divide-brand-beige/30">
                        <tr v-for="row in mensSizes" :key="row.us" class="hover:bg-brand-beige/10">
                            <td class="px-6 py-3 font-medium">{{ row.us }}</td>
                            <td class="px-6 py-3">{{ row.uk }}</td>
                            <td class="px-6 py-3">{{ row.eu }}</td>
                            <td class="px-6 py-3">{{ row.cm }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card overflow-hidden">
                <h2 class="bg-brand-black px-6 py-4 font-display text-lg text-white">Women's Sizes</h2>
                <table class="w-full text-sm">
                    <thead class="bg-brand-beige/30 text-left text-xs uppercase tracking-wider">
                        <tr><th class="px-6 py-3">US</th><th class="px-6 py-3">UK</th><th class="px-6 py-3">EU</th><th class="px-6 py-3">CM</th></tr>
                    </thead>
                    <tbody class="divide-y divide-brand-beige/30">
                        <tr v-for="row in womensSizes" :key="row.us" class="hover:bg-brand-beige/10">
                            <td class="px-6 py-3 font-medium">{{ row.us }}</td>
                            <td class="px-6 py-3">{{ row.uk }}</td>
                            <td class="px-6 py-3">{{ row.eu }}</td>
                            <td class="px-6 py-3">{{ row.cm }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-10 card p-8">
            <h2 class="font-display text-xl text-brand-black">How to Measure</h2>
            <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm text-brand-black/70">
                <li>Place a sheet of paper on the floor against a wall.</li>
                <li>Stand with your heel against the wall on the paper.</li>
                <li>Mark the tip of your longest toe.</li>
                <li>Measure from the wall to the mark in centimeters.</li>
                <li>Match your measurement to the CM column above.</li>
            </ol>
            <p class="mt-4 text-sm text-brand-black/60">Between sizes? Size up for loafers and drivers; size down for oxfords with thick socks.</p>
        </div>
    </ShopLayout>
</template>
