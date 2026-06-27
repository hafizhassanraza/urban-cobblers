<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import { shoeImages } from '@/data/images';

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

onMounted(() => {
    const email = new URLSearchParams(window.location.search).get('email');
    if (email) form.email = email;
});

const submit = () => {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const contactInfo = [
    { label: 'Email', value: 'hello@urbancobblers.com' },
    { label: 'Phone', value: '+1 (555) 123-4567' },
    { label: 'Workshop', value: '142 Heritage Lane, Brooklyn, NY 11201' },
    { label: 'Hours', value: 'Mon–Sat: 10am – 7pm' },
];
</script>

<template>
    <Head title="Contact" />

    <ShopLayout>
        <div class="mb-10">
            <p class="section-label">Get in Touch</p>
            <h1 class="section-title mt-2">Contact Us</h1>
            <p class="mt-3 max-w-xl text-brand-black/60">Questions about sizing, bespoke shoes, or repairs? Our cobblers are here to help.</p>
        </div>

        <div class="grid gap-10 lg:grid-cols-5">
            <div class="lg:col-span-3">
                <form @submit.prevent="submit" class="card space-y-5 p-8">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-brand-black">Name</label>
                            <input v-model="form.name" type="text" required class="input-field mt-1.5" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-black">Email</label>
                            <input v-model="form.email" type="email" required class="input-field mt-1.5" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-brand-black">Subject</label>
                        <select v-model="form.subject" required class="input-field mt-1.5">
                            <option value="">Select a topic</option>
                            <option value="order">Order Inquiry</option>
                            <option value="sizing">Sizing & Fit Help</option>
                            <option value="bespoke">Bespoke Shoes</option>
                            <option value="repair">Shoe Repair / Resoling</option>
                            <option value="wholesale">Wholesale</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-brand-black">Message</label>
                        <textarea v-model="form.message" required rows="5" class="input-field mt-1.5" placeholder="How can we help?" />
                    </div>
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Sending...' : 'Send Message' }}
                    </button>
                </form>
            </div>

            <div class="space-y-4 lg:col-span-2">
                <div v-for="info in contactInfo" :key="info.label" class="card p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-copper">{{ info.label }}</p>
                    <p class="mt-1 font-medium text-brand-black">{{ info.value }}</p>
                </div>
                <div class="overflow-hidden rounded-2xl">
                    <img :src="shoeImages.workshop" alt="Workshop" class="w-full object-cover aspect-video" />
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
