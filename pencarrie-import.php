<?php
// ============================================================
// Pencarrie Product Importer — run via cron nightly
// Cron example: 0 2 * * * php /path/to/threadsembroidery/pencarrie-import.php
//
// Requires .env in project root with:
//   DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
//   PENCARRIE_API_TOKEN=<your bearer token>
// ============================================================

/**
 * Read a value from the Laravel .env file (standalone script, no framework boot).
 */
function env_value(string $key, ?string $default = null): ?string
{
    static $env = null;

    if ($env === null) {
        $env = [];
        $path = __DIR__ . '/.env';

        if (is_readable($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                    continue;
                }

                [$name, $value] = explode('=', $line, 2);
                $value = trim($value);
                if (
                    (str_starts_with($value, '"') && str_ends_with($value, '"'))
                    || (str_starts_with($value, "'") && str_ends_with($value, "'"))
                ) {
                    $value = substr($value, 1, -1);
                }

                $env[trim($name)] = $value;
            }
        }
    }

    return $env[$key] ?? $default;
}

// ── Config ───────────────────────────────────────────────────
define('PC_TOKEN', env_value('PENCARRIE_API_TOKEN', ''));
define('DB_HOST', env_value('DB_HOST', '127.0.0.1'));
define('DB_NAME', env_value('DB_DATABASE', ''));
define('DB_USER', env_value('DB_USERNAME', ''));
define('DB_PASS', env_value('DB_PASSWORD', ''));
define('TMP_ZIP', sys_get_temp_dir() . '/pc_products.zip');
define('TMP_CSV', sys_get_temp_dir() . '/pc_products.csv');
define('BATCH_SIZE', 500); // rows per INSERT batch

if (PC_TOKEN === '') {
    die("Missing PENCARRIE_API_TOKEN in .env\n");
}

if (DB_NAME === '' || DB_USER === '') {
    die("Missing DB_DATABASE or DB_USERNAME in .env\n");
}

// ── Brand whitelist ──────────────────────────────────────────
// Only products from these brands will be imported. Matching is
// case-insensitive and ignores punctuation/extra spaces.
// "JUST_PREFIX" handles all Just Brands (Just Hoods, Just Cool, etc.)
// "HAT_KEYWORDS" lets ANY brand's hat/cap/headwear items through,
// per "All brands and items of hats" requirement.

const JUST_PREFIX = 'just';

$ALLOWED_BRANDS = [
    'awdis',
    'findan hales',          // Findan + Hales
    'fruit of the loom',
    'gildan',
    'anthem',
    'native spirit',
    'sols',                  // Sol's
    'kustom kit',
    'henbury',
    'regatta',                // All Regatta items
    'bagbase',                // Bag Base
    'rtx',                     // RTX Pro
    'rtx pro',
    'result',                  // All Result
    'stormtech',
    'spiro',
    'russell',
    'dennys',                 // Dennys
    'premier',                 // Premier all products
    'tee jays',
    'teejays',
    'westford mill',
    'helly hansen',
    'nike',
];

$HAT_KEYWORDS = [
    'hat', 'hats', 'cap', 'caps', 'beanie', 'beanies',
    'bucket hat', 'headwear', 'visor', 'trucker cap', 'snapback',
];

/**
 * Normalize a brand/type string for comparison:
 * lowercase, strip punctuation (apostrophes, plus signs, ampersands,
 * hyphens), collapse whitespace.
 */
function normalize_str(string $str): string
{
    $str = strtolower($str);
    $str = str_replace(["'", '’', '+', '&', '-', '.'], ' ', $str);
    $str = preg_replace('/\s+/', ' ', $str);

    return trim($str);
}

/**
 * Whether a product's brand is in the allowed whitelist
 * (including the "Just" brand family).
 */
function is_allowed_brand(string $brand, array $allowedBrands): bool
{
    $norm = normalize_str($brand);
    if ($norm === '') {
        return false;
    }

    if (strpos($norm, JUST_PREFIX) === 0) {
        return true; // Just Hoods, Just Cool, etc.
    }

    foreach ($allowedBrands as $allowed) {
        if ($norm === $allowed || strpos($norm, $allowed) === 0) {
            return true;
        }
    }

    return false;
}

/**
 * Whether a product looks like a hat/headwear item, regardless of
 * brand — checked across Type, Categories, Collections and Title.
 */
