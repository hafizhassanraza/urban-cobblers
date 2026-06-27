<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import ShopLayout from '@/Layouts/ShopLayout.vue';
import AuthMethodToggle from '@/Components/AuthMethodToggle.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps({
    items: Array,
    subtotal: Number,
    shipping: Number,
    user: Object,
});

const accountMode = ref('register');

const form = useForm({
    account_mode: 'register',
    login_method: 'email',
    login: '',
    password: '',
    password_confirmation: '',
    shipping_name: props.user?.name || '',
    shipping_email: props.user?.email || '',
    shipping_phone: props.user?.phone || '',
    shipping_address: props.user?.address || '',
    shipping_city: '',
    shipping_zip: '',
    notes: '',
});

const loginForm = useForm({
    login_method: 'email',
    login: '',
    password: '',
    remember: false,
});

const isGuest = computed(() => !props.user);

watch(accountMode, (mode) => {
    form.account_mode = mode;
});

watch(() => form.login_method, () => {
    form.login = '';
    loginForm.login_method = form.login_method;
});

const loginLabel = computed(() => form.login_method === 'email' ? 'Email Address' : 'Phone Number');
const loginInputType = computed(() => form.login_method === 'email' ? 'email' : 'tel');
const loginPlaceholder = computed(() => form.login_method === 'email' ? 'you@example.com' : '+1 555 123 4567');

const total = () => Number(props.subtotal) + Number(props.shipping);

const submitOrder = () => {
    form.account_mode = accountMode.value;
    if (accountMode.value === 'register' && form.login_method === 'email' && !form.shipping_email) {
        form.shipping_email = form.login;
    }
    if (accountMode.value === 'register' && form.login_method === 'phone' && !form.shipping_phone) {
        form.shipping_phone = form.login;
    }
    form.post(route('checkout.store'));
};

const submitLogin = () => {
    loginForm.login_method = form.login_method;
    loginForm.post(route('checkout.login'), {
        onFinish: () => loginForm.reset('password'),
    });
};

const syncContactFromLogin = () => {
    if (form.login_method === 'email' && form.login) {
        form.shipping_email = form.login;
    }
    if (form.login_method === 'phone' && form.login) {
        form.shipping_phone = form.login;
    }
};
</script>

