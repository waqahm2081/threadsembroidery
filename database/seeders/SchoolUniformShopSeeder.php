<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolProduct;
use App\Models\SchoolProductCategory;
use App\Models\SchoolProductImage;
use App\Models\SchoolProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolUniformShopSeeder extends Seeder
{
    public function run(): void
    {
        $fixtures = database_path('seeders/fixtures');
        $disk = Storage::disk('public');

        $schools = [
            [
                'name' => 'Oakridge Primary School',
                'logo' => 'school-logo-1.png',
                'accent_colour' => '#1e4d8c',
                'contact_email' => 'office@oakridge-primary.example',
                'contact_phone' => '01884 100001',
                'address' => '12 Oak Lane, Cullompton',
                'description' => 'Official schoolwear for Oakridge Primary pupils.',
                'categories' => [
                    [
                        'name' => 'Everyday Uniform',
                        'description' => 'Day-to-day school clothing',
                        'products' => [
                            ['name' => 'Navy Polo Shirt', 'desc' => 'Classic embroidered polo for everyday wear.', 'image' => 'product-polo.png', 'sizes' => [
                                ['Age 3-4', 12.50], ['Age 5-6', 12.50], ['Age 7-8', 13.50], ['Age 9-10', 13.50], ['Age 11-12', 14.50], ['XS Adult', 16.00],
                            ]],
                            ['name' => 'Grey School Trousers', 'desc' => 'Smart grey trousers for boys and girls.', 'image' => 'product-trousers.png', 'sizes' => [
                                ['Age 3-4', 15.00], ['Age 5-6', 15.00], ['Age 7-8', 16.50], ['Age 9-10', 16.50], ['Age 11-12', 18.00],
                            ]],
                        ],
                    ],
                    [
                        'name' => 'Outerwear',
                        'description' => 'Jumpers and hoodies',
                        'products' => [
                            ['name' => 'School Sweatshirt', 'desc' => 'Warm crew-neck sweatshirt in school colours.', 'image' => 'product-hoodie.png', 'sizes' => [
                                ['Age 3-4', 18.00], ['Age 5-6', 18.00], ['Age 7-8', 19.50], ['Age 9-10', 19.50], ['Age 11-12', 21.00], ['XS Adult', 23.00],
                            ]],
                        ],
                    ],
                    [
                        'name' => 'PE Kit',
                        'description' => 'Sports and PE clothing',
                        'products' => [
                            ['name' => 'PE T-Shirt', 'desc' => 'Lightweight PE top for indoor and outdoor sport.', 'image' => 'product-pe.png', 'sizes' => [
                                ['Age 3-4', 9.50], ['Age 5-6', 9.50], ['Age 7-8', 10.50], ['Age 9-10', 10.50], ['Age 11-12', 11.50],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Riverside Academy',
                'logo' => 'school-logo-2.png',
                'accent_colour' => '#0d7a5f',
                'contact_email' => 'uniform@riverside-academy.example',
                'contact_phone' => '01884 100002',
                'address' => '88 River Road, Exeter',
                'description' => 'Riverside Academy uniform shop — secondary & sixth form.',
                'categories' => [
                    [
                        'name' => 'Blazer & Shirts',
                        'description' => 'Formal day wear',
                        'products' => [
                            ['name' => 'Academy Polo', 'desc' => 'Teal-trim polo for Years 7–11.', 'image' => 'product-polo.png', 'sizes' => [
                                ['XS', 14.00], ['S', 14.00], ['M', 15.00], ['L', 15.00], ['XL', 16.00],
                            ]],
                            ['name' => 'Academy Trousers', 'desc' => 'Black school trousers.', 'image' => 'product-trousers.png', 'sizes' => [
                                ['24"', 18.00], ['26"', 18.00], ['28"', 19.50], ['30"', 19.50], ['32"', 21.00],
                            ]],
                        ],
                    ],
                    [
                        'name' => 'Jumpers',
                        'description' => 'Knitwear & sweatshirts',
                        'products' => [
                            ['name' => 'Academy Hoodie', 'desc' => 'Soft hoodie with school branding space.', 'image' => 'product-hoodie.png', 'sizes' => [
                                ['XS', 22.00], ['S', 22.00], ['M', 24.00], ['L', 24.00], ['XL', 26.00],
                            ]],
                            ['name' => 'PE Performance Tee', 'desc' => 'Breathable PE shirt.', 'image' => 'product-pe.png', 'sizes' => [
                                ['XS', 11.00], ['S', 11.00], ['M', 12.00], ['L', 12.00], ['XL', 13.00],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'St Mary\'s Grammar',
                'logo' => 'school-logo-3.png',
                'accent_colour' => '#8b1e3f',
                'contact_email' => 'shop@stmarys-grammar.example',
                'contact_phone' => '01884 100003',
                'address' => '1 Chapel Street, Tiverton',
                'description' => 'St Mary\'s Grammar official uniform supplier.',
                'categories' => [
                    [
                        'name' => 'Winter Uniform',
                        'description' => 'Cold-weather essentials',
                        'products' => [
                            ['name' => 'Burgundy Sweatshirt', 'desc' => 'Heavyweight winter sweatshirt.', 'image' => 'product-hoodie.png', 'sizes' => [
                                ['Age 7-8', 20.00], ['Age 9-10', 20.00], ['Age 11-12', 22.00], ['XS', 24.00], ['S', 24.00], ['M', 26.00],
                            ]],
                            ['name' => 'Winter Trousers', 'desc' => 'Lined school trousers for winter term.', 'image' => 'product-trousers.png', 'sizes' => [
                                ['Age 7-8', 17.00], ['Age 9-10', 17.00], ['Age 11-12', 19.00], ['XS', 21.00], ['S', 21.00],
                            ]],
                        ],
                    ],
                    [
                        'name' => 'Summer Uniform',
                        'description' => 'Lighter summer options',
                        'products' => [
                            ['name' => 'Summer Polo', 'desc' => 'Light polo for summer term.', 'image' => 'product-polo.png', 'sizes' => [
                                ['Age 7-8', 13.00], ['Age 9-10', 13.00], ['Age 11-12', 14.00], ['XS', 15.50], ['S', 15.50], ['M', 16.50],
                            ]],
                            ['name' => 'Games Shirt', 'desc' => 'Games and athletics shirt.', 'image' => 'product-pe.png', 'sizes' => [
                                ['Age 7-8', 10.00], ['Age 9-10', 10.00], ['Age 11-12', 11.00], ['XS', 12.00], ['S', 12.00],
                            ]],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($schools as $schoolIndex => $schoolData) {
            $logoPath = null;
            $logoSource = $fixtures.DIRECTORY_SEPARATOR.$schoolData['logo'];
            if (is_file($logoSource)) {
                $logoPath = 'schools/seed-'.Str::slug($schoolData['name']).'-'.basename($schoolData['logo']);
                $disk->put($logoPath, File::get($logoSource));
            }

            $school = School::updateOrCreate(
                ['slug' => Str::slug($schoolData['name'])],
                [
                    'name' => $schoolData['name'],
                    'logo_path' => $logoPath,
                    'contact_email' => $schoolData['contact_email'],
                    'contact_phone' => $schoolData['contact_phone'],
                    'address' => $schoolData['address'],
                    'description' => $schoolData['description'],
                    'accent_colour' => $schoolData['accent_colour'],
                    'is_active' => true,
                    'sort_order' => $schoolIndex,
                ]
            );

            foreach ($schoolData['categories'] as $catIndex => $catData) {
                $category = SchoolProductCategory::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'slug' => Str::slug($catData['name']),
                    ],
                    [
                        'name' => $catData['name'],
                        'description' => $catData['description'] ?? null,
                        'is_active' => true,
                        'sort_order' => $catIndex,
                    ]
                );

                foreach ($catData['products'] as $prodIndex => $prodData) {
                    $product = SchoolProduct::updateOrCreate(
                        [
                            'school_id' => $school->id,
                            'slug' => Str::slug($prodData['name']),
                        ],
                        [
                            'school_product_category_id' => $category->id,
                            'name' => $prodData['name'],
                            'description' => $prodData['desc'] ?? null,
                            'is_active' => true,
                            'sort_order' => $prodIndex,
                        ]
                    );

                    // Replace variants so re-seeding stays clean
                    $product->variants()->delete();
                    foreach ($prodData['sizes'] as $sizeIndex => [$size, $price]) {
                        SchoolProductVariant::create([
                            'school_product_id' => $product->id,
                            'size' => $size,
                            'price' => $price,
                            'sort_order' => $sizeIndex,
                        ]);
                    }

                    $imageSource = $fixtures.DIRECTORY_SEPARATOR.$prodData['image'];
                    if (is_file($imageSource)) {
                        // Keep one primary image; remove old seed images for this product
                        foreach ($product->images as $old) {
                            $disk->delete($old->path);
                            $old->delete();
                        }

                        $dest = 'school-products/'.$product->id.'/'.basename($prodData['image']);
                        $disk->put($dest, File::get($imageSource));

                        // Repeat same image as second gallery shot (as requested)
                        $dest2 = 'school-products/'.$product->id.'/alt-'.basename($prodData['image']);
                        $disk->put($dest2, File::get($imageSource));

                        SchoolProductImage::create([
                            'school_product_id' => $product->id,
                            'path' => $dest,
                            'alt_text' => $prodData['name'],
                            'is_primary' => true,
                            'sort_order' => 0,
                        ]);
                        SchoolProductImage::create([
                            'school_product_id' => $product->id,
                            'path' => $dest2,
                            'alt_text' => $prodData['name'].' (alt)',
                            'is_primary' => false,
                            'sort_order' => 1,
                        ]);
                    }
                }
            }
        }

        $this->command?->info('Seeded 3 schools with categories, products, sizes and images.');
    }
}
