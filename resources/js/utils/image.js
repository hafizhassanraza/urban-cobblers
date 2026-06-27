import { placeholderImage } from '@/data/images';

export function imageUrl(path) {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path;
    }
    return `/storage/${path}`;
}

export function onImageError(event) {
    if (event?.target && event.target.src !== placeholderImage) {
        event.target.src = placeholderImage;
    }
}

export function formatPrice(amount) {
    return Number(amount).toFixed(2);
}

export function discountPercent(product) {
    if (!product.compare_price || product.compare_price <= product.price) return null;
    return Math.round(((product.compare_price - product.price) / product.compare_price) * 100);
}
