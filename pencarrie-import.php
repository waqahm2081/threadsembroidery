<?php
// ============================================================
// Pencarrie Product Importer — run via cron nightly
// Cron example: 0 2 * * * php /path/to/threadsembroidery/pencarrie-import.php
//
// Requires .env in project root with:
//   DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
//   PENCARRIE_API_TOKEN=<your bearer token>
//
// Only imports the curated style-code whitelist below, then
// removes any pc_products rows whose style_code is not listed.
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

// ── Style-code whitelist ─────────────────────────────────────
// Only these styles are imported. Matching is case-insensitive.
// Gender notes in the brief are decoration guidance — Pencarrie
// usually stores gender as a separate column on the same style.

$ALLOWED_STYLE_CODES = [
    // T-shirts
    'GD05', 'GD01', 'AT002', 'JC001', 'JC003', 'AM15', 'AT003', 'AT004', '03806', 'RX151',

    // Hoodies, zip hoodies, midlayer, fleece, sweatshirt
    '03568', '03812', '575M', '575B', 'AM101', 'AM02', 'GD57', 'GD57B',
    'JC057', 'JC058', 'JC305', 'JH001', 'JH006', 'JH050', 'JH120', 'JH125', 'JH305',
    'K304', 'LV341', 'NS460', 'RX351', '55000', 'BK480', 'BK481',
    'RX301', 'RX305', 'RX350', '02088', '03814', 'AM20', 'GD612', 'GD67', 'H842',
    'JH030', 'JH037', 'JH043', 'JH043B', 'JH046', 'JH123', 'K302', 'K335', 'NS432',

    // Bodywarmers & coats
    '02887', '02888', 'BK525', 'BK482', 'BK528', 'BK529', 'BK557', 'CR331',
    'RG115', 'RG116', 'RG154', 'RG155', 'RG447', 'RG652',
    '04444', '04445', '04758', 'AFP1', 'AFP1W', 'HXP1', 'HXP1W',
    'RG045', 'RG085', 'RG086', 'RG443', 'RG150', 'RG151',
    'RX550', 'RX500', 'RX500F', 'RX551',

    // Hi-vis
    'PW1250', 'RG564', 'RS503', 'RX740', 'PW002', 'PW055', 'PW003', 'WR002',
    'PW061', 'PW059', 'PW309', 'WR010', 'YK206', 'RX760', 'RG475', 'RS200E',
    'PW450', 'YK302', 'RX710',

    // Hats
    'F9297', 'BB20', 'BB188', 'BB183', 'BB10', 'BB67', 'BB655', 'BB654', 'BB651',
    'BB15', 'F6506', 'BB640', 'BB640B', 'BB52N', 'BB460', 'BB390', 'RX290',
    'BB383R', 'RG661', 'BB443', 'BB445', 'BB426', 'BB440',

    // Bags & keyrings
    '04089', '70100', '70600', 'BG100', 'BG10', 'BG118', 'BG125', 'BG144',
    'BG25', 'BG324', 'BG723', 'W600', 'W610', 'W201', 'W406', 'W696',
    'BR003', 'W901', 'W261',

    // Polos
    'RX101', 'RX101F', 'RX102', 'RX105', 'RX105F',
    '01180', '01179', '03566', '03575', '04442',
    '539F', '539M', '539B',
    'K400', 'K408', 'K412', 'K444', 'K430', 'K703', 'NS207', 'NS208',

    // Towels
    'PA574', 'TC13', 'TC34', 'W710',

    // Aprons
    '88019', 'DE111', 'LE100', 'PR023', 'PRE102', 'PR116', 'PR115',
    'PR141', 'PR149', 'PR155', 'PR172', 'PR181',
];

/** @var array<string, true> Lookup map of uppercase style codes */
$ALLOWED_STYLE_LOOKUP = [];
foreach ($ALLOWED_STYLE_CODES as $code) {
    $ALLOWED_STYLE_LOOKUP[strtoupper(trim($code))] = true;
}

/**
 * Whether a CSV style code is in the curated allowlist.
 */
function is_allowed_style_code(string $styleCode, array $lookup): bool
{
    $norm = strtoupper(trim($styleCode));

    return $norm !== '' && isset($lookup[$norm]);
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
log_msg('Allowlist: ' . count($ALLOWED_STYLE_CODES) . ' style codes');

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
$skippedStyle = 0;
$matchedStyles = [];

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

    $styleCode = get_col($row, $col, 'Style Code');

    // ── Style-code whitelist filter ─────────────────────────
    if (! is_allowed_style_code($styleCode, $ALLOWED_STYLE_LOOKUP)) {
        $skippedStyle++;

        continue;
    }

    $matchedStyles[strtoupper($styleCode)] = true;

    $batch[] = [
        'sku' => $sku,
        'style_code' => $styleCode,
        'title' => get_col($row, $col, 'Title'),
        'body' => get_col($row, $col, 'Body'),
        'brand' => get_col($row, $col, 'Brand'),
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
        log_msg("  Processed $totalRows rows (kept $insertedRows, skipped $skippedStyle by style filter)...");
    }
}

// Final batch
if (! empty($batch)) {
    $insertedRows += flush_batch($pdo, $sql, $batch);
}

fclose($handle);
unlink(TMP_CSV);

// ── Step 4: Remove styles not on the allowlist ───────────────
log_msg('Removing products not on the style-code allowlist...');
$placeholders = implode(',', array_fill(0, count($ALLOWED_STYLE_CODES), '?'));
$deleteStmt = $pdo->prepare(
    "DELETE FROM pc_products WHERE UPPER(TRIM(style_code)) NOT IN ($placeholders)"
);
$deleteStmt->execute(array_map('strtoupper', $ALLOWED_STYLE_CODES));
$deletedRows = $deleteStmt->rowCount();
log_msg("Removed $deletedRows SKU rows outside the allowlist");

// Report any allowlisted styles that were not found in the feed
$missingStyles = array_values(array_filter(
    array_map('strtoupper', $ALLOWED_STYLE_CODES),
    static fn (string $code) => ! isset($matchedStyles[$code])
));
sort($missingStyles);
if ($missingStyles) {
    log_msg('⚠ Style codes on allowlist but not found in feed (' . count($missingStyles) . '): ' . implode(', ', $missingStyles));
} else {
    log_msg('All allowlisted style codes were present in the feed.');
}

// Clear Laravel category cache (database + file drivers)
try {
    $pdo->exec("DELETE FROM cache WHERE `key` LIKE '%pc_category_index%'");
} catch (Throwable $e) {
    // ignore — cache driver may not be database
}

// Also wipe file-cache entries that contain the category index key
$cacheRoot = __DIR__ . '/storage/framework/cache/data';
if (is_dir($cacheRoot)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheRoot, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if (! $file->isFile()) {
            continue;
        }
        $contents = @file_get_contents($file->getPathname());
        if (is_string($contents) && str_contains($contents, 'pc_category_index')) {
            @unlink($file->getPathname());
        }
    }
    log_msg('Cleared Laravel file cache entries for pc_category_index');
}

// Log success
$message = "Processed $totalRows rows, upserted $insertedRows, skipped $skippedStyle (style filter), deleted $deletedRows outside allowlist";
$pdo->prepare("INSERT INTO pc_import_log (type, status, rows, message) VALUES ('products','success',?,?)")
    ->execute([$insertedRows, $message]);

log_msg("✓ Done. $message");
log_msg('Matched ' . count($matchedStyles) . ' distinct style codes from allowlist of ' . count($ALLOWED_STYLE_CODES));
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
