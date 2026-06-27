<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EcommerceSeeder extends Seeder
{
    protected function categoryImage(string $slug): string
    {
        return match ($slug) {
            'sandals' => '/images/shoes/sandals/sandal-01-navy.png',
            'boots' => '/images/shoes/boots/boot-01.png',
            'office-sneakers' => '/images/shoes/office-sneakers/office-sneaker-01.png',
            'loafers' => '/images/shoes/loafer.jpg',
            'drivers' => '/images/shoes/driver.jpg',
            'slippers' => '/images/shoes/slipper.jpg',
            'sneakers' => '/images/shoes/sneaker.jpg',
            'bespoke' => '/images/shoes/workshop.jpg',
            'shoe-care' => '/images/shoes/care.jpg',
            default => '/images/shoes/loafer.jpg',
        };
    }

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@urbancobblers.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1 555-0100',
            'address' => '123 Main Street, New York, NY 10001',
        ]);

        User::factory()->create([
            'name' => 'Sarah Mitchell',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1 555-0200',
            'address' => '456 Oak Avenue, Brooklyn, NY 11201',
        ]);

        User::factory()->create([
            'name' => 'James Rivera',
            'email' => 'james@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1 555-0300',
            'address' => '789 Pine Road, Austin, TX 78701',
        ]);

        $categories = [
            ['name' => 'Sandals', 'slug' => 'sandals', 'description' => 'Handcrafted Peshawari chappals and leather sandals — croc, ostrich, and classic finishes.', 'image' => '/images/shoes/sandals/sandal-01-navy.png', 'sort_order' => 1],
            ['name' => 'Boots', 'slug' => 'boots', 'description' => 'Premium formal footwear — croc, suede, ostrich textures with signature bit and tassel details.', 'image' => '/images/shoes/boots/boot-01.png', 'sort_order' => 2],
            ['name' => 'Office Sneakers', 'slug' => 'office-sneakers', 'description' => 'Hybrid dress-sneaker styles — monk-strap uppers with cushioned rubber soles for the modern office.', 'image' => '/images/shoes/office-sneakers/office-sneaker-01.png', 'sort_order' => 3],
            ['name' => 'Loafers', 'slug' => 'loafers', 'description' => 'Penny, tassel, and bit loafers — effortless elegance for smart casual.', 'image' => '/images/shoes/loafer.jpg', 'sort_order' => 4],
            ['name' => 'Sneakers', 'slug' => 'sneakers', 'description' => 'Elevated leather sneakers with artisan construction and all-day comfort.', 'image' => '/images/shoes/sneaker.jpg', 'sort_order' => 5],
            ['name' => 'Drivers', 'slug' => 'drivers', 'description' => 'Pebble-sole driving shoes built for comfort on and off the road.', 'image' => '/images/shoes/driver.jpg', 'sort_order' => 6],
            ['name' => 'Slippers', 'slug' => 'slippers', 'description' => 'At-home luxury — unlined calf slippers with suede or leather soles.', 'image' => '/images/shoes/slipper.jpg', 'sort_order' => 7],
            ['name' => 'Bespoke', 'slug' => 'bespoke', 'description' => 'Made-to-measure Goodyear welted footwear — your last, your leather, your fit.', 'image' => '/images/shoes/workshop.jpg', 'sort_order' => 8],
            ['name' => 'Shoe Care', 'slug' => 'shoe-care', 'description' => 'Polishes, brushes, cedar trees, and repair kits to preserve your investment.', 'image' => '/images/shoes/care.jpg', 'sort_order' => 9],
        ];

        foreach ($categories as $category) {
            Category::create([...$category, 'is_active' => true]);
        }

        foreach ($this->productCatalog() as $product) {
            $category = Category::where('slug', $product['category_slug'])->first();
            $categorySlug = $product['category_slug'];
            $image = $product['image'] ?? $this->categoryImage($categorySlug);
            unset($product['category_slug'], $product['image']);

            Product::create([
                ...$product,
                'category_id' => $category->id,
                'image' => $image,
                'sku' => $product['sku'] ?? 'UC-'.strtoupper(Str::random(6)),
                'is_active' => true,
            ]);
        }
    }

    protected function productCatalog(): array
    {
        return [
            // Sandals — uploaded product photos
            ['category_slug' => 'sandals', 'name' => 'Navy Leather Peshawari', 'slug' => 'navy-leather-peshawari', 'sku' => 'UC-SD-001', 'short_description' => 'Matte navy, gold buckle strap.', 'description' => 'Traditional closed-toe Peshawari chappal in deep navy leather with tan lining and gold-toned slingback buckle. US 7–13 / UK 6–12.', 'price' => 185.00, 'compare_price' => 215.00, 'image' => '/images/shoes/sandals/sandal-01-navy.png', 'stock' => 22, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'sandals', 'name' => 'Black Croc Peshawari', 'slug' => 'black-croc-peshawari', 'sku' => 'UC-SD-002', 'short_description' => 'Gloss croc, slingback buckle.', 'description' => 'Jet-black crocodile-embossed leather Peshawari with adjustable slingback and sturdy sole. US 7–13 / UK 6–12.', 'price' => 245.00, 'compare_price' => null, 'image' => '/images/shoes/sandals/sandal-02-black-croc.png', 'stock' => 18, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'sandals', 'name' => 'Copper Croc Peshawari', 'slug' => 'copper-croc-peshawari', 'sku' => 'UC-SD-003', 'short_description' => 'Burnished copper croc texture.', 'description' => 'Warm copper-brown crocodile emboss with antique burnish and gold buckle slingback. US 7–13 / UK 6–12.', 'price' => 245.00, 'compare_price' => 275.00, 'image' => '/images/shoes/sandals/sandal-03-copper-croc.png', 'stock' => 16, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'sandals', 'name' => 'Brown Croc Peshawari', 'slug' => 'brown-croc-peshawari', 'sku' => 'UC-SD-004', 'short_description' => 'Chocolate croc, T-strap design.', 'description' => 'Deep chocolate crocodile-embossed upper with overlapping panels and adjustable heel strap. US 7–13 / UK 6–12.', 'price' => 255.00, 'compare_price' => null, 'image' => '/images/shoes/sandals/sandal-04-brown-croc.png', 'stock' => 14, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'sandals', 'name' => 'Burgundy Ostrich Peshawari', 'slug' => 'burgundy-ostrich-peshawari', 'sku' => 'UC-SD-005', 'short_description' => 'Ostrich quill, mahogany finish.', 'description' => 'Rich burgundy ostrich-quill leather with glossy finish and gold-toned buckle. US 7–13 / UK 6–12.', 'price' => 295.00, 'compare_price' => 335.00, 'image' => '/images/shoes/sandals/sandal-05-burgundy-ostrich.png', 'stock' => 12, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'sandals', 'name' => 'Tan Ostrich Peshawari', 'slug' => 'tan-ostrich-peshawari', 'sku' => 'UC-SD-006', 'short_description' => 'Golden tan ostrich texture.', 'description' => 'Warm tan ostrich-quill leather with raised bump texture and stacked sole. US 7–13 / UK 6–12.', 'price' => 285.00, 'compare_price' => null, 'image' => '/images/shoes/sandals/sandal-06-tan-ostrich.png', 'stock' => 15, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'sandals', 'name' => 'Burgundy Croc Peshawari', 'slug' => 'burgundy-croc-peshawari', 'sku' => 'UC-SD-007', 'short_description' => 'Oxblood croc, pointed toe.', 'description' => 'Deep oxblood crocodile-embossed leather with peep-toe notch and slingback strap. US 7–13 / UK 6–12.', 'price' => 265.00, 'compare_price' => 295.00, 'image' => '/images/shoes/sandals/sandal-07-burgundy-croc.png', 'stock' => 13, 'is_featured' => false, 'is_ready_to_wear' => true],

            // Boots — uploaded product photos
            ['category_slug' => 'boots', 'name' => 'Black Croc Signature Bit', 'slug' => 'black-croc-signature-bit', 'sku' => 'UC-BT-001', 'short_description' => 'Gloss croc with silver hardware.', 'description' => 'High-shine crocodile-embossed leather with signature metal bit detail. Blake-stitched sole. US 7–13 / UK 6–12.', 'price' => 385.00, 'compare_price' => 445.00, 'image' => '/images/shoes/boots/boot-01.png', 'stock' => 18, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Chocolate Croc Bit Loafer', 'slug' => 'chocolate-croc-bit', 'sku' => 'UC-BT-002', 'short_description' => 'Mahogany croc, horsebit buckle.', 'description' => 'Deep chocolate crocodile texture with silver horsebit across the vamp. Cushioned leather insole. US 7–13 / UK 6–12.', 'price' => 365.00, 'compare_price' => null, 'image' => '/images/shoes/boots/boot-02.png', 'stock' => 15, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Polished Black Croc Bit', 'slug' => 'polished-black-croc-bit', 'sku' => 'UC-BT-003', 'short_description' => 'Mirror-finish black croc.', 'description' => 'Reflective black crocodile-embossed upper with polished silver bit hardware. US 7–13 / UK 6–12.', 'price' => 375.00, 'compare_price' => 420.00, 'image' => '/images/shoes/boots/boot-03.png', 'stock' => 22, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Classic Black Croc Bit', 'slug' => 'classic-black-croc-bit', 'sku' => 'UC-BT-004', 'short_description' => 'Embossed croc, silver bar detail.', 'description' => 'Sleek pointed toe with embossed crocodile pattern and silver-toned bit. US 7–13 / UK 6–12.', 'price' => 395.00, 'compare_price' => null, 'image' => '/images/shoes/boots/boot-04.png', 'stock' => 14, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Heritage Striped Bit Loafer', 'slug' => 'heritage-striped-bit', 'sku' => 'UC-BT-005', 'short_description' => 'Pebbled leather, gold horsebit.', 'description' => 'Black pebbled leather with gold horsebit and red-navy striped webbing strap. US 7–12 / UK 6–11.', 'price' => 340.00, 'compare_price' => null, 'image' => '/images/shoes/boots/boot-05.png', 'stock' => 10, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Black Pebbled Bit Loafer', 'slug' => 'black-pebbled-bit', 'sku' => 'UC-BT-006', 'short_description' => 'Textured leather, silver horsebit.', 'description' => 'Deep black pebbled calfskin with silver horsebit on a wide strap. US 7–13 / UK 6–12.', 'price' => 325.00, 'compare_price' => 360.00, 'image' => '/images/shoes/boots/boot-06.png', 'stock' => 16, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Black Gloss Croc Bit', 'slug' => 'black-gloss-croc-bit', 'sku' => 'UC-BT-007', 'short_description' => 'Patent croc, gunmetal hardware.', 'description' => 'High-gloss crocodile emboss with gunmetal horsebit on black strap. US 7–13 / UK 6–12.', 'price' => 390.00, 'compare_price' => null, 'image' => '/images/shoes/boots/boot-07.png', 'stock' => 12, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Ostrich Bit Loafer', 'slug' => 'ostrich-bit-loafer', 'sku' => 'UC-BT-008', 'short_description' => 'Exotic quill texture, gold bit.', 'description' => 'Dark brown ostrich-quill leather with gold-tone bit on grosgrain strap. US 7–13 / UK 6–12.', 'price' => 425.00, 'compare_price' => 480.00, 'image' => '/images/shoes/boots/boot-08.png', 'stock' => 8, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Steel Blue Suede Bit', 'slug' => 'steel-blue-suede-bit', 'sku' => 'UC-BT-009', 'short_description' => 'Dusty blue suede, gold horsebit.', 'description' => 'Soft steel-blue suede upper with gold horsebit detail and black outsole. US 7–13 / UK 6–12.', 'price' => 295.00, 'compare_price' => null, 'image' => '/images/shoes/boots/boot-09.png', 'stock' => 20, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Grey Suede Tassel Loafer', 'slug' => 'grey-suede-tassel', 'sku' => 'UC-BT-010', 'short_description' => 'Charcoal suede, silver tassels.', 'description' => 'Dark grey suede with polished silver tassel bands and apron toe stitching. US 7–13 / UK 6–12.', 'price' => 285.00, 'compare_price' => 315.00, 'image' => '/images/shoes/boots/boot-10.png', 'stock' => 18, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Dual Tone Croc Bit', 'slug' => 'dual-tone-croc-bit', 'sku' => 'UC-BT-011', 'short_description' => 'Brown & black croc pair.', 'description' => 'Premium crocodile-embossed leather in mahogany and jet black with gold horsebit. US 7–13 / UK 6–12.', 'price' => 410.00, 'compare_price' => 465.00, 'image' => '/images/shoes/boots/boot-11.png', 'stock' => 14, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Mahogany Croc Bit Loafer', 'slug' => 'mahogany-croc-bit', 'sku' => 'UC-BT-012', 'short_description' => 'Rich brown croc, silver buckle.', 'description' => 'Deep mahogany crocodile texture with rectangular silver buckle hardware. US 7–13 / UK 6–12.', 'price' => 355.00, 'compare_price' => null, 'image' => '/images/shoes/boots/boot-12.png', 'stock' => 16, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'boots', 'name' => 'Black Suede Bit Loafer', 'slug' => 'black-suede-bit', 'sku' => 'UC-BT-013', 'short_description' => 'Matte black suede, silver bit.', 'description' => 'Soft black suede with silver horsebit and contrasting tan leather lining. US 7–13 / UK 6–12.', 'price' => 275.00, 'compare_price' => 305.00, 'image' => '/images/shoes/boots/boot-13.png', 'stock' => 24, 'is_featured' => true, 'is_ready_to_wear' => true],

            // Office Sneakers — uploaded + complementary
            ['category_slug' => 'office-sneakers', 'name' => 'Suede Monk-Strap Office Sneaker', 'slug' => 'suede-monk-strap-office-sneaker', 'sku' => 'UC-OS-001', 'short_description' => 'Tan suede monk, white cupsole.', 'description' => 'A sophisticated hybrid blending a classic single monk-strap upper in premium tan suede with a cushioned white rubber cupsole. US 7–13 / UK 6–12.', 'price' => 245.00, 'compare_price' => 275.00, 'image' => '/images/shoes/office-sneakers/office-sneaker-01.png', 'stock' => 24, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'office-sneakers', 'name' => 'Black Leather Office Sneaker', 'slug' => 'black-leather-office-sneaker', 'sku' => 'UC-OS-002', 'short_description' => 'Minimal black calf, white sole.', 'description' => 'Clean black calfskin upper with white cupsole — boardroom-ready comfort for long office days. US 7–13 / UK 6–12.', 'price' => 225.00, 'compare_price' => null, 'image' => '/images/shoes/sneaker.jpg', 'stock' => 20, 'is_featured' => false, 'is_ready_to_wear' => true],

            // Loafers — complementary collection
            ['category_slug' => 'loafers', 'name' => 'Urban Penny Loafer', 'slug' => 'urban-penny-loafer', 'sku' => 'UC-LF-001', 'short_description' => 'Hand-stitched penny loafer.', 'description' => 'Hand-stitched apron, cushioned leather insole, Blake-stitched flexible sole. US 7–13 / UK 6–12.', 'price' => 245.00, 'compare_price' => null, 'image' => '/images/shoes/loafer.jpg', 'stock' => 30, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'loafers', 'name' => 'Tassel Loafer', 'slug' => 'tassel-loafer', 'sku' => 'UC-LF-002', 'short_description' => 'Classic suede tassel loafer.', 'description' => 'Supple suede tassel loafer with leather sole and cushioned footbed. US 7–13 / UK 6–12.', 'price' => 235.00, 'compare_price' => 265.00, 'image' => '/images/shoes/loafer.jpg', 'stock' => 20, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'loafers', 'name' => 'Horsebit Bit Loafer', 'slug' => 'horsebit-bit-loafer', 'sku' => 'UC-LF-003', 'short_description' => 'Polished calf, silver horsebit.', 'description' => 'Mirror-shine calfskin with signature horsebit hardware across the vamp. US 7–13 / UK 6–12.', 'price' => 268.00, 'compare_price' => 295.00, 'image' => '/images/shoes/loafer.jpg', 'stock' => 16, 'is_featured' => true, 'is_ready_to_wear' => true],

            // Sneakers — complementary
            ['category_slug' => 'sneakers', 'name' => 'Leather Court Sneaker', 'slug' => 'leather-court-sneaker', 'sku' => 'UC-SN-001', 'short_description' => 'Minimal white leather sneaker.', 'description' => 'White calfskin with leather lining and cushioned footbed. US 7–13 / UK 6–12.', 'price' => 195.00, 'compare_price' => 225.00, 'image' => '/images/shoes/sneaker.jpg', 'stock' => 32, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'sneakers', 'name' => 'Suede Retro Runner', 'slug' => 'suede-retro-runner', 'sku' => 'UC-SN-002', 'short_description' => 'Heritage panel runner.', 'description' => 'Leather and suede panel runner with cushioned EVA midsole. US 7–13 / UK 6–12.', 'price' => 210.00, 'compare_price' => null, 'image' => '/images/shoes/sneaker2.jpg', 'stock' => 20, 'is_featured' => false, 'is_ready_to_wear' => true],

            // Drivers
            ['category_slug' => 'drivers', 'name' => 'Pebble Grain Driver', 'slug' => 'pebble-grain-driver', 'sku' => 'UC-DR-001', 'short_description' => 'Rubber pebble sole driver.', 'description' => 'Soft unlined pebble grain driver with cushioned heel. US 7–13 / UK 6–12.', 'price' => 195.00, 'compare_price' => null, 'image' => '/images/shoes/driver.jpg', 'stock' => 28, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'drivers', 'name' => 'Driving Moccasin', 'slug' => 'driving-moccasin', 'sku' => 'UC-DR-002', 'short_description' => 'Hand-laced moccasin driver.', 'description' => 'Unlined moccasin with rubber pebble sole for weekend escapes. US 7–13 / UK 6–12.', 'price' => 185.00, 'compare_price' => 210.00, 'image' => '/images/shoes/driver.jpg', 'stock' => 24, 'is_featured' => false, 'is_ready_to_wear' => true],

            // Slippers
            ['category_slug' => 'slippers', 'name' => 'Calf Leather Slipper', 'slug' => 'calf-leather-slipper', 'sku' => 'UC-SL-001', 'short_description' => 'Unlined at-home slipper.', 'description' => 'Buttery calf slipper with suede sole. Ships within 48 hours. US 7–13 / UK 6–12.', 'price' => 125.00, 'compare_price' => null, 'image' => '/images/shoes/slipper.jpg', 'stock' => 35, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'slippers', 'name' => 'Quilted House Slipper', 'slug' => 'quilted-house-slipper', 'sku' => 'UC-SL-002', 'short_description' => 'Quilted lining slipper.', 'description' => 'Quilted leather lining with flexible rubber outsole. US 7–13 / UK 6–12.', 'price' => 135.00, 'compare_price' => 155.00, 'image' => '/images/shoes/slipper.jpg', 'stock' => 30, 'is_featured' => false, 'is_ready_to_wear' => true],

            // Bespoke
            ['category_slug' => 'bespoke', 'name' => 'Bespoke Oxford Consultation', 'slug' => 'bespoke-oxford-consultation', 'sku' => 'UC-BS-001', 'short_description' => 'Made-to-measure oxford deposit.', 'description' => 'Deposit for bespoke oxford: measurement, last selection, leather choice, two fittings. From $1,200.', 'price' => 250.00, 'compare_price' => null, 'image' => '/images/shoes/workshop.jpg', 'stock' => 99, 'is_featured' => true, 'is_ready_to_wear' => false],
            ['category_slug' => 'bespoke', 'name' => 'Bespoke Loafer Consultation', 'slug' => 'bespoke-loafer-consultation', 'sku' => 'UC-BS-002', 'short_description' => 'Custom loafer fitting deposit.', 'description' => 'Deposit for bespoke loafer or Peshawari service. Final price from $900.', 'price' => 200.00, 'compare_price' => null, 'image' => '/images/shoes/workshop.jpg', 'stock' => 99, 'is_featured' => false, 'is_ready_to_wear' => false],

            // Shoe Care
            ['category_slug' => 'shoe-care', 'name' => 'Complete Shoe Care Kit', 'slug' => 'complete-care-set', 'sku' => 'UC-SC-001', 'short_description' => 'Full polish & conditioning kit.', 'description' => 'Conditioner, cream polish, horsehair brushes, daubers, and microfiber cloth.', 'price' => 68.00, 'compare_price' => null, 'image' => '/images/shoes/care.jpg', 'stock' => 50, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'shoe-care', 'name' => 'Cedar Shoe Trees (Pair)', 'slug' => 'cedar-shoe-trees', 'sku' => 'UC-SC-002', 'short_description' => 'Aromatic cedar, adjustable.', 'description' => 'Premium cedar shoe trees. Adjustable US 7–13.', 'price' => 45.00, 'compare_price' => null, 'image' => '/images/shoes/care.jpg', 'stock' => 40, 'is_featured' => true, 'is_ready_to_wear' => true],
            ['category_slug' => 'shoe-care', 'name' => 'Exotic Leather Conditioner', 'slug' => 'exotic-leather-conditioner', 'sku' => 'UC-SC-003', 'short_description' => 'For croc & ostrich finishes.', 'description' => 'Specialty conditioner for embossed crocodile and ostrich-quill leathers.', 'price' => 38.00, 'compare_price' => 45.00, 'image' => '/images/shoes/care.jpg', 'stock' => 35, 'is_featured' => false, 'is_ready_to_wear' => true],
            ['category_slug' => 'shoe-care', 'name' => 'Premium Laces (3-Pack)', 'slug' => 'premium-laces', 'sku' => 'UC-SC-004', 'short_description' => 'Waxed cotton dress laces.', 'description' => 'Three pairs of waxed cotton laces in black, brown, and tan.', 'price' => 18.00, 'compare_price' => null, 'image' => '/images/shoes/care.jpg', 'stock' => 60, 'is_featured' => false, 'is_ready_to_wear' => true],
        ];
    }
}
