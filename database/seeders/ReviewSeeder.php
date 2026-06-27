<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('is_admin', false)->get();

        if ($customers->isEmpty()) {
            return;
        }

        $reviews = [
            'black-croc-signature-bit' => [
                ['rating' => 5, 'title' => 'Stunning croc texture', 'body' => 'The embossed pattern catches the light perfectly. Comfortable from day one and the bit detail elevates any outfit.', 'verified' => true],
                ['rating' => 5, 'title' => 'Worth every penny', 'body' => 'Premium feel throughout. I receive compliments every time I wear these. Urban Cobblers quality is unmatched.', 'verified' => false],
            ],
            'navy-leather-peshawari' => [
                ['rating' => 5, 'title' => 'Perfect summer formal', 'body' => 'The navy leather is rich and the gold buckle adds a refined touch. Surprisingly comfortable for all-day wear at events.', 'verified' => false],
            ],
            'suede-monk-strap-office-sneaker' => [
                ['rating' => 5, 'title' => 'Office game changer', 'body' => 'Finally a shoe that looks dressy enough for client meetings but feels like a sneaker. The tan suede monk upper is brilliant.', 'verified' => false],
                ['rating' => 4, 'title' => 'Smart hybrid', 'body' => 'Great concept and execution. White sole stays clean with regular wipes. Would love more color options.', 'verified' => false],
            ],
            'dual-tone-croc-bit' => [
                ['rating' => 5, 'title' => 'Statement piece', 'body' => 'The dual-tone croc finish is extraordinary. Pairs perfectly with both navy and charcoal suits.', 'verified' => true],
            ],
            'copper-croc-peshawari' => [
                ['rating' => 5, 'title' => 'Beautiful craftsmanship', 'body' => 'The copper burnish on the croc texture is gorgeous. True artisan quality in every stitch.', 'verified' => false],
            ],
        ];

        $customerIndex = 0;

        foreach ($reviews as $slug => $productReviews) {
            $product = Product::where('slug', $slug)->first();

            if (! $product) {
                continue;
            }

            foreach ($productReviews as $data) {
                $customer = $customers[$customerIndex % $customers->count()];
                $customerIndex++;

                ProductReview::updateOrCreate(
                    ['product_id' => $product->id, 'user_id' => $customer->id],
                    [
                        'rating' => $data['rating'],
                        'title' => $data['title'],
                        'body' => $data['body'],
                        'is_verified_purchase' => $data['verified'],
                        'is_approved' => true,
                    ]
                );
            }
        }
    }
}
