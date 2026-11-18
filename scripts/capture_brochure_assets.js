import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const outputDir = path.resolve(__dirname, '../public/brochure-assets/screenshots');

if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

async function captureScreenshots() {
  console.log('🚀 Starting Puppeteer browser session...');
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();
  await page.setViewport({ width: 1920, height: 1080, deviceScaleFactor: 2 });

  const BASE_URL = 'http://127.0.0.1:8089';

  // 1. SCREENSHOT 01: Login Screen
  console.log('📸 Capturing 01-login.png...');
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '01-login.png'), fullPage: false });

  // Login as Admin
  console.log('🔑 Logging in as Admin (admin@louismarine.qa)...');
  await page.type('#email', 'admin@louismarine.qa');
  await page.type('#password', 'password');
  await Promise.all([
    page.click('button[type="submit"]'),
    page.waitForNavigation({ waitUntil: 'networkidle0' })
  ]);

  console.log('Current URL after login:', page.url());

  // 2. SCREENSHOT 02: Main Admin Dashboard
  console.log('📸 Capturing 02-dashboard.png...');
  await page.goto(`${BASE_URL}/admin/dashboard`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '02-dashboard.png'), fullPage: false });

  // 3. SCREENSHOT 04: Vessel Management
  console.log('📸 Capturing 04-vessel-management.png...');
  await page.goto(`${BASE_URL}/admin/vessels`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '04-vessel-management.png'), fullPage: false });

  // 4. SCREENSHOT 05: Service/Inspection Creation / Jobs
  console.log('📸 Capturing 05-service-form.png...');
  await page.goto(`${BASE_URL}/admin/jobs`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '05-service-form.png'), fullPage: false });

  // 5. SCREENSHOT 06: FFE Module (Equipment Categories)
  console.log('📸 Capturing 06-ffe.png...');
  await page.goto(`${BASE_URL}/admin/equipment/categories`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '06-ffe.png'), fullPage: false });

  // 6. SCREENSHOT 07: LSA Module (Equipment Items)
  console.log('📸 Capturing 07-lsa.png...');
  await page.goto(`${BASE_URL}/admin/equipment/items`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '07-lsa.png'), fullPage: false });

  // 7. SCREENSHOT 08: Certificate Creation Form
  console.log('📸 Capturing 08-certificate-form.png...');
  await page.goto(`${BASE_URL}/admin/certificates/create`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '08-certificate-form.png'), fullPage: false });

  // 8. SCREENSHOT 09 & 11: Certificate Preview & QR Code Section
  console.log('📸 Capturing 09-certificate-preview.png & 11-qr-certificate.png...');
  await page.goto(`${BASE_URL}/admin/certificates/019b69ee-7ea5-7132-9723-cab8004c2a4c`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '09-certificate-preview.png'), fullPage: false });
  await page.screenshot({ path: path.join(outputDir, '11-qr-certificate.png'), fullPage: false });

  // 9. SCREENSHOT 14: Certificate Search & History Table
  console.log('📸 Capturing 14-certificate-history.png...');
  await page.goto(`${BASE_URL}/admin/certificates`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '14-certificate-history.png'), fullPage: false });

  // 10. SCREENSHOT 15: Operations & Analytics Reports
  console.log('📸 Capturing 15-reports.png...');
  await page.goto(`${BASE_URL}/admin/reports/operations`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '15-reports.png'), fullPage: false });

  // 11. Switch to Engineer & Capture Engineer Dashboard
  console.log('🔑 Logging out and logging in as Engineer (engineer1@louismarine.qa)...');
  await page.goto(`${BASE_URL}/admin/dashboard`);
  const client = await page.target().createCDPSession();
  await client.send('Network.clearBrowserCookies');

  await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle0' });
  await page.type('#email', 'engineer1@louismarine.qa');
  await page.type('#password', 'password');
  await Promise.all([
    page.click('button[type="submit"]'),
    page.waitForNavigation({ waitUntil: 'networkidle0' })
  ]);

  console.log('Current URL after engineer login:', page.url());

  // SCREENSHOT 03: Engineer Dashboard
  console.log('📸 Capturing 03-engineer-dashboard.png...');
  await page.goto(`${BASE_URL}/engineer/dashboard`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '03-engineer-dashboard.png'), fullPage: false });

  // 12. SCREENSHOT 12: Public QR Verification Page (Desktop View)
  console.log('📸 Capturing 12-verification-page.png...');
  await page.goto(`${BASE_URL}/verify/LMQ-2025-CO2-0006`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '12-verification-page.png'), fullPage: false });

  // 13. SCREENSHOT 13: Public QR Verification Page (Mobile View)
  console.log('📱 Capturing 13-mobile-verification.png...');
  await page.setViewport({ width: 375, height: 812, deviceScaleFactor: 2, isMobile: true });
  await page.goto(`${BASE_URL}/verify/LMQ-2025-CO2-0006`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '13-mobile-verification.png'), fullPage: false });

  // 14. Generated Certificate - Render view
  console.log('📄 Capturing 10-generated-certificate.png...');
  await page.setViewport({ width: 1920, height: 1080, deviceScaleFactor: 2 });
  await page.goto(`${BASE_URL}/verify/LMQ-2025-CO2-0006`, { waitUntil: 'networkidle0' });
  await page.screenshot({ path: path.join(outputDir, '10-generated-certificate.png'), fullPage: false });

  await browser.close();
  console.log('✅ All authentic application screenshots captured successfully!');
}

captureScreenshots().catch(err => {
  console.error('❌ Error capturing screenshots:', err);
  process.exit(1);
});
