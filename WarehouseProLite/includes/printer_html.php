<?php

class PrinterHTML {
    private $conn;
    private $use_barcode;
    private $use_qrcode;
    
    /**
     * Initialize printer and check for barcode/QR libraries
     */
    public function __construct($db_connection) {
        $this->conn = $db_connection;
        
        // Load Composer autoloader if available
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }
        
        // Check if libraries are available
        $this->use_barcode = class_exists('Picqer\Barcode\BarcodeGeneratorPNG');
        $this->use_qrcode = class_exists('Endroid\QrCode\Builder\Builder');
    }
    
    /**
     * Generates HTML labels for a single SKU
     */
    public function printLabelForSKU($sku, $copies = 1) {
        $s = strtoupper(trim($sku));
        $product = $this->getProductBySKU($s);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found for SKU: ' . htmlspecialchars($s)];
        }
        
        $labels = [];
        for ($i = 0; $i < $copies; $i++) {
            $labels[] = $this->generateSingleLabelHTML($product);
        }
        $html = $this->wrapLabelsForPrinting($labels);
        $this->logPrintJob($s, $copies, 'HTML Generator');
        
        return [
            'success' => true,
            'html' => $html,
            'sku' => $s,
            'copies' => $copies
        ];
    }
    
    /**
     * Generates HTML labels for multiple SKUs
     */
    public function printMultipleLabels($skus, $copies = 1) {
        $html_parts = [];
        $has_labels = false;
        
        foreach ($skus as $sku) {
            $s = strtoupper(trim($sku));
            $product = $this->getProductBySKU($s);
            
            if (!$product) continue;
            
            $has_labels = true;
            for ($i = 0; $i < $copies; $i++) {
                $html_parts[] = $this->generateSingleLabelHTML($product);
            }
            $this->logPrintJob($s, $copies, 'HTML Generator');
        }
        
        if (!$has_labels) {
            return ['success' => false, 'message' => 'No valid products found'];
        }
        
        $html = $this->wrapLabelsForPrinting($html_parts);
        
        return [
            'success' => true,
            'html' => $html
        ];
    }
    
    /**
     * Generates HTML for a single product label
     */
    private function generateSingleLabelHTML($product) {
        // Get product fields
        $name = $this->cleanText($product['name']);
        if (strlen($name) > 30) {
            $name = substr($name, 0, 50);
        }
        
        // =Nz([Country],"") & IIf(Len([Class] & "")>0," — " & [Class],"")
        $country = $product['country'] ?? '';
        $class = $product['class'] ?? '';
        $countryClass = $country;
        if (strlen($class) > 0) {
            $countryClass = ($countryClass ? $countryClass . ' — ' : '') . $class;
        }
        
        // =IIf([PackUOM]="g","Pack: " & Nz([DefaultPackWeight],0) & " g","Pack: each")
        $packUOM = $product['pack_uom'] ?? '';
        $defaultPackWeight = $product['default_pack_weight'] ?? 0;
        if ($packUOM == 'g') {
            $packText = 'Pack: ' . number_format($defaultPackWeight, 0) . ' g';
        } else {
            $packText = 'Pack: each';
        }
        
        // =IIf(Nz([BestBeforeDays],0)>0,"BB: " & Format(DateAdd("d",[BestBeforeDays],Date()),"dd-mmm-yy"),"")
        $bestBeforeDays = (int)($product['best_before_days'] ?? 0);
        $bestBefore = '';
        if ($bestBeforeDays > 0) {
            $bestBefore = date('d-M-y', strtotime("+{$bestBeforeDays} days"));
        }
        
        // =IIf(Len(Nz([LotPrefix],""))>0,"Lot: " & [LotPrefix] & Format(Date(),"yymmdd"),"")
        $lotPrefix = $product['lot_prefix'] ?? '';
        $lotNumber = '';
        if (strlen($lotPrefix) > 0) {
            $lotNumber = $lotPrefix . date('ymd');
        }
        
        // Generate barcode
        $barcodeText = $this->code39Text($product['sku']);
        $barcodeImage = $this->generateBarcodeImage($product['sku']);
        $barcodeHTML = '';
        
        if ($barcodeImage) {
            // Barcode image
            $barcodeBase64 = base64_encode($barcodeImage);
            $barcodeHTML = '<img src="data:image/png;base64,' . $barcodeBase64 . '" alt="Barcode" class="barcode-image">';
        } else {
            // Text barcode
            $barcodeHTML = '<div class="barcode-text">' . htmlspecialchars($barcodeText) . '</div>';
        }
        
        // Generate QR code
        $qrData = json_encode([
            'type' => 'product',
            'sku' => $product['sku'],
            'name' => $product['name'],
            'lot' => $lotNumber
        ]);
        $qrImage = $this->generateQRCodeImage($qrData);
        $qrHTML = '';
        
        if ($qrImage) {
            // QR image
            $qrBase64 = base64_encode($qrImage);
            $qrHTML = '<img src="data:image/png;base64,' . $qrBase64 . '" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;">';
        } else {
            // Text placeholder
            $qrHTML = '<div style="font-size: 8px; color: #666; text-align: center; padding: 2mm;">QR: ' . htmlspecialchars($product['sku']) . '</div>';
        }
        
        // Build label
        $html = '
        <div class="label">
            <div class="label-content">
                <div class="label-name">Product Name:</div>
                <div class="label-value">' . htmlspecialchars($name) . '</div>
            
                <div class="label-name">Country/Class:</div>
                <div class="label-value">' . htmlspecialchars($countryClass) . '</div>
            
                <div class="label-name">Pack text:</div>
                <div class="label-value">' . htmlspecialchars($packText) . '</div>
            
                <div class="label-name">Best Before:</div>
                <div class="label-value">' . (strlen($bestBefore) ? htmlspecialchars($bestBefore) : '') . '</div>
            
                <div class="label-name">Lot:</div>
                <div class="label-value">' . (strlen($lotNumber) ? htmlspecialchars($lotNumber) : '') . '</div>
            
                <div class="label-name">SKU:</div>
                <div class="label-value">' . htmlspecialchars($product['sku']) . '</div>
            
                <div class="label-name">Barcode:</div>
                <div class="label-value">' . $barcodeHTML . '</div>
            </div>   
            <div class="qr-code">' . $qrHTML . '</div>
        </div>';
    return $html;
    }
    
    /**
     * Wraps label in a print-ready document
     */
    private function wrapLabelsForPrinting($labels) {
        $labels_html = implode('', $labels);
        
        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
            /* Base label styles - same for preview and print */
            .label {
                width: 148mm;
                height: 105mm;
                box-sizing: border-box;
                border: 1px solid #000;
                padding: 3mm 4mm;
                margin: 0;
                font-family: Arial, sans-serif;
                position: relative;
                overflow: hidden;
                page-break-after: always;
                page-break-inside: avoid;
            }
            .label-content {
                display: grid;
                grid-template-columns: auto 1fr;
                grid-template-rows: auto auto auto auto auto auto auto auto;
                column-gap: 3mm;
                row-gap: 1.5mm;
                font-size: 30px;
                line-height: 1.3;
                height: 99mm;
                align-content: start;
            }
            .label-name {
                font-weight: normal;
                font-size: 25px;
            }
            .label-value {
                font-weight: bold;
                font-size: 25px;
                word-wrap: break-word;
            }
            .barcode-image {
                max-width: 85mm;
                height: auto;
                display: inline-block;
                vertical-align: middle;
            }
            .barcode-text {
                font-family: "Courier New", monospace;
                font-size: 16px;
                letter-spacing: 2px;
            }
            .qr-code {
                position: absolute;
                top: 30mm;
                right: 5mm;
                width: 30mm;
                height: 30mm;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            body {
                margin: 0;
                padding: 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
                background: #f0f0f0;
            }
            .label {
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                max-width: 90vw;
            }
            .print-button {
                position: fixed;
                top: 10px;
                right: 20px;
                padding: 10px 20px;
                background: #2e8b57;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                z-index: 1000;
                font-size: 25px;
            }
            .print-button:hover { background: #256b44; }
        
            @media print {
                @page {
                    size: 148mm 105mm landscape;
                    margin: 0;
                }
                body {
                    margin: 0;
                    padding: 0;
                }
                .label {
                    width: 148mm;
                    height: 105mm;
                    max-width: none;
                    box-shadow: none;
                }
                .print-button {
                    display: none;
                }
            }
            </style>
        </head>
        <body>
            <button class="print-button no-print" onclick="window.print()">Print</button>
        ' . $labels_html . '
        </body>
        </html>';
    }
    
    /**
     * Clean text by replacing special characters
     */
    private function cleanText($s) {
        if (empty($s)) return '';
        $s = str_replace('–', '-', $s);
        $s = str_replace('—', '-', $s);
        $s = str_replace("'", "'", $s);
        return $s;
    }
    
    /**
     * Format text as Code39 barcode format
     */
    public function code39Text($s) {
        if (empty($s)) return '';
        $text = strtoupper(str_replace(' ', '-', $s));
        return '*' . $text . '*';
    }

    /**
     * Generate barcode image into PNG
     */
    private function generateBarcodeImage($sku) {
        if (!$this->use_barcode) {
            return null;
        }
        try {
            $barcode_data = $this->code39Text($sku);
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcode_png = $generator->getBarcode($barcode_data, $generator::TYPE_CODE_39, 3, 80);
            if (!$barcode_png || strlen($barcode_png) < 100) {
                return null;
            }
            return $barcode_png;
        } catch (Exception $e) {
            error_log("Barcode generation error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generates QR code image into PNG
     */
    private function generateQRCodeImage($data) {
        if (!$this->use_qrcode) {
            return null;
        }
        try {
            $result = \Endroid\QrCode\Builder\Builder::create()
                ->writer(new \Endroid\QrCode\Writer\PngWriter())
                ->data($data)
                ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow())
                ->size(150)
                ->margin(10)
                ->build();
            $qr_string = $result->getString();
            return $qr_string ?: null;
        } catch (Exception $e) {
            error_log("QR code generation error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Fetch product from database by SKU
     */
    private function getProductBySKU($sku) {
        $stmt = mysqli_prepare($this->conn, 
            "SELECT p.*
             FROM products p
             WHERE p.sku = ? AND p.is_active = 1
             LIMIT 1"
        );
        if (!$stmt) return null;
        mysqli_stmt_bind_param($stmt, "s", $sku);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row;
    }
    
    /**
     * Log print job to database
     */
    private function logPrintJob($sku, $copies, $printerName) {
        $this->createPrintLogTable();
        $copies = max(1, (int)$copies);
        $stmt = mysqli_prepare($this->conn, 
            "INSERT INTO print_log (sku, copies, pack_date, printed_at, printer_name) 
             VALUES (?, ?, CURDATE(), NOW(), ?)"
        );
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sis", $sku, $copies, $printerName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    /**
     * Creates print log table
     */
    private function createPrintLogTable() {
        mysqli_query($this->conn, 
            "CREATE TABLE IF NOT EXISTS print_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sku VARCHAR(50) NOT NULL,
                copies INT NOT NULL DEFAULT 1,
                pack_date DATE NOT NULL,
                printed_at DATETIME NOT NULL,
                printer_name VARCHAR(100),
                INDEX idx_sku (sku),
                INDEX idx_printed_at (printed_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }
}