<?php

namespace Database\Seeders;

use App\Models\Bundle;
use App\Models\BundleColour;
use App\Models\PlacementOption;
use App\Models\SizeGuide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BundleSeeder extends Seeder
{
    public function run(): void
    {
        $colourIds = $this->seedColours();

        foreach ($this->sizeGuides() as $i => $guide) {
            SizeGuide::updateOrCreate(['key' => $guide['key']], [
                ...$guide,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        foreach ($this->placements() as $i => $placement) {
            PlacementOption::updateOrCreate(['slug' => $placement['slug']], [
                ...$placement,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        foreach ($this->bundles() as $i => $data) {
            $includes = $data['includes'];
            $garments = $data['garments'];
            unset($data['includes'], $data['garments']);

            $bundle = Bundle::updateOrCreate(['slug' => $data['slug']], [
                ...$data,
                'sort_order' => $i,
                'is_active' => true,
            ]);

            $bundle->includes()->delete();
            foreach ($includes as $j => $text) {
                $bundle->includes()->create(['text' => $text, 'sort_order' => $j]);
            }

            $bundle->garments()->delete();
            foreach ($garments as $j => $garment) {
                $row = $bundle->garments()->create([
                    'category_slug' => $garment['cat'],
                    'qty' => $garment['qty'],
                    'label' => $garment['label'],
                    'size_guide_key' => $garment['guide'],
                    'sort_order' => $j,
                ]);

                $names = $this->coloursForGarment($garment['cat'], $garment['label']);
                $sync = [];
                foreach ($names as $ci => $name) {
                    $id = $colourIds[$name] ?? null;
                    if ($id) {
                        $sync[$id] = ['sort_order' => $ci];
                    }
                }
                if ($sync !== []) {
                    $row->colours()->sync($sync);
                }
            }
        }
    }

    /** @return array<string, int> */
    private function seedColours(): array
    {
        $map = [];
        foreach ($this->colourPalette() as $i => $colour) {
            $row = BundleColour::updateOrCreate(
                ['slug' => Str::slug($colour['name'])],
                [
                    'name' => $colour['name'],
                    'hex' => $colour['hex'],
                    'sort_order' => $i,
                    'is_active' => true,
                ]
            );
            $map[$colour['name']] = $row->id;
        }

        return $map;
    }

    /** @return list<array{name:string,hex:string}> */
    private function colourPalette(): array
    {
        return [
            ['name' => 'Black', 'hex' => '#1c1c1c'],
            ['name' => 'Graphite Grey', 'hex' => '#5a5e63'],
            ['name' => 'Navy', 'hex' => '#1f2a44'],
            ['name' => 'Oxford Navy', 'hex' => '#152238'],
            ['name' => 'Burgundy', 'hex' => '#6e1d2c'],
            ['name' => 'Carbon', 'hex' => '#2f3336'],
            ['name' => 'Charcoal', 'hex' => '#3d4349'],
            ['name' => 'Eco Raw', 'hex' => '#cbb89a'],
            ['name' => 'Forest Green', 'hex' => '#1e3d2f'],
            ['name' => 'Grey Marl', 'hex' => '#8b8f94'],
            ['name' => 'Khaki', 'hex' => '#8a7a4b'],
            ['name' => 'Lavender', 'hex' => '#9b8bb8'],
            ['name' => 'Stone', 'hex' => '#c5bba8'],
            ['name' => 'Teal', 'hex' => '#1f6f6a'],
            ['name' => 'White', 'hex' => '#f4f4f2'],
            ['name' => 'Desert Sand', 'hex' => '#c2a57a'],
            ['name' => 'Light Blue', 'hex' => '#7ba3c9'],
            ['name' => 'Pink', 'hex' => '#d48aa8'],
            ['name' => 'Red', 'hex' => '#b4232a'],
            ['name' => 'Yellow', 'hex' => '#f5d000'],
            ['name' => 'Orange', 'hex' => '#ff6a00'],
        ];
    }

    /** @return list<string> */
    private function coloursForGarment(string $categorySlug, string $label): array
    {
        $labelLower = strtolower($label);

        if ($categorySlug === 'hi-vis') {
            if (str_contains($labelLower, 'vest')) {
                return ['Yellow'];
            }

            return ['Orange', 'Yellow'];
        }

        return match ($categorySlug) {
            'polos' => ['Black', 'Graphite Grey', 'Navy'],
            'jackets' => ['Black', 'Navy'],
            't-shirts' => [
                'Black', 'Burgundy', 'Carbon', 'Charcoal', 'Eco Raw', 'Forest Green',
                'Grey Marl', 'Khaki', 'Lavender', 'Navy', 'Stone', 'Teal', 'White',
            ],
            'sweatshirts' => [
                'Black', 'Burgundy', 'Charcoal', 'Desert Sand', 'Forest Green', 'Grey Marl',
                'Light Blue', 'Navy', 'Pink', 'Red', 'White',
            ],
            'hoods' => [
                'Black', 'Burgundy', 'Charcoal', 'Desert Sand', 'Grey Marl',
                'Light Blue', 'Navy', 'Oxford Navy', 'Red',
            ],
            default => ['Black', 'Navy', 'White'],
        };
    }

    private function sizeGuides(): array
    {
        return [
            [
                'key' => 'rx101', 'code' => 'RX101', 'name' => 'Pro Polo',
                'note' => 'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm (sleeve ±1cm).',
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
                'rows' => [
                    ['label' => 'Chest (½)', 'values' => [45, 49, 53, 57, 61, 65, 69, 73]],
                    ['label' => 'Body length', 'values' => [67, 70, 72.5, 75, 77.5, 80, 82.5, 85]],
                    ['label' => 'Sleeve', 'values' => [21.5, 23, 23, 23.5, 24, 24, 24.5, 24.5]],
                ],
            ],
            [
                'key' => 'rx151', 'code' => 'RX151', 'name' => 'Pro T-Shirt',
                'note' => 'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm (sleeve ±1cm).',
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
                'rows' => [
                    ['label' => 'Chest (½)', 'values' => [45, 49, 53, 57, 61, 65, 69, 73]],
                    ['label' => 'Body length', 'values' => [67, 70, 72.5, 75, 77.5, 80, 82.5, 85]],
                    ['label' => 'Sleeve', 'values' => [23, 24.5, 24.5, 25, 25.5, 25.5, 26, 26]],
                ],
            ],
            [
                'key' => 'rx301', 'code' => 'RX301', 'name' => 'Pro Sweatshirt',
                'note' => 'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm.',
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
                'rows' => [
                    ['label' => 'Chest (½)', 'values' => [48, 52, 56, 60, 64, 68, 72, 76]],
                    ['label' => 'Body length', 'values' => [65, 68, 71, 74, 77, 80, 83, 86]],
                    ['label' => 'Sleeve', 'values' => [61.5, 62.5, 65, 64, 67, 68.5, 70, 71]],
                ],
            ],
            [
                'key' => 'rx350', 'code' => 'RX350', 'name' => 'Pro Hoodie',
                'note' => 'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm.',
                'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
                'rows' => [
                    ['label' => 'Chest (½)', 'values' => [50, 54, 58, 62, 66, 70, 74, 79]],
                    ['label' => 'Body length', 'values' => [67, 70, 73, 76, 79, 82, 85, 88]],
                    ['label' => 'Sleeve', 'values' => [60.5, 62, 63.5, 65, 66.5, 68, 69.5, 71.5]],
                ],
            ],
            [
                'key' => 'rx500', 'code' => 'RX500', 'name' => 'Pro Softshell Jacket',
                'note' => 'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm.',
                'sizes' => ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
                'rows' => [
                    ['label' => 'Chest (½)', 'values' => [50, 54, 58, 62, 66, 68, 72]],
                    ['label' => 'Body length', 'values' => [71, 73, 75, 77, 79, 81, 83]],
                    ['label' => 'Sleeve', 'values' => [64, 66, 68, 70, 72, 74, 76]],
                ],
            ],
            [
                'key' => 'rx700', 'code' => 'RX700', 'name' => 'Pro Hi-Vis Vest',
                'note' => 'Chest to-fit sizes in inches — the chest circumference each size is designed for.',
                'sizes' => ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
                'rows' => [
                    ['label' => 'Chest (to fit)', 'values' => ['36"', '40"', '44"', '48"', '52"', '56"', '60"']],
                ],
            ],
            [
                'key' => 'rx710', 'code' => 'RX710', 'name' => 'Pro Hi-Vis Polo',
                'note' => 'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm (sleeve ±1cm).',
                'sizes' => ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
                'rows' => [
                    ['label' => 'Chest (½)', 'values' => [50, 54, 58, 62, 66, 70, 74]],
                    ['label' => 'Body length', 'values' => [74, 76.5, 79, 81.5, 84, 86.5, 89]],
                    ['label' => 'Sleeve', 'values' => [22.5, 23, 23.5, 24, 24.5, 25, 25.5]],
                ],
            ],
        ];
    }

    private function placements(): array
    {
        return [
            ['slug' => 'left_chest', 'label' => 'Left Chest', 'method' => 'both', 'price' => 3.50, 'sizes' => ['70mm', '80mm', '90mm', '100mm'], 'included_in_bundle' => true],
            ['slug' => 'right_chest', 'label' => 'Right Chest', 'method' => 'both', 'price' => 3.50, 'sizes' => ['70mm', '80mm', '90mm'], 'included_in_bundle' => false],
            ['slug' => 'centre_chest', 'label' => 'Centre Chest', 'method' => 'both', 'price' => 6.00, 'sizes' => ['150mm', '200mm', '250mm'], 'included_in_bundle' => false],
            ['slug' => 'back_print', 'label' => 'Back Print', 'method' => 'print', 'price' => 6.00, 'sizes' => ['A4', 'A3', '250mm', '300mm'], 'included_in_bundle' => false],
            ['slug' => 'back_embroidery', 'label' => 'Back Embroidery', 'method' => 'embroidery', 'price' => 8.00, 'sizes' => ['150mm', '200mm', '250mm'], 'included_in_bundle' => false],
            ['slug' => 'left_sleeve', 'label' => 'Left Sleeve', 'method' => 'both', 'price' => 3.50, 'sizes' => ['60mm', '80mm'], 'included_in_bundle' => false],
            ['slug' => 'right_sleeve', 'label' => 'Right Sleeve', 'method' => 'both', 'price' => 3.50, 'sizes' => ['60mm', '80mm'], 'included_in_bundle' => false],
        ];
    }

    private function bundles(): array
    {
        return [
            [
                'slug' => 'solo', 'name' => 'Solo Bundle', 'icon' => 'bi-person-badge',
                'tagline' => 'Sole traders & one-person brands', 'badge' => null, 'featured' => false, 'popular' => false,
                'price' => 89.00, 'save_amount' => 24,
                'blurb' => 'A tidy starter kit to get one person fully branded and looking professional from day one.',
                'methods' => ['embroidery', 'print'],
                'includes' => ['3 × Premium Polo Shirts', '1 × Softshell Jacket', 'Left chest embroidery on all items', 'Free logo artwork setup'],
                'garments' => [
                    ['cat' => 'polos', 'qty' => 3, 'label' => 'Polo Shirt', 'guide' => 'rx101'],
                    ['cat' => 'jackets', 'qty' => 1, 'label' => 'Softshell Jacket', 'guide' => 'rx500'],
                ],
            ],
            [
                'slug' => 'topup', 'name' => 'Top Up Bundle', 'icon' => 'bi-plus-circle',
                'tagline' => 'Add to an existing kit', 'badge' => null, 'featured' => false, 'popular' => false,
                'price' => 119.00, 'save_amount' => 31,
                'blurb' => 'Already set up? Top up your team with a fresh run of branded everyday essentials.',
                'methods' => ['embroidery', 'print'],
                'includes' => ['5 × T-Shirts', '5 × Polo Shirts', 'Left chest branding on all items', 'Mix colours & sizes freely'],
                'garments' => [
                    ['cat' => 't-shirts', 'qty' => 5, 'label' => 'T-Shirt', 'guide' => 'rx151'],
                    ['cat' => 'polos', 'qty' => 5, 'label' => 'Polo Shirt', 'guide' => 'rx101'],
                ],
            ],
            [
                'slug' => 'startup', 'name' => 'Start Up Bundle', 'icon' => 'bi-rocket-takeoff',
                'tagline' => 'New business, fully kitted', 'badge' => 'Most Popular', 'featured' => true, 'popular' => true,
                'price' => 199.00, 'save_amount' => 58,
                'blurb' => 'Everything a new business needs to look established — a complete branded uniform in one go.',
                'methods' => ['embroidery', 'print'],
                'includes' => ['5 × Polo Shirts', '3 × Sweatshirts', '1 × Softshell Jacket', 'Left chest embroidery + back option', 'Free logo setup & digitising'],
                'garments' => [
                    ['cat' => 'polos', 'qty' => 5, 'label' => 'Polo Shirt', 'guide' => 'rx101'],
                    ['cat' => 'sweatshirts', 'qty' => 3, 'label' => 'Sweatshirt', 'guide' => 'rx301'],
                    ['cat' => 'jackets', 'qty' => 1, 'label' => 'Softshell Jacket', 'guide' => 'rx500'],
                ],
            ],
            [
                'slug' => 'safety', 'name' => 'Safety Bundle', 'icon' => 'bi-cone-striped',
                'tagline' => 'Hi-vis & site-ready', 'badge' => null, 'featured' => false, 'popular' => false,
                'price' => 79.00, 'save_amount' => 19,
                'blurb' => 'Compliant, branded hi-vis for site teams — clear identification that meets the job.',
                'methods' => ['print', 'embroidery'],
                'includes' => ['5 × Hi-Vis Vests', '3 × Hi-Vis Polo Shirts', 'Print branding chest & back', 'Free logo artwork setup'],
                'garments' => [
                    ['cat' => 'hi-vis', 'qty' => 5, 'label' => 'Hi-Vis Vest', 'guide' => 'rx700'],
                    ['cat' => 'hi-vis', 'qty' => 3, 'label' => 'Hi-Vis Polo', 'guide' => 'rx710'],
                ],
            ],
            [
                'slug' => 'merch', 'name' => 'Starting Up Your Merch', 'icon' => 'bi-bag-heart',
                'tagline' => 'Launch your own merch line', 'badge' => null, 'featured' => false, 'popular' => false,
                'price' => 249.00, 'save_amount' => 72,
                'blurb' => 'Kick off a clothing brand or club merch drop with a print-ready starter run.',
                'methods' => ['print'],
                'includes' => ['10 × T-Shirts', '10 × Hoodies', 'Full-colour DTF print, front & back', 'Free logo artwork setup'],
                'garments' => [
                    ['cat' => 't-shirts', 'qty' => 10, 'label' => 'T-Shirt', 'guide' => 'rx151'],
                    ['cat' => 'hoods', 'qty' => 10, 'label' => 'Hoodie', 'guide' => 'rx350'],
                ],
            ],
            [
                'slug' => 'team', 'name' => 'Team Bundle', 'icon' => 'bi-people',
                'tagline' => 'Squads, clubs & small teams', 'badge' => 'Great for Clubs', 'featured' => false, 'popular' => false,
                'price' => 169.00, 'save_amount' => 45,
                'blurb' => 'Kit out a small team or club with matching branded wear — mix sizes freely across the order.',
                'methods' => ['embroidery', 'print'],
                'includes' => ['8 × T-Shirts', '4 × Hoodies', 'Left chest branding on all items', 'Free logo artwork setup', 'Mix sizes & colours freely'],
                'garments' => [
                    ['cat' => 't-shirts', 'qty' => 8, 'label' => 'T-Shirt', 'guide' => 'rx151'],
                    ['cat' => 'hoods', 'qty' => 4, 'label' => 'Hoodie', 'guide' => 'rx350'],
                ],
            ],
        ];
    }
}
