// @ts-check
const { test, expect } = require('@playwright/test');

test('invalid login test', async ({ page }) => {

  await page.goto('http://localhost/StockTrackProLite/index.php');


  await page.fill('input[name="username"]', 'wronguser');
  await page.fill('input[name="password"]', 'wrongpass');


  await page.click('input[type="submit"]');


  await expect(page).toHaveURL(/error=1/);
});
