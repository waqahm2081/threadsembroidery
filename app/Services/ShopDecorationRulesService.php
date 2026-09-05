<?php

namespace App\Services;

/**
 * Category / product decoration rules for the shop customizer.
 *
 * Resolves available methods (print / embroidery), placement options,
 * print-area keys, and whether the placement modal can be skipped.
 */
class ShopDecorationRulesService
{
    /**
     * @param  array<string, mixed>  $product  Catalog product detail
     * @return array{
     *     group: string,
     *     methods: list<string>,
     *     logo_setup_available: bool,
     *     skip_modal: bool,
     *     auto_placement: ?string,
     *     placements: list<array<string, mixed>>,
     *     display_decoration_fee: float
     * }
     */
    public static function forProduct(array $product, string $categoryName): array
    {
        $group = self::detectGroup($product, $categoryName);
        $catalog = self::placementCatalog();

        return match ($group) {
            'tshirt', 'polo', 'hoodie', 'hi_vis' => self::build(
                $group,
                ['print', 'embroidery'],
                ['left_chest', 'back_print', 'back_embroidery'],
                $catalog
            ),
            'fleece' => self::build(
                $group,
                ['embroidery'],
                ['left_chest', 'back_embroidery'],
                $catalog
            ),
            'bodywarmer_coat' => self::build(
                $group,
                ['print', 'embroidery'],
                ['front_print', 'front_embroidery'],
                $catalog
            ),
            'apron_bib' => self::build(
                $group,
                ['print', 'embroidery'],
                ['apron_middle'],
                $catalog
            ),
            'apron_short' => self::build(
                $group,
                ['print', 'embroidery'],
                ['apron_left', 'apron_right'],
                $catalog
            ),
            'rucksack' => self::build(
                $group,
                ['print', 'embroidery'],
                ['rucksack_top_print', 'rucksack_top_embroidery'],
                $catalog
            ),
            'bag' => self::build(
                $group,
                ['print'],
                ['bag_print'],
                $catalog,
                skipModal: true,
                autoPlacement: 'bag_print'
            ),
            'keyring' => self::build(
                $group,
                ['print'],
                ['keyring_print'],
                $catalog,
                skipModal: true,
                autoPlacement: 'keyring_print'
            ),
            'towel' => self::build(
                $group,
                ['embroidery'],
                ['towel_embroidery'],
                $catalog,
                skipModal: true,
                autoPlacement: 'towel_embroidery'
            ),
            'bottom' => self::build(
                $group,
                ['print'],
                ['full_print'],
                $catalog,
                skipModal: true,
                autoPlacement: 'full_print'
            ),
            default => self::build(
                'default',
                ['print'],
                ['full_print'],
                $catalog,
                skipModal: true,
                autoPlacement: 'full_print'
            ),
        };
    }