function is_hat_item(array $row, array $col, array $hatKeywords): bool
{
    $haystack = normalize_str(
        get_col($row, $col, 'Type') . ' ' .
        get_col($row, $col, 'Suggested Categories') . ' ' .
        get_col($row, $col, 'Suggested Collections') . ' ' .
        get_col($row, $col, 'Title')
    );
    foreach ($hatKeywords as $kw) {
        if (strpos($haystack, normalize_str($kw)) !== false) {
            return true;
        }
    }

    return false;
}

// ── DB connect ───────────────────────────────────────────────
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('DB connection failed: ' . $e->getMessage() . "\n");
}

log_msg('=== Pencarrie Product Import started ===');

// ── Step 1: Download ZIP ─────────────────────────────────────
log_msg('Downloading product ZIP...');
$fp = fopen(TMP_ZIP, 'wb');
$ch = curl_init('https://www.pencarrie.com/api/public/v1/export/products.zip');
curl_setopt_array($ch, [
    CURLOPT_FILE => $fp,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . PC_TOKEN,
        'Accept: application/octet-stream',
        'Accept-Encoding: gzip, deflate, br',
    ],
    CURLOPT_USERAGENT => 'curl/7.88.1',   // match CLI curl UA exactly
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 300,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_ENCODING => '',               // accept compressed responses
]);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);
fclose($fp);

if ($curlErr || $httpCode !== 200) {
    log_error("Download failed. HTTP $httpCode. $curlErr");
    exit(1);
}
log_msg('Downloaded ' . round(filesize(TMP_ZIP) / 1024, 1) . ' KB');

// ── Step 2: Extract CSV ──────────────────────────────────────
log_msg('Extracting CSV...');
$zip = new ZipArchive();
if ($zip->open(TMP_ZIP) !== true) {
    log_error('Failed to open ZIP');
    exit(1);
}
// Extract the first CSV found
for ($i = 0; $i < $zip->numFiles; $i++) {
    $info = $zip->statIndex($i);
    if (pathinfo($info['name'], PATHINFO_EXTENSION) === 'csv') {
        $zip->extractTo(sys_get_temp_dir(), $info['name']);
        rename(sys_get_temp_dir() . '/' . $info['name'], TMP_CSV);
        break;
    }
}
$zip->close();
unlink(TMP_ZIP);

if (! file_exists(TMP_CSV)) {
    log_error('CSV not found after extraction');
    exit(1);
}
log_msg('CSV extracted (' . round(filesize(TMP_CSV) / 1024 / 1024, 1) . ' MB)');

// ── Step 3: Parse & import ───────────────────────────────────
log_msg('Importing into database...');

$handle = fopen(TMP_CSV, 'r');
if (! $handle) {
    log_error('Cannot open CSV');
    exit(1);
}

// Read header row, strip BOM
$header = fgetcsv($handle);
$header[0] = ltrim($header[0], "\xEF\xBB\xBF"); // strip UTF-8 BOM
$header = array_map('trim', $header);

// Map column names to indices
$col = array_flip($header);

$batch = [];
$totalRows = 0;
$insertedRows = 0;
$skippedBrand = 0;

// Prepare upsert SQL
$sql = 'INSERT INTO pc_products (
    sku, style_code, title, body, brand, gender, type,
    categories, collections, attributes, colourway_code,
    colourway_name, colour_group, material, weight, rgb,
    image_model, image_front, image_back, image_side, image_detail,
    size, size_conversions,
    price_single, price_pack, pack_qty, price_carton, carton_qty,
    vat_rate, grams, is_clearance, is_discontinued, is_new, is_special_order
) VALUES %s
ON DUPLICATE KEY UPDATE
    title          = VALUES(title),
    body           = VALUES(body),
    brand          = VALUES(brand),
    type           = VALUES(type),
    categories     = VALUES(categories),
    colourway_name = VALUES(colourway_name),
    colour_group   = VALUES(colour_group),
    material       = VALUES(material),
    rgb            = VALUES(rgb),
    image_model    = VALUES(image_model),
    image_front    = VALUES(image_front),
    image_back     = VALUES(image_back),
    price_single   = VALUES(price_single),
    price_pack     = VALUES(price_pack),
    pack_qty       = VALUES(pack_qty),
    price_carton   = VALUES(price_carton),
    carton_qty     = VALUES(carton_qty),
    is_clearance   = VALUES(is_clearance),
    is_discontinued= VALUES(is_discontinued),
    is_new         = VALUES(is_new),
    updated_at     = NOW()';

function get_col($row, $col, $key, $default = '')
{
    return isset($col[$key], $row[$col[$key]]) ? trim($row[$col[$key]]) : $default;
}

