<?php
/**
 * PWA Icon Generator
 * 
 * Generates icon files for Progressive Web App installation
 * Uses simple PNG generation without GD requirement
 * Run from command line: php scripts/generate_pwa_icons.php
 */

// Ensure output directory exists
$iconDir = __DIR__ . '/../public/icons';
if (!is_dir($iconDir)) {
    mkdir($iconDir, 0755, true);
}

// Icon configurations with sizes
$configs = [
    ['size' => 192, 'type' => 'any', 'name' => 'icon-192x192.png'],
    ['size' => 192, 'type' => 'maskable', 'name' => 'icon-192x192-maskable.png'],
    ['size' => 512, 'type' => 'any', 'name' => 'icon-512x512.png'],
    ['size' => 512, 'type' => 'maskable', 'name' => 'icon-512x512-maskable.png'],
];

// Generate all icons
foreach ($configs as $config) {
    $filepath = "$iconDir/{$config['name']}";
    $pngData = generateSimplePNG($config['size'], $config['type']);
    file_put_contents($filepath, $pngData);
    echo "✓ Generated: {$config['name']} ({$config['size']}x{$config['size']})\n";
}

// Generate screenshots
$screenshots = [
    ['size' => [1280, 720], 'name' => 'screenshot-wide.png'],
    ['size' => [540, 720], 'name' => 'screenshot-narrow.png'],
];

foreach ($screenshots as $config) {
    [$width, $height] = $config['size'];
    $filepath = "$iconDir/{$config['name']}";
    $pngData = generateScreenshotPNG($width, $height);
    file_put_contents($filepath, $pngData);
    echo "✓ Generated: {$config['name']} ({$width}x{$height})\n";
}

echo "\n✓ All PWA icons generated successfully!\n";
echo "  Location: $iconDir\n";

/**
 * Generate a simple solid color PNG without GD
 * Using PNG format directly with minimal structure
 */
function generateSimplePNG($size, $type = 'any') {
    // PNG signature
    $png = "\x89PNG\r\n\x1a\n";
    
    // IHDR chunk (image header)
    $width = pack('N', $size);
    $height = pack('N', $size);
    $bitDepth = "\x08";           // 8-bit
    $colorType = "\x02";           // RGB
    $compression = "\x00";         // deflate
    $filter = "\x00";              // adaptive
    $interlace = "\x00";           // no interlace
    
    $ihdr_data = $width . $height . $bitDepth . $colorType . $compression . $filter . $interlace;
    $ihdr = createChunk('IHDR', $ihdr_data);
    
    // Create image data
    $pixels = createPixelData($size, $type);
    
    // IDAT chunk (image data)
    $compressed = gzcompress($pixels);
    $idat = createChunk('IDAT', $compressed);
    
    // IEND chunk (image end)
    $iend = createChunk('IEND', '');
    
    return $png . $ihdr . $idat . $iend;
}

/**
 * Generate screenshot PNG
 */
function generateScreenshotPNG($width, $height) {
    // PNG signature
    $png = "\x89PNG\r\n\x1a\n";
    
    // IHDR chunk
    $ihdr_data = pack('N', $width) .
                 pack('N', $height) .
                 "\x08" .           // bit depth (8)
                 "\x02" .           // color type (RGB)
                 "\x00" .           // compression
                 "\x00" .           // filter
                 "\x00";            // interlace
    
    $ihdr = createChunk('IHDR', $ihdr_data);
    
    // Create image data with header and content areas
    $pixels = createScreenshotPixels($width, $height);
    
    // IDAT chunk
    $compressed = gzcompress($pixels);
    $idat = createChunk('IDAT', $compressed);
    
    // IEND chunk
    $iend = createChunk('IEND', '');
    
    return $png . $ihdr . $idat . $iend;
}

/**
 * Create PNG chunk
 */
function createChunk($type, $data) {
    $length = pack('N', strlen($data));
    $chunkData = $type . $data;
    $crc = pack('N', crc32($chunkData) & 0xffffffff);
    return $length . $chunkData . $crc;
}

/**
 * Create pixel data for icon
 */
function createPixelData($size, $type) {
    // Colors: Primary (#1f2937), Accent (#3b82f6)
    $primary = [31, 41, 55];      // Dark gray
    $accent = [59, 130, 246];     // Blue
    $white = [255, 255, 255];     // White
    
    $pixels = '';
    
    for ($y = 0; $y < $size; $y++) {
        $pixels .= "\x00"; // Filter byte (none)
        
        for ($x = 0; $x < $size; $x++) {
            if ($type === 'maskable') {
                // Circular design for maskable icons
                $centerX = $size / 2;
                $centerY = $size / 2;
                $padding = $size * 0.2;
                $radius = ($size - (2 * $padding)) / 2;
                $dist = sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2));
                
                if ($dist < $radius * 0.5) {
                    $pixels .= packRGB($white);
                } elseif ($dist < $radius) {
                    $pixels .= packRGB($primary);
                } else {
                    $pixels .= packRGB($white);
                }
            } else {
                // Solid background with corner highlight
                if ($x < $size * 0.3 && $y < $size * 0.3) {
                    $pixels .= packRGB($accent);
                } else {
                    $pixels .= packRGB($primary);
                }
            }
        }
    }
    
    return $pixels;
}

/**
 * Create screenshot pixel data
 */
function createScreenshotPixels($width, $height) {
    $primary = [31, 41, 55];       // Dark gray
    $accent = [59, 130, 246];      // Blue
    $white = [255, 255, 255];      // White
    $lightGray = [241, 245, 249];  // Light gray
    
    $pixels = '';
    
    for ($y = 0; $y < $height; $y++) {
        $pixels .= "\x00"; // Filter byte
        
        for ($x = 0; $x < $width; $x++) {
            // Header bar (15% of height)
            if ($y < $height * 0.15) {
                $pixels .= packRGB($primary);
            }
            // Content area with placeholder boxes
            elseif ($y > $height * 0.2) {
                // Draw placeholder content boxes
                $boxHeight = $height * 0.12;
                $boxSpacing = $height * 0.02;
                $startY = $height * 0.2;
                $boxNum = floor(($y - $startY) / ($boxHeight + $boxSpacing));
                $yInBox = ($y - $startY) % ($boxHeight + $boxSpacing);
                
                if ($yInBox < $boxHeight && $boxNum < 3) {
                    if ($x < $width * 0.05 || $x > $width * 0.95) {
                        $pixels .= packRGB($white);
                    } elseif ($x < $width * 0.08) {
                        $pixels .= packRGB($accent);
                    } else {
                        $pixels .= packRGB($lightGray);
                    }
                } else {
                    $pixels .= packRGB($white);
                }
            } else {
                $pixels .= packRGB($white);
            }
        }
    }
    
    return $pixels;
}

/**
 * Pack RGB values into 3 bytes
 */
function packRGB($rgb) {
    return chr($rgb[0]) . chr($rgb[1]) . chr($rgb[2]);
}
?>

?>