    /**
     * @param  array<string, mixed>  $product
     */
    public static function detectGroup(array $product, string $categoryName): string
    {
        $title = (string) ($product['title'] ?? '');
        $type = (string) ($product['type'] ?? '');
        $categories = $product['categories'] ?? [];
        if (! is_array($categories)) {
            $categories = [];
        }

        $hay = self::haystack([$categoryName, $type, $title, ...$categories]);

        // Most specific first
        if (self::matches($hay, ['keyring', 'key ring', 'key-ring'])) {
            return 'keyring';
        }

        if (self::matches($hay, ['rucksack', 'backpack', 'back pack'])) {
            return 'rucksack';
        }

        if (self::matches($hay, ['bag', 'tote', 'shopper', 'holdall', 'duffel', 'duffle', 'laptop bag'])) {
            return 'bag';
        }

        if (self::matches($hay, ['towel'])) {
            return 'towel';
        }

        if (self::matches($hay, ['apron'])) {
            // Bib aprons: "Bib" appears in the product name
            if (preg_match('/\bbib\b/i', $title)) {
                return 'apron_bib';
            }

            return 'apron_short';
        }

        // Bottoms — avoid matching "short sleeve" tees
        if (preg_match('/\b(trousers?|joggers?|leggings?|chinos?|skirts?|pants?)\b/i', $hay)) {
            return 'bottom';
        }
        if (preg_match('/\bshorts?\b/i', $hay) && ! preg_match('/\b(t-?shirts?|tees?|sleeve|polo|hoodie|sweat)\b/i', $hay)) {
            return 'bottom';
        }

        // Hi-vis before coats/jackets so hi-vis jackets keep emb+print placements
        if (self::matches($hay, ['hi-vis', 'hi vis', 'hivis', 'high visibility', 'high-vis'])) {
            return 'hi_vis';
        }

        // Bodywarmers & coats — allow front print even on fleece fabric (rule 5B)
        if (self::matches($hay, [
            'bodywarmer', 'body warmer', 'gilet', 'coat', 'jacket', 'parka', 'softshell',
        ])) {
            return 'bodywarmer_coat';
        }

        // Fleece category / type only (not bodywarmers) → embroidery only
        if (self::matches($hay, ['fleece'])) {
            return 'fleece';
        }

        if (self::matches($hay, ['hoodie', 'sweatshirt', 'sweat shirt', 'midlayer', 'mid layer', 'zip hoodie'])) {
            return 'hoodie';
        }

        if (self::matches($hay, ['polo'])) {
            return 'polo';
        }

        if (preg_match('/\b(t-?shirts?|tees?)\b/i', $hay) || str_contains($hay, 't shirt')) {
            return 'tshirt';
        }

        // Soft match on category name alone for tops
        $cat = strtolower($categoryName);
        if (str_contains($cat, 't-shirt') || str_contains($cat, 'tshirt')) {
            return 'tshirt';
        }
        if (str_contains($cat, 'polo')) {
            return 'polo';
        }
        if (str_contains($cat, 'hoodie') || str_contains($cat, 'sweat')) {
            return 'hoodie';
        }

        return 'default';
    }