while (($row = fgetcsv($handle)) !== false) {
    $totalRows++;

    $sku = get_col($row, $col, 'SKU');
    if (empty($sku)) {
        continue;
    }

    $brand = get_col($row, $col, 'Brand');

    // ── Brand whitelist filter ──────────────────────────────
    // Skip the row unless its brand is on the allowed list, OR
    // it's a hat/headwear item (any brand allowed for those).
    if (! is_allowed_brand($brand, $ALLOWED_BRANDS) && ! is_hat_item($row, $col, $HAT_KEYWORDS)) {
        $skippedBrand++;

        continue;
    }

    $batch[] = [
        'sku' => $sku,
        'style_code' => get_col($row, $col, 'Style Code'),
        'title' => get_col($row, $col, 'Title'),
        'body' => get_col($row, $col, 'Body'),
        'brand' => $brand,
        'gender' => get_col($row, $col, 'Gender'),
        'type' => get_col($row, $col, 'Type'),
        'categories' => get_col($row, $col, 'Suggested Categories'),
        'collections' => get_col($row, $col, 'Suggested Collections'),
        'attributes' => get_col($row, $col, 'Attributes'),
        'colourway_code' => get_col($row, $col, 'Colourway Code'),
        'colourway_name' => get_col($row, $col, 'Colourway Name'),
        'colour_group' => get_col($row, $col, 'Colour Group'),
        'material' => get_col($row, $col, 'Material'),
        'weight' => get_col($row, $col, 'Weight'),
        'rgb' => get_col($row, $col, 'RGB'),
        'image_model' => get_col($row, $col, 'Model Image'),
        'image_front' => get_col($row, $col, 'Front Image'),
        'image_back' => get_col($row, $col, 'Back Image'),
        'image_side' => get_col($row, $col, 'Side Image'),
        'image_detail' => get_col($row, $col, 'Detail Image'),
        'size' => get_col($row, $col, 'Size'),
        'size_conversions' => get_col($row, $col, 'Size Conversions'),
        'price_single' => (float) get_col($row, $col, 'Single List Price', 0),
        'price_pack' => (float) get_col($row, $col, 'Pack List Price', 0),
        'pack_qty' => (int) get_col($row, $col, 'Pack Quantity', 0),
        'price_carton' => (float) get_col($row, $col, 'Carton List Price', 0),
        'carton_qty' => (int) get_col($row, $col, 'Carton Quantity', 0),
        'vat_rate' => (int) get_col($row, $col, 'VAT Rate', 20),
        'grams' => (int) get_col($row, $col, 'Grams', 0),
        'is_clearance' => get_col($row, $col, 'Clearance') === 'TRUE' ? 1 : 0,
        'is_discontinued' => get_col($row, $col, 'Discontinued') === 'TRUE' ? 1 : 0,
        'is_new' => get_col($row, $col, 'New') === 'TRUE' ? 1 : 0,
        'is_special_order' => get_col($row, $col, 'Special Order') === 'TRUE' ? 1 : 0,
    ];

    // Flush batch
    if (count($batch) >= BATCH_SIZE) {
        $insertedRows += flush_batch($pdo, $sql, $batch);
        $batch = [];
        log_msg("  Processed $totalRows rows (kept $insertedRows, skipped $skippedBrand by brand filter)...");
    }
}

// Final batch
if (! empty($batch)) {
    $insertedRows += flush_batch($pdo, $sql, $batch);
}

fclose($handle);
unlink(TMP_CSV);

// Log success
$pdo->prepare("INSERT INTO pc_import_log (type, status, rows, message) VALUES ('products','success',?,?)")
    ->execute([$insertedRows, "Processed $totalRows rows, upserted $insertedRows, skipped $skippedBrand (brand filter)"]);

log_msg("✓ Done. Processed $totalRows rows, upserted $insertedRows, skipped $skippedBrand (not in brand whitelist / not hats).");
log_msg('=== Import complete ===');

// ── Helpers ──────────────────────────────────────────────────
function flush_batch(PDO $pdo, string $sql, array $batch): int
{
    $placeholders = [];
    $values = [];
    foreach ($batch as $row) {
        $placeholders[] = '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        array_push($values, ...array_values($row));
    }
    $stmt = $pdo->prepare(sprintf($sql, implode(',', $placeholders)));
    $stmt->execute($values);

    return $stmt->rowCount();
}

function log_msg(string $msg): void
{
    echo '[' . date('Y-m-d H:i:s') . "] $msg\n";
}

function log_error(string $msg): void
{
    echo '[' . date('Y-m-d H:i:s') . "] ERROR: $msg\n";
}
