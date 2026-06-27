import { shoeImages } from './images';

export const utilityLinks = [
    { label: 'Track Order', route: 'track-order.index' },
    { label: 'Contact Us', route: 'contact' },
    { label: 'Size Guide', route: 'size-guide' },
    { label: 'Our Story', route: 'about' },
];

export const megaMenu = [
    {
        label: "Men's",
        columns: [
            {
                title: 'Collections',
                links: [
                    { label: 'Sandals', slug: 'sandals' },
                    { label: 'Boots', slug: 'boots' },
                    { label: 'Loafers', slug: 'loafers' },
                    { label: 'Drivers', slug: 'drivers' },
                    { label: 'Slippers', slug: 'slippers' },
                ],
            },
            {
                title: 'Athleisure',
                links: [
                    { label: 'Office Sneakers', slug: 'office-sneakers' },
                    { label: 'Sneakers', slug: 'sneakers' },
                ],
            },
            {
                title: 'Bespoke',
                links: [
                    { label: 'Made to Order', slug: 'bespoke' },
                ],
            },
            {
                title: 'Accessories',
                links: [
                    { label: 'Shoe Care', slug: 'shoe-care' },
                ],
            },
        ],
    },
    {
        label: 'Accessories',
        columns: [
            {
                title: 'Shoe Care',
                links: [
                    { label: 'All Care', slug: 'shoe-care' },
                    { label: 'Polish & Kits', slug: 'shoe-care' },
                    { label: 'Cedar Trees', slug: 'shoe-care' },
                ],
            },
        ],
    },
];

export const ordersNav = {
    label: 'Orders',
    route: 'orders.index',
};

export const popularSearches = ['Sandals', 'Boots', 'Office Sneakers', 'Loafers', 'Peshawari', 'New Arrivals'];

export const shopByStyle = [
    { label: 'Sandals', slug: 'sandals', image: shoeImages.sandal },
    { label: 'Boots', slug: 'boots', image: shoeImages.boot },
    { label: 'Office Sneakers', slug: 'office-sneakers', image: shoeImages.officeSneaker },
    { label: 'Loafers', slug: 'loafers', image: shoeImages.loafer },
    { label: 'Drivers', slug: 'drivers', image: shoeImages.driver },
    { label: 'Sneakers', slug: 'sneakers', image: shoeImages.sneaker },
];

export const journalStories = [
    {
        title: 'The Peshawari Chappal',
        excerpt: 'Centuries of craft meet modern luxury — croc, ostrich, and classic leather finishes...',
        image: shoeImages.sandal,
        slug: 'navy-leather-peshawari',
    },
    {
        title: 'The Signature Bit Loafer',
        excerpt: 'Crocodile emboss, silver hardware, and a silhouette that commands the room...',
        image: shoeImages.boot,
        slug: 'black-croc-signature-bit',
    },
    {
        title: 'The Office Sneaker',
        excerpt: 'Where boardroom polish meets all-day comfort — the hybrid shoe for the modern professional.',
        image: shoeImages.officeSneaker,
        slug: 'suede-monk-strap-office-sneaker',
    },
    {
        title: 'Behind The Craft',
        excerpt: 'Explore the story behind our artisan cobblers and the timeless techniques in every pair.',
        image: shoeImages.workshop,
        slug: null,
        route: 'about',
    },
];

export function categoryUrl(slug, query = {}) {
    const params = Object.keys(query).length ? query : undefined;
    return route('categories.show', slug, false) + (params ? '?' + new URLSearchParams(params).toString() : '');
}