<template>
    <Head title="Checkout" />

    <ShopLayout>
        <FlashMessage />

        <h1 class="font-display text-4xl text-brand-black">Checkout</h1>
        <p v-if="user" class="mt-2 text-sm text-brand-black/60">Signed in as {{ user.name }}</p>

        <form @submit.prevent="submitOrder" class="mt-8 grid gap-8 lg:grid-cols-2">
            <div class="space-y-6">
                <div v-if="isGuest" class="card p-6">
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="rounded-full px-4 py-2 text-sm font-medium transition"
                            :class="accountMode === 'register' ? 'bg-brand-copper text-white' : 'bg-brand-beige/30 text-brand-black hover:bg-brand-beige/50'"
                            @click="accountMode = 'register'"
                        >
                            Create Account
                        </button>
                        <button
                            type="button"
                            class="rounded-full px-4 py-2 text-sm font-medium transition"
                            :class="accountMode === 'login' ? 'bg-brand-copper text-white' : 'bg-brand-beige/30 text-brand-black hover:bg-brand-beige/50'"
                            @click="accountMode = 'login'"
                        >
                            Sign In
                        </button>
                    </div>

                    <div class="mt-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-brand-black/60">
                            {{ accountMode === 'register' ? 'Register with' : 'Sign in with' }}
                        </p>
                        <div class="mt-2">
                            <AuthMethodToggle v-model="form.login_method" />
                        </div>
                    </div>

                    <div v-if="accountMode === 'login'" class="mt-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium">{{ loginLabel }}</label>
                            <input
                                v-model="loginForm.login"
                                :type="loginInputType"
                                required
                                class="input-field mt-1"
                                :placeholder="loginPlaceholder"
                            />
                            <p v-if="loginForm.errors.login" class="mt-1 text-sm text-red-600">{{ loginForm.errors.login }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Password</label>
                            <input v-model="loginForm.password" type="password" required class="input-field mt-1" />
                            <p v-if="loginForm.errors.password" class="mt-1 text-sm text-red-600">{{ loginForm.errors.password }}</p>
                        </div>
                        <button
                            type="button"
                            class="btn-secondary w-full"
                            :disabled="loginForm.processing"
                            @click="submitLogin"
                        >
                            {{ loginForm.processing ? 'Signing in...' : 'Sign In & Continue' }}
                        </button>
                        <p class="text-center text-xs text-brand-black/50">
                            Then complete shipping details below and place your order.
                        </p>
                    </div>

                    <div v-else class="mt-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium">{{ loginLabel }}</label>
                            <input
                                v-model="form.login"
                                :type="loginInputType"
                                required
                                class="input-field mt-1"
                                :placeholder="loginPlaceholder"
                                @blur="syncContactFromLogin"
                            />
                            <p v-if="form.errors.login" class="mt-1 text-sm text-red-600">{{ form.errors.login }}</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium">Password</label>
                                <input v-model="form.password" type="password" required class="input-field mt-1" />
                                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Confirm Password</label>
                                <input v-model="form.password_confirmation" type="password" required class="input-field mt-1" />
                            </div>
                        </div>
                        <p class="text-xs text-brand-black/50">
                            Your account will be created when you place the order. Shipping details below will be saved to your profile.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="font-display text-xl text-brand-black">Shipping Details</h2>

                    <div>
                        <label class="block text-sm font-medium">Full Name</label>
                        <input v-model="form.shipping_name" type="text" required class="input-field mt-1" />
                        <p v-if="form.errors.shipping_name" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_name }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-if="form.login_method !== 'email' || accountMode === 'login' || user">
                            <label class="block text-sm font-medium">Email</label>
                            <input v-model="form.shipping_email" type="email" required class="input-field mt-1" />
                            <p v-if="form.errors.shipping_email" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_email }}</p>
                        </div>
                        <div v-if="form.login_method !== 'phone' || accountMode === 'login' || user">
                            <label class="block text-sm font-medium">Phone</label>
                            <input v-model="form.shipping_phone" type="tel" required class="input-field mt-1" />
                            <p v-if="form.errors.shipping_phone" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_phone }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Address</label>
                        <textarea v-model="form.shipping_address" required rows="2" class="input-field mt-1" />
                        <p v-if="form.errors.shipping_address" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_address }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium">City</label>
                            <input v-model="form.shipping_city" type="text" required class="input-field mt-1" />
                            <p v-if="form.errors.shipping_city" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_city }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">ZIP Code</label>
                            <input v-model="form.shipping_zip" type="text" required class="input-field mt-1" />
                            <p v-if="form.errors.shipping_zip" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_zip }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Order Notes (optional)</label>
                        <textarea v-model="form.notes" rows="2" class="input-field mt-1" />
                    </div>

                    <div v-if="isGuest && accountMode === 'login'" class="rounded-lg border border-brand-beige/40 bg-brand-beige/10 p-4 text-sm text-brand-black/70">
                        Sign in above first, or switch to <button type="button" class="font-medium text-brand-copper hover:underline" @click="accountMode = 'register'">Create Account</button> to register and checkout in one step.
                    </div>
                </div>
            </div>

            <div>
                <div class="card p-6">
                    <h2 class="font-display text-xl text-brand-black">Your Order</h2>
                    <div class="mt-4 space-y-3">
                        <div v-for="item in items" :key="item.id" class="flex justify-between text-sm">
                            <span>{{ item.product.name }} × {{ item.quantity }}</span>
                            <span>${{ (item.product.price * item.quantity).toFixed(2) }}</span>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2 border-t border-brand-beige/40 pt-4 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>${{ Number(subtotal).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span>${{ Number(shipping).toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total</span>
                            <span>${{ total().toFixed(2) }}</span>
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="btn-primary mt-6 w-full"
                        :disabled="form.processing || (isGuest && accountMode === 'login')"
                    >
                        {{ form.processing ? 'Placing Order...' : (isGuest && accountMode === 'register' ? 'Create Account & Place Order' : 'Place Order') }}
                    </button>
                    <p v-if="form.errors.account_mode" class="mt-2 text-sm text-red-600">{{ form.errors.account_mode }}</p>
                </div>
            </div>
        </form>
    </ShopLayout>
</template>
