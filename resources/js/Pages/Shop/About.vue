<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { shoeImages } from '@/data/images';

const values = [
    { title: 'Heritage Cobbling', desc: 'We preserve centuries-old shoemaking techniques — lasting, welting, and hand-finishing passed through generations.' },
    { title: 'Finest Leathers', desc: 'Full-grain calfskin and suede sourced from trusted European tanneries with full traceability.' },
    { title: 'Built to Last', desc: 'Goodyear welt construction means your shoes can be resoled for decades — not discarded after a season.' },
    { title: 'Perfect Fit', desc: 'Multiple widths and bespoke services ensure every pair feels like it was made just for you.' },
];

const timeline = [
    { year: '1989', event: 'Opened our first shoe workshop in Brooklyn' },
    { year: '2001', event: 'Launched bespoke made-to-measure shoe service' },
    { year: '2015', event: 'Introduced women\'s collection and online store' },
    { year: '2024', event: '35 years of handcrafted footwear' },
];
</script>

<template>
    <Head title="About Us" />

    <ShopLayout>
        <div class="relative -mx-4 -mt-8 mb-16 overflow-hidden sm:-mx-6 lg:-mx-8">
            <div class="relative h-72 sm:h-96">
                <img :src="shoeImages.workshop" alt="Shoe workshop" class="absolute inset-0 h-full w-full object-cover" />
                <div class="absolute inset-0 bg-brand-black/50" />
                <div class="relative flex h-full items-center justify-center text-center px-4">
                    <div>
                        <p class="section-label !text-brand-copper">Our Story</p>
                        <h1 class="mt-3 font-display text-5xl text-white">The Art of Shoemaking</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-3xl text-center">
            <p class="text-lg leading-relaxed text-brand-black/70">
                Urban Cobblers is a footwear brand born in Brooklyn. For over three decades we've been handcrafting shoes for people who believe what you walk in matters — oxfords for the boardroom, boots for the weekend, and bespoke pairs for life's biggest moments.
            </p>
        </div>

        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="v in values" :key="v.title" class="card-hover p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-copper/10 font-display text-brand-copper">✦</div>
                <h3 class="mt-4 font-display text-xl text-brand-black">{{ v.title }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-brand-black/60">{{ v.desc }}</p>
            </div>
        </div>

        <div class="mt-20 grid gap-12 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="section-label">Timeline</p>
                <h2 class="section-title mt-2">35 Years of Footwear</h2>
                <div class="mt-8 space-y-6">
                    <div v-for="t in timeline" :key="t.year" class="flex gap-6">
                        <span class="shrink-0 font-display text-2xl text-brand-copper">{{ t.year }}</span>
                        <p class="pt-1 text-brand-black/70">{{ t.event }}</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <img :src="shoeImages.oxford" alt="Dress shoes" class="rounded-2xl object-cover aspect-square" />
                <img :src="shoeImages.boot" alt="Chelsea boots" class="rounded-2xl object-cover aspect-square mt-8" />
            </div>
        </div>

        <div class="mt-20 rounded-3xl bg-brand-beige/30 p-10 text-center lg:p-16">
            <h2 class="font-display text-3xl text-brand-black">Visit Our Shoe Workshop</h2>
            <p class="mx-auto mt-4 max-w-lg text-brand-black/60">Try on styles in person, get fitted by our cobblers, or book a bespoke consultation.</p>
            <Link :href="route('contact')" class="btn-primary mt-8 inline-flex">Book a Fitting</Link>
        </div>
    </ShopLayout>
</template>
