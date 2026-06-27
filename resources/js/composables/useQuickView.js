import { inject, provide, ref } from 'vue';

const QUICK_VIEW_KEY = Symbol('quickView');

export function provideQuickView() {
    const isOpen = ref(false);
    const product = ref(null);
    const loading = ref(false);

    const openQuickView = async (slugOrProduct) => {
        if (typeof slugOrProduct === 'object' && slugOrProduct !== null) {
            product.value = slugOrProduct;
            isOpen.value = true;
            return;
        }

        loading.value = true;
        isOpen.value = true;
        product.value = null;

        try {
            const response = await fetch(route('shop.quick-view', slugOrProduct), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) {
                throw new Error('Product not found');
            }
            product.value = await response.json();
        } catch {
            isOpen.value = false;
        } finally {
            loading.value = false;
        }
    };

    const closeQuickView = () => {
        isOpen.value = false;
        product.value = null;
        loading.value = false;
    };

    const state = { isOpen, product, loading, openQuickView, closeQuickView };
    provide(QUICK_VIEW_KEY, state);

    return state;
}

export function useQuickView() {
    const state = inject(QUICK_VIEW_KEY);
    if (!state) {
        throw new Error('useQuickView must be used within ShopLayout');
    }
    return state;
}
