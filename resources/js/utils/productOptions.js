export const COLOR_PALETTE = [
    { name: 'Black', hex: '#111111' },
    { name: 'Brown', hex: '#6B4423' },
    { name: 'Tan', hex: '#C4A574' },
    { name: 'Coffee', hex: '#3E2723' },
];

export const MENS_SIZES = [
    { us: 7, uk: 6 },
    { us: 8, uk: 7 },
    { us: 9, uk: 8 },
    { us: 10, uk: 9 },
    { us: 11, uk: 10 },
    { us: 12, uk: 11 },
    { us: 13, uk: 12 },
];

export const WOMENS_SIZES = [
    { us: 5, uk: 3 },
    { us: 6, uk: 4 },
    { us: 7, uk: 5 },
    { us: 8, uk: 6 },
    { us: 9, uk: 7 },
    { us: 10, uk: 8 },
    { us: 11, uk: 9 },
];

export function productColors(product) {
    if (!product?.id) {
        return COLOR_PALETTE.slice(0, 2);
    }

    const count = 2 + (product.id % 3);

    return COLOR_PALETTE.slice(0, count);
}

export function productSizes(product) {
    const slug = product?.category?.slug ?? '';

    if (slug.startsWith('womens-')) {
        return WOMENS_SIZES;
    }

    return MENS_SIZES;
}

export function sizeLabel(us, sizes) {
    const row = sizes.find((entry) => entry.us === us);

    if (!row) {
        return `US ${us}`;
    }

    return `US ${row.us} / UK ${row.uk}`;
}
