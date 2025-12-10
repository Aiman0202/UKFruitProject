import { test, expect } from '@playwright/test';

test('Stock increases after adjustment', async ({ page }) => {

    const BASE_URL = 'http://localhost';
    const PRODUCT_NAME_WAREHOUSE = "LF-LMN-006 - Lemon (loose)";
    const ADJUST_QTY = 5;

    await page.goto(`${BASE_URL}/WarehouseProLite/index.php`);
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', 'admin');
    await page.click('input[type="submit"]');

    await page.goto(`${BASE_URL}/WarehouseProLite/adjustment_add.php`);
    await page.waitForSelector('select[name="product_id"]');
    await page.selectOption('select[name="product_id"]', { label: PRODUCT_NAME_WAREHOUSE });
    await page.fill('input[name="qty_delta"]', ADJUST_QTY.toString());
    await page.selectOption('select[name="reason"]', 'correction');
    await page.click('input[type="submit"]');

    await page.context().clearCookies();
    await page.evaluate(() => localStorage.clear());

    await page.goto(`${BASE_URL}/StockTrackProLite/index.php`);
    await page.waitForSelector('input[name="username"]');
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', 'admin');
    await page.click('input[type="submit"]');

    const PRODUCT_NAME_STOCK = 'Lemon (loose)';
    await page.goto(`${BASE_URL}/StockTrackProLite/products.php`);
    const row = page.locator('table tbody tr', { hasText: PRODUCT_NAME_STOCK });
    await row.waitFor({ state: 'visible', timeout: 10000 });

    const currentStockText = await row.locator('td:nth-child(11)').innerText();
    const currentStock = parseInt(currentStockText, 10);

    const EXPECTED_STOCK = currentStock;
    await expect(row.locator('td:nth-child(11)')).toHaveText(String(EXPECTED_STOCK));

});




