import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const inputDir = path.resolve(__dirname, '../public/brochure-assets/screenshots');
const outputDir = path.resolve(__dirname, '../public/brochure-assets/annotations');

if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

async function renderCallouts() {
  console.log('🎨 Generating annotated screenshot callouts...');
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();
  await page.setViewport({ width: 1920, height: 1080, deviceScaleFactor: 2 });

  // 1. Dashboard Callout
  const dashboardImg = fs.readFileSync(path.join(inputDir, '02-dashboard.png')).toString('base64');
  const dashboardHtml = `
    <!DOCTYPE html>
    <html>
    <head>
      <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; position: relative; }
        img { width: 1920px; height: 1080px; display: block; }
        .callout {
          position: absolute;
          display: flex;
          align-items: center;
          background: rgba(10, 37, 64, 0.92);
          color: white;
          padding: 6px 14px;
          border-radius: 20px;
          border: 2px solid #00D4B2;
          box-shadow: 0 4px 15px rgba(0,0,0,0.3);
          font-weight: 600;
          font-size: 14px;
          z-index: 10;
        }
        .badge {
          background: #00D4B2;
          color: #0A2540;
          width: 22px;
          height: 22px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: bold;
          margin-right: 8px;
          font-size: 12px;
        }
      </style>
    </head>
    <body>
      <img src="data:image/png;base64,${dashboardImg}" />
      <div class="callout" style="top: 140px; left: 240px;"><span class="badge">01</span> Quick Certificate Creation</div>
      <div class="callout" style="top: 220px; left: 240px;"><span class="badge">02</span> Vessel & Fleet Registry</div>
      <div class="callout" style="top: 300px; left: 240px;"><span class="badge">03</span> Certificate History & Status</div>
      <div class="callout" style="top: 380px; left: 240px;"><span class="badge">04</span> Expiry Alerts & Tracking</div>
      <div class="callout" style="top: 460px; left: 240px;"><span class="badge">05</span> Operations Analytics</div>
    </body>
    </html>
  `;
  await page.setContent(dashboardHtml);
  await page.screenshot({ path: path.join(outputDir, '02-dashboard-annotated.png') });

  // 2. Verification Page Callout
  const verifyImg = fs.readFileSync(path.join(inputDir, '12-verification-page.png')).toString('base64');
  const verifyHtml = `
    <!DOCTYPE html>
    <html>
    <head>
      <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, sans-serif; position: relative; }
        img { width: 1920px; height: 1080px; display: block; }
        .callout {
          position: absolute;
          display: flex;
          align-items: center;
          background: rgba(10, 37, 64, 0.92);
          color: white;
          padding: 6px 14px;
          border-radius: 20px;
          border: 2px solid #00D4B2;
          box-shadow: 0 4px 15px rgba(0,0,0,0.3);
          font-weight: 600;
          font-size: 14px;
          z-index: 10;
        }
        .badge {
          background: #00D4B2;
          color: #0A2540;
          width: 22px;
          height: 22px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: bold;
          margin-right: 8px;
          font-size: 12px;
        }
      </style>
    </head>
    <body>
      <img src="data:image/png;base64,${verifyImg}" />
      <div class="callout" style="top: 180px; left: 550px;"><span class="badge">01</span> Real-Time Verification Status</div>
      <div class="callout" style="top: 280px; left: 550px;"><span class="badge">02</span> Unique Certificate Number & QR Link</div>
      <div class="callout" style="top: 380px; left: 550px;"><span class="badge">03</span> Vessel Name & IMO Details</div>
      <div class="callout" style="top: 480px; left: 550px;"><span class="badge">04</span> Servicing Company Official Seal</div>
    </body>
    </html>
  `;
  await page.setContent(verifyHtml);
  await page.screenshot({ path: path.join(outputDir, '12-verification-annotated.png') });

  await browser.close();
  console.log('✅ Callout annotations generated successfully!');
}

renderCallouts().catch(err => {
  console.error('❌ Error rendering callouts:', err);
  process.exit(1);
});
