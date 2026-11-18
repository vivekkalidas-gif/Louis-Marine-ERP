import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const brainDir = 'C:\\Users\\VICTUS RTX BY HP\\.gemini\\antigravity\\brain\\24974027-3c8b-4556-8dc0-f8d68b0415e9\\brochure-assets\\final_pages';
if (!fs.existsSync(brainDir)) {
  fs.mkdirSync(brainDir, { recursive: true });
}

async function exportPagePreviews() {
  console.log('📸 Exporting PNG page previews for 12-page brochure...');
  const browser = await puppeteer.launch({ headless: 'new' });
  const page = await browser.newPage();
  await page.setViewport({ width: 1240, height: 1754, deviceScaleFactor: 1.5 }); // A4 aspect ratio

  // 1. Export 12 pages of Full Brochure
  const fullPath = path.resolve(__dirname, '../public/brochure-assets/Marine_Certificate_Management_System_Brochure.html');
  await page.goto(`file://${fullPath}`, { waitUntil: 'networkidle0' });

  const pages = await page.$$('.page');
  for (let i = 0; i < pages.length; i++) {
    const pageNum = String(i + 1).padStart(2, '0');
    const outPath = path.join(brainDir, `full_brochure_page_${pageNum}.png`);
    await pages[i].screenshot({ path: outPath });
    console.log(`Saved ${outPath}`);
  }

  // 2. Export 2 pages of Quick Brochure
  const quickPath = path.resolve(__dirname, '../public/brochure-assets/Marine_Certificate_Management_System_Quick_Brochure.html');
  await page.goto(`file://${quickPath}`, { waitUntil: 'networkidle0' });

  const quickPages = await page.$$('.page');
  for (let i = 0; i < quickPages.length; i++) {
    const pageNum = String(i + 1).padStart(2, '0');
    const outPath = path.join(brainDir, `quick_brochure_page_${pageNum}.png`);
    await quickPages[i].screenshot({ path: outPath });
    console.log(`Saved ${outPath}`);
  }

  await browser.close();
  console.log('✅ Page previews exported successfully!');
}

exportPagePreviews().catch(console.error);
