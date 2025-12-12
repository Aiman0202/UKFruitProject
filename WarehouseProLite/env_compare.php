<?php
/**
 * Barcode/QR Code Environment Diagnostic Script
 * 
 * INSTRUCTIONS:
 * 1. Place this file in your WarehouseProLite folder
 * 2. Open in browser: http://localhost/WarehouseProLite/env_compare.php
 * 3. Compare results between working and non-working computers
 * 
 * Look for differences in:
 * - PHP versions
 * - GD extension capabilities 
 * - Missing composer packages
 * - Failed test generations
 */

?>
<!DOCTYPE html>
<html>
<head>
    <title>Barcode Environment Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { color: #22a122; font-weight: bold; }
        .error { color: #d93025; font-weight: bold; }
        .warning { color: #f57c00; font-weight: bold; }
        .info { background: #e3f2fd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .section { margin: 20px 0; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .test-success { background: #d4edda; border: 1px solid #c3e6cb; }
        .test-error { background: #f8d7da; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<h1>🔍 Barcode/QR Code Environment Diagnostic</h1>

<div class="info">
    <strong>Purpose:</strong> This script helps identify why barcodes/QR codes work on one computer but not another.
    <br><strong>Date:</strong> <?php echo date('Y-m-d H:i:s'); ?>
    <br><strong>Computer:</strong> <?php echo gethostname(); ?>
</div>

<div class="section">
    <h2>1. 🖥️ PHP Environment</h2>
    <table>
        <tr><th>Setting</th><th>Value</th></tr>
        <tr><td>PHP Version</td><td><?php echo phpversion(); ?></td></tr>
        <tr><td>Operating System</td><td><?php echo php_uname(); ?></td></tr>
        <tr><td>Web Server</td><td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td></tr>
        <tr><td>Memory Limit</td><td><?php echo ini_get('memory_limit'); ?></td></tr>
        <tr><td>Max Execution Time</td><td><?php echo ini_get('max_execution_time'); ?>s</td></tr>
        <tr><td>Error Reporting</td><td><?php echo ini_get('error_reporting'); ?></td></tr>
    </table>
</div>

<div class="section">
    <h2>2. 📦 PHP Extensions</h2>
    <?php
    $extensions = [
        'gd' => 'Graphics library (required for images)',
        'mbstring' => 'Multi-byte string functions',
        'curl' => 'HTTP client',
        'openssl' => 'SSL/TLS support',
        'zip' => 'ZIP archive support',
        'json' => 'JSON support'
    ];
    ?>
    <table>
        <tr><th>Extension</th><th>Status</th><th>Version</th><th>Description</th></tr>
        <?php foreach ($extensions as $ext => $desc): ?>
            <?php 
            $loaded = extension_loaded($ext);
            $version = $loaded ? (phpversion($ext) ?: 'Built-in') : 'N/A';
            $status_class = $loaded ? 'success' : 'error';
            $status_text = $loaded ? '✓ Loaded' : '✗ Missing';
            ?>
            <tr>
                <td><?php echo $ext; ?></td>
                <td class="<?php echo $status_class; ?>"><?php echo $status_text; ?></td>
                <td><?php echo $version; ?></td>
                <td><?php echo $desc; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="section">
    <h2>3. 🎨 GD Extension Details</h2>
    <?php if (extension_loaded('gd')): ?>
        <?php $gd_info = gd_info(); ?>
        <table>
            <tr><th>Feature</th><th>Status</th></tr>
            <?php foreach ($gd_info as $key => $value): ?>
                <?php 
                $display_value = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
                $status_class = (is_bool($value) && $value) || !is_bool($value) ? 'success' : 'error';
                ?>
                <tr>
                    <td><?php echo $key; ?></td>
                    <td class="<?php echo $status_class; ?>"><?php echo $display_value; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="test-result test-error">
            <strong>❌ GD Extension not loaded!</strong>
            <br>This is required for barcode and QR code generation.
            <br><strong>Fix:</strong> Enable GD extension in php.ini by uncommenting: extension=gd
        </div>
    <?php endif; ?>
</div>

<div class="section">
    <h2>4. 📁 File System Check</h2>
    <?php
    $paths = [
        'Project Directory' => __DIR__,
        'Vendor Directory' => __DIR__ . '/vendor',
        'Composer Autoloader' => __DIR__ . '/vendor/autoload.php',
        'Composer Config' => __DIR__ . '/composer.json',
        'Composer Lock' => __DIR__ . '/composer.lock'
    ];
    ?>
    <table>
        <tr><th>Path</th><th>Status</th><th>Details</th></tr>
        <?php foreach ($paths as $label => $path): ?>
            <?php
            $exists = file_exists($path);
            $readable = $exists ? is_readable($path) : false;
            $details = '';
            
            if ($exists) {
                $size = is_file($path) ? filesize($path) : 'Directory';
                $details = "Readable: " . ($readable ? 'Yes' : 'No');
                if (is_file($path)) {
                    $details .= " | Size: " . ($size > 1024 ? round($size/1024, 1) . 'KB' : $size . 'B');
                }
            } else {
                $details = 'Not found';
            }
            
            $status_class = $exists ? 'success' : 'error';
            $status_text = $exists ? '✓ Found' : '✗ Missing';
            ?>
            <tr>
                <td><?php echo $label; ?></td>
                <td class="<?php echo $status_class; ?>"><?php echo $status_text; ?></td>
                <td><?php echo $details; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="section">
    <h2>5. 📚 Composer Libraries</h2>
    <?php if (file_exists(__DIR__ . '/vendor/autoload.php')): ?>
        <?php require_once __DIR__ . '/vendor/autoload.php'; ?>
        <div class="test-result test-success">✓ Composer autoloader loaded successfully</div>
        
        <?php
        $classes = [
            'Picqer\Barcode\BarcodeGeneratorPNG' => 'Barcode Generator (PNG)',
            'Endroid\QrCode\Builder\Builder' => 'QR Code Builder',
            'Endroid\QrCode\Writer\PngWriter' => 'QR PNG Writer',
            'Endroid\QrCode\Encoding\Encoding' => 'QR Encoding Support'
        ];
        ?>
        <table>
            <tr><th>Library Component</th><th>Class</th><th>Status</th></tr>
            <?php foreach ($classes as $class => $name): ?>
                <?php 
                $exists = class_exists($class);
                $status_class = $exists ? 'success' : 'error';
                $status_text = $exists ? '✓ Available' : '✗ Missing';
                ?>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td><code><?php echo $class; ?></code></td>
                    <td class="<?php echo $status_class; ?>"><?php echo $status_text; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="test-result test-error">
            <strong>❌ Composer autoloader not found!</strong>
            <br><strong>Fix:</strong> Run <code>composer install</code> in the WarehouseProLite directory
        </div>
    <?php endif; ?>
</div>

<div class="section">
    <h2>6. 🔧 Barcode Generation Test</h2>
    <?php if (file_exists(__DIR__ . '/vendor/autoload.php')): ?>
        <?php require_once __DIR__ . '/vendor/autoload.php'; ?>
        
        <?php if (class_exists('Picqer\Barcode\BarcodeGeneratorPNG')): ?>
            <?php
            try {
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $barcode_png = $generator->getBarcode('*TEST123*', $generator::TYPE_CODE_39, 2, 50);
                
                if ($barcode_png && strlen($barcode_png) > 100) {
                    $barcode_base64 = base64_encode($barcode_png);
                    echo '<div class="test-result test-success">';
                    echo '<strong>✓ Barcode generation successful!</strong><br>';
                    echo 'Generated ' . strlen($barcode_png) . ' bytes of PNG data<br>';
                    echo '<img src="data:image/png;base64,' . $barcode_base64 . '" alt="Test Barcode" style="margin: 10px 0; border: 1px solid #ddd;">';
                    echo '</div>';
                } else {
                    echo '<div class="test-result test-error">';
                    echo '<strong>❌ Barcode generation failed!</strong><br>';
                    echo 'No data generated or data too small (' . (isset($barcode_png) ? strlen($barcode_png) : '0') . ' bytes)';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="test-result test-error">';
                echo '<strong>❌ Barcode generation error!</strong><br>';
                echo 'Error: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            ?>
        <?php else: ?>
            <div class="test-result test-error">
                <strong>❌ Barcode generator class not available!</strong>
                <br>Install with: <code>composer require picqer/php-barcode-generator</code>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="test-result test-error">Cannot test - Composer autoloader missing</div>
    <?php endif; ?>
</div>

<div class="section">
    <h2>7. 📱 QR Code Generation Test</h2>
    <?php if (file_exists(__DIR__ . '/vendor/autoload.php')): ?>
        <?php require_once __DIR__ . '/vendor/autoload.php'; ?>
        
        <?php if (class_exists('Endroid\QrCode\Builder\Builder')): ?>
            <?php
            try {
                $result = \Endroid\QrCode\Builder\Builder::create()
                    ->writer(new \Endroid\QrCode\Writer\PngWriter())
                    ->data('TEST QR CODE')
                    ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                    ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow())
                    ->size(150)
                    ->margin(10)
                    ->build();
                
                $qr_string = $result->getString();
                
                if ($qr_string && strlen($qr_string) > 100) {
                    $qr_base64 = base64_encode($qr_string);
                    echo '<div class="test-result test-success">';
                    echo '<strong>✓ QR code generation successful!</strong><br>';
                    echo 'Generated ' . strlen($qr_string) . ' bytes of PNG data<br>';
                    echo '<img src="data:image/png;base64,' . $qr_base64 . '" alt="Test QR" style="margin: 10px 0; border: 1px solid #ddd; width: 120px;">';
                    echo '</div>';
                } else {
                    echo '<div class="test-result test-error">';
                    echo '<strong>❌ QR code generation failed!</strong><br>';
                    echo 'No data generated or data too small (' . (isset($qr_string) ? strlen($qr_string) : '0') . ' bytes)';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="test-result test-error">';
                echo '<strong>❌ QR code generation error!</strong><br>';
                echo 'Error: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            ?>
        <?php else: ?>
            <div class="test-result test-error">
                <strong>❌ QR code generator class not available!</strong>
                <br>Install with: <code>composer require endroid/qr-code</code>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="test-result test-error">Cannot test - Composer autoloader missing</div>
    <?php endif; ?>
</div>

<div class="section">
    <h2>8. 🔍 Quick Diagnosis</h2>
    <?php
    $issues = [];
    $fixes = [];
    
    // Check GD
    if (!extension_loaded('gd')) {
        $issues[] = "GD extension not loaded";
        $fixes[] = "Enable GD extension in php.ini: uncomment 'extension=gd'";
    }
    
    // Check autoloader
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        $issues[] = "Composer packages not installed";
        $fixes[] = "Run 'composer install' in the project directory";
    }
    
    // Check barcode class
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        if (!class_exists('Picqer\Barcode\BarcodeGeneratorPNG')) {
            $issues[] = "Barcode generator library missing";
            $fixes[] = "Run 'composer require picqer/php-barcode-generator'";
        }
        if (!class_exists('Endroid\QrCode\Builder\Builder')) {
            $issues[] = "QR code generator library missing";
            $fixes[] = "Run 'composer require endroid/qr-code'";
        }
    }
    
    if (empty($issues)) {
        echo '<div class="test-result test-success">';
        echo '<strong>✅ All systems appear to be working correctly!</strong><br>';
        echo 'If barcodes still don\'t show, check the browser console for JavaScript errors.';
        echo '</div>';
    } else {
        echo '<div class="test-result test-error">';
        echo '<strong>⚠️ Issues Found:</strong><br>';
        foreach ($issues as $issue) {
            echo "• $issue<br>";
        }
        echo '<br><strong>Recommended Fixes:</strong><br>';
        foreach ($fixes as $fix) {
            echo "• $fix<br>";
        }
        echo '</div>';
    }
    ?>
</div>

<hr>
<p><em>Generated on <?php echo date('Y-m-d H:i:s'); ?> by <?php echo gethostname(); ?></em></p>

</body>
</html>
