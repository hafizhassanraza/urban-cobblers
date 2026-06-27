<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import StarRating from '@/Components/StarRating.vue';

const props = defineProps({
    product: Object,
    reviews: Array,
    reviewStats: Object,
    userReview: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const editing = ref(false);

const form = useForm({
    rating: props.userReview?.rating ?? 5,
    title: props.userReview?.title ?? '',
    body: props.userReview?.body ?? '',
});

const formatDate = (iso) => new Date(iso).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
});

const startEdit = () => {
    form.rating = props.userReview.rating;
    form.title = props.userReview.title ?? '';
    form.body = props.userReview.body;
    editing.value = true;
};

const cancelEdit = () => {
    editing.value = false;
    form.clearErrors();
};

const submitReview = () => {
    if (props.userReview && editing.value) {
        form.put(route('reviews.update', props.userReview.id), {
            preserveScroll: true,
            onSuccess: () => { editing.value = false; },
        });
        return;
    }

    form.post(route('reviews.store', props.product.id), {
        preserveScroll: true,
        onSuccess: () => form.reset('title', 'body'),
    });
};

const deleteReview = () => {
    if (! confirm('Remove your review?')) {
        return;
    }

    useForm({}).delete(route('reviews.destroy', props.userReview.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section class="mt-10 border-t border-brand-beige/40 pt-10">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-brand-black">Client Reviews</h2>
                <div v-if="reviewStats.count" class="mt-3 flex flex-wrap items-center gap-3">
                    <StarRating :rating="reviewStats.average" size="lg" />
                    <span class="text-2xl font-bold text-brand-black">{{ reviewStats.average.toFixed(1) }}</span>
                    <span class="text-sm text-brand-black/50">({{ reviewStats.count }} review{{ reviewStats.count === 1 ? '' : 's' }})</span>
                </div>
                <p v-else class="mt-2 text-sm text-brand-black/50">No reviews yet — be the first to share your experience.</p>
            </div>
        </div>

        <!-- Write / edit form -->
        <div v-if="user && (!userReview || editing)" class="mt-8 rounded-2xl border border-brand-beige/50 bg-brand-beige/10 p-6">
            <h3 class="font-semibold text-brand-black">{{ userReview && editing ? 'Edit Your Review' : 'Write a Review' }}</h3>
            <form class="mt-5 space-y-4" @submit.prevent="submitReview">
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-brand-black/60">Your Rating</label>
                    <StarRating v-model:rating="form.rating" interactive size="lg" />
                    <p v-if="form.errors.rating" class="mt-1 text-sm text-red-600">{{ form.errors.rating }}</p>
                </div>

                <div>
                    <label for="review-title" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-brand-black/60">Title (optional)</label>
                    <input id="review-title" v-model="form.title" type="text" maxlength="120" class="input-field" placeholder="Summarize your experience" />
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label for="review-body" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-brand-black/60">Review</label>
                    <textarea
                        id="review-body"
                        v-model="form.body"
                        rows="4"
                        class="input-field resize-y"
                        placeholder="Tell us about the fit, quality, and craftsmanship..."
                        required
                    />
                    <p v-if="form.errors.body" class="mt-1 text-sm text-red-600">{{ form.errors.body }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : (userReview && editing ? 'Update Review' : 'Submit Review') }}
                    </button>
                    <button v-if="editing" type="button" class="btn-secondary" @click="cancelEdit">Cancel</button>
                </div>
            </form>
        </div>

        <div v-else-if="user && userReview && !editing" class="mt-8 rounded-2xl border border-brand-copper/30 bg-brand-copper/5 p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-copper">Your Review</p>
                    <StarRating :rating="userReview.rating" class="mt-2" />
                    <p v-if="userReview.title" class="mt-2 font-semibold text-brand-black">{{ userReview.title }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-brand-black/70">{{ userReview.body }}</p>
                    <p v-if="!userReview.is_approved" class="mt-2 text-xs text-amber-700">Pending approval</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" class="text-sm font-medium text-brand-copper hover:underline" @click="startEdit">Edit</button>
                    <button type="button" class="text-sm font-medium text-red-600 hover:underline" @click="deleteReview">Delete</button>
                </div>
            </div>
        </div>

        <div v-else class="mt-8 rounded-2xl border border-brand-beige/50 bg-brand-beige/10 p-6 text-center">
            <p class="text-sm text-brand-black/70">Purchased this pair? Share your thoughts with other clients.</p>
            <Link :href="route('login')" class="btn-primary mt-4 inline-flex">Sign in to Review</Link>
        </div>

        <!-- Review list -->
        <div v-if="reviews.length" class="mt-10 space-y-6">
            <article
                v-for="review in reviews.filter(r => !userReview || r.id !== userReview.id)"
                :key="review.id"
                class="border-b border-brand-beige/30 pb-6 last:border-0"
            >
                <div class="flex flex-wrap items-center gap-3">
                    <StarRating :rating="review.rating" size="sm" />
                    <span class="font-semibold text-brand-black">{{ review.reviewer_name }}</span>
                    <span v-if="review.is_verified_purchase" class="rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-green-800">
                        Verified Purchase
                    </span>
                    <span class="text-xs text-brand-black/40">{{ formatDate(review.created_at) }}</span>
                </div>
                <p v-if="review.title" class="mt-2 font-medium text-brand-black">{{ review.title }}</p>
                <p class="mt-2 text-sm leading-relaxed text-brand-black/70">{{ review.body }}</p>
            </article>
        </div>
    </section>
</template>