    /**
     * Master placement definitions (keys must match design-print-area.js + ShopPricingService).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function placementCatalog(): array
    {
        $fees = ShopPricingService::PLACEMENT_FEES;

        return [
            'left_chest' => [
                'key' => 'left_chest',
                'label' => 'Left Chest',
                'desc' => 'Classic logo position on the front',
                'icon' => 'heart',
                'methods' => ['print', 'embroidery'],
                'fee' => $fees['left_chest'] ?? 3.50,
            ],
            'right_chest' => [
                'key' => 'right_chest',
                'label' => 'Right Chest',
                'desc' => 'Logo position on the wearer’s right',
                'icon' => 'heart',
                'methods' => ['print', 'embroidery'],
                'fee' => $fees['right_chest'] ?? 3.50,
            ],
            'front_print' => [
                'key' => 'front_print',
                'label' => 'Front Print',
                'desc' => 'Print on the front of the garment',
                'icon' => 'printer',
                'methods' => ['print'],
                'fee' => $fees['front_print'] ?? 3.50,
            ],
            'front_embroidery' => [
                'key' => 'front_embroidery',
                'label' => 'Front Embroidery',
                'desc' => 'Embroidery on the front of the garment',
                'icon' => 'magic',
                'methods' => ['embroidery'],
                'fee' => $fees['front_embroidery'] ?? 3.50,
            ],
            'back_print' => [
                'key' => 'back_print',
                'label' => 'Back Print',
                'desc' => 'Full back print area',
                'icon' => 'printer',
                'methods' => ['print'],
                'fee' => $fees['back_print'] ?? 6.00,
            ],
            'back_embroidery' => [
                'key' => 'back_embroidery',
                'label' => 'Back Embroidery',
                'desc' => 'Full back embroidery area',
                'icon' => 'magic',
                'methods' => ['embroidery'],
                'fee' => $fees['back_embroidery'] ?? 8.00,
            ],
            'apron_left' => [
                'key' => 'apron_left',
                'label' => 'Left Side',
                'desc' => 'Print or embroidery on the left of short aprons',
                'icon' => 'layout-sidebar',
                'methods' => ['print', 'embroidery'],
                'fee' => $fees['apron_left'] ?? 3.50,
            ],
            'apron_right' => [
                'key' => 'apron_right',
                'label' => 'Right Side',
                'desc' => 'Print or embroidery on the right of short aprons',
                'icon' => 'layout-sidebar-reverse',
                'methods' => ['print', 'embroidery'],
                'fee' => $fees['apron_right'] ?? 3.50,
            ],
            'apron_middle' => [
                'key' => 'apron_middle',
                'label' => 'Centre',
                'desc' => 'Centre placement for bib aprons',
                'icon' => 'square',
                'methods' => ['print', 'embroidery'],
                'fee' => $fees['apron_middle'] ?? 3.50,
            ],
            'rucksack_top_print' => [
                'key' => 'rucksack_top_print',
                'label' => 'Top Print',
                'desc' => 'Print on the top/front of the rucksack',
                'icon' => 'printer',
                'methods' => ['print'],
                'fee' => $fees['rucksack_top_print'] ?? 6.00,
            ],
            'rucksack_top_embroidery' => [
                'key' => 'rucksack_top_embroidery',
                'label' => 'Top Embroidery',
                'desc' => 'Embroidery on the top of the rucksack',
                'icon' => 'magic',
                'methods' => ['embroidery'],
                'fee' => $fees['rucksack_top_embroidery'] ?? 3.50,
            ],
            'bag_print' => [
                'key' => 'bag_print',
                'label' => 'Bag Print',
                'desc' => 'Print across the bag',
                'icon' => 'printer',
                'methods' => ['print'],
                'fee' => $fees['bag_print'] ?? 6.00,
            ],
            'keyring_print' => [
                'key' => 'keyring_print',
                'label' => 'Keyring Print',
                'desc' => 'Print on the keyring',
                'icon' => 'printer',
                'methods' => ['print'],
                'fee' => $fees['keyring_print'] ?? 6.00,
            ],
            'towel_embroidery' => [
                'key' => 'towel_embroidery',
                'label' => 'Towel Embroidery',
                'desc' => 'Embroidery placement on the towel',
                'icon' => 'magic',
                'methods' => ['embroidery'],
                'fee' => $fees['towel_embroidery'] ?? 3.50,
            ],
            'full_print' => [
                'key' => 'full_print',
                'label' => 'Full Product Print',
                'desc' => 'Entire product is the printable area',
                'icon' => 'printer',
                'methods' => ['print'],
                'fee' => $fees['full_print'] ?? 6.00,
            ],
        ];
    }

    /**
     * @param  list<string>  $methods
     * @param  list<string>  $placementKeys
     * @param  array<string, array<string, mixed>>  $catalog
     * @return array<string, mixed>
     */
    private static function build(
        string $group,
        array $methods,
        array $placementKeys,
        array $catalog,
        bool $skipModal = false,
        ?string $autoPlacement = null,
    ): array {
        $placements = [];
        foreach ($placementKeys as $key) {
            if (isset($catalog[$key])) {
                $placements[] = $catalog[$key];
            }
        }

        $displayFee = 0.0;
        if ($placements !== []) {
            $displayFee = (float) ($placements[0]['fee'] ?? 0);
        }

        return [
            'group' => $group,
            'methods' => $methods,
            'logo_setup_available' => in_array('embroidery', $methods, true),
            'skip_modal' => $skipModal,
            'auto_placement' => $autoPlacement,
            'placements' => $placements,
            'display_decoration_fee' => $displayFee,
        ];
    }

    /**
     * @param  list<string>  $parts
     */
    private static function haystack(array $parts): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', implode(' ', array_filter($parts))) ?? ''));
    }

    /**
     * @param  list<string>  $needles
     */
    private static function matches(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($haystack, strtolower($needle))) {
                return true;
            }
        }

        return false;
    }
}
