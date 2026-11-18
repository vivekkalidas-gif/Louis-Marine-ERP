import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const assetsDir = path.resolve(__dirname, '../public/brochure-assets');
const screenshotsDir = path.join(assetsDir, 'screenshots');
const annotationsDir = path.join(assetsDir, 'annotations');
const finalDir = path.join(assetsDir, 'final');

if (!fs.existsSync(finalDir)) {
  fs.mkdirSync(finalDir, { recursive: true });
}

function getImgBase64(filePath) {
  if (fs.existsSync(filePath)) {
    return 'data:image/png;base64,' + fs.readFileSync(filePath).toString('base64');
  }
  return '';
}

function getSvgBase64(filePath) {
  if (fs.existsSync(filePath)) {
    return 'data:image/svg+xml;base64,' + fs.readFileSync(filePath).toString('base64');
  }
  return '';
}

// Load Official Logo (Supports PNG, SVG, or JPG)
function getLogoSrc() {
  const svgPath = path.join(assetsDir, 'diya_logo.svg');
  const pngPath = path.join(assetsDir, 'diya_logo.png');
  const jpgPath = path.join(assetsDir, 'diya_logo.jpg');
  const logoPngPath = path.join(assetsDir, 'logo.png');
  const logoSvgPath = path.join(assetsDir, 'logo.svg');

  if (fs.existsSync(pngPath)) return getImgBase64(pngPath);
  if (fs.existsSync(svgPath)) return getSvgBase64(svgPath);
  if (fs.existsSync(jpgPath)) return getImgBase64(jpgPath);
  if (fs.existsSync(logoPngPath)) return getImgBase64(logoPngPath);
  if (fs.existsSync(logoSvgPath)) return getSvgBase64(logoSvgPath);
  return '';
}

const logoSvg = getLogoSrc();

// Load screenshots as base64
const imgs = {
  login: getImgBase64(path.join(screenshotsDir, '01-login.png')),
  dashboard: getImgBase64(path.join(screenshotsDir, '03-engineer-dashboard.png')),
  engineerDashboard: getImgBase64(path.join(screenshotsDir, '03-engineer-dashboard.png')),
  vessel: getImgBase64(path.join(screenshotsDir, '04-vessel-management.png')),
  serviceForm: getImgBase64(path.join(screenshotsDir, '05-service-form.png')),
  ffe: getImgBase64(path.join(screenshotsDir, '06-ffe.png')),
  lsa: getImgBase64(path.join(screenshotsDir, '07-lsa.png')),
  certForm: getImgBase64(path.join(screenshotsDir, '08-certificate-form.png')),
  certPreview: getImgBase64(path.join(screenshotsDir, '09-certificate-preview.png')),
  generatedCert: getImgBase64(path.join(screenshotsDir, '10-generated-certificate.png')),
  qrCert: getImgBase64(path.join(screenshotsDir, '11-qr-certificate.png')),
  verifyPage: getImgBase64(path.join(annotationsDir, '12-verification-annotated.png')) || getImgBase64(path.join(screenshotsDir, '12-verification-page.png')),
  mobileVerify: getImgBase64(path.join(screenshotsDir, '13-mobile-verification.png')),
  history: getImgBase64(path.join(screenshotsDir, '14-certificate-history.png')),
  reports: getImgBase64(path.join(screenshotsDir, '15-reports.png'))
};

function generateFullBrochureHtml() {
  return `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Diya Tech Solutions - Marine Certificate Management System Brochure</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 0;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #0A192F;
      color: #1E293B;
      font-size: 9pt;
      line-height: 1.45;
    }

    /* Page Container */
    .page {
      width: 210mm;
      height: 297mm;
      page-break-after: always;
      position: relative;
      background: #FFFFFF;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 10mm 12mm 8mm 12mm;
    }

    /* Header Styling */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 3.5mm;
      border-bottom: 2px solid #E2E8F0;
      margin-bottom: 3.5mm;
    }
    .header-logo img {
      height: 9.5mm;
      width: auto;
      display: block;
    }
    .header-tag {
      font-size: 7.5pt;
      font-weight: 800;
      color: #0A2540;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      background: #F1F5F9;
      padding: 2.5px 10px;
      border-radius: 4px;
      border-left: 3px solid #FF1E27;
    }

    /* Footer Styling */
    .footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 3mm;
      border-top: 1.5px solid #E2E8F0;
      font-size: 7.5pt;
      color: #64748B;
      margin-top: 2mm;
    }
    .footer-brand { font-weight: 700; color: #0A2540; }
    .page-number { font-weight: 800; color: #FFFFFF; background: #0A2540; padding: 2px 10px; border-radius: 10px; font-size: 7.5pt; }
    
    /* Typography */
    h1 { font-size: 23pt; font-weight: 900; color: #0A2540; margin: 0 0 2.5mm 0; line-height: 1.15; letter-spacing: -0.5px; }
    h2 { font-size: 14.5pt; font-weight: 800; color: #0A2540; margin: 0 0 2mm 0; line-height: 1.2; letter-spacing: -0.3px; }
    h3 { font-size: 10pt; font-weight: 700; color: #1E3A8A; margin: 0 0 1.5mm 0; }
    p { font-size: 8.8pt; line-height: 1.45; color: #334155; margin: 0 0 2.5mm 0; }
    ul { margin: 0 0 2.5mm 0; padding-left: 4.5mm; font-size: 8.5pt; color: #334155; line-height: 1.45; }
    li { margin-bottom: 1mm; }
    
    /* Layout Grids */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 4mm; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 3.5mm; }
    .grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 3mm; }
    
    /* Browser Frame Presentation */
    .browser-window {
      width: 100%;
      border-radius: 8px;
      border: 1px solid #CBD5E1;
      background: #FFFFFF;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(10, 37, 64, 0.08);
      display: flex;
      flex-direction: column;
    }
    .browser-header {
      background: #F1F5F9;
      border-bottom: 1px solid #E2E8F0;
      padding: 4px 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .browser-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .dot-red { background: #EF4444; }
    .dot-yellow { background: #F59E0B; }
    .dot-green { background: #10B981; }
    .browser-title {
      font-size: 7pt;
      font-weight: 700;
      color: #64748B;
      margin-left: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .browser-body {
      flex: 1;
      position: relative;
      overflow: hidden;
      background: #FFFFFF;
    }
    .browser-body img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: top left;
      display: block;
    }

    /* Cards & Accents */
    .hero-card {
      background: linear-gradient(135deg, #0A2540 0%, #1E3A8A 100%);
      color: #FFFFFF;
      border-radius: 10px;
      padding: 5mm 7mm;
      margin-bottom: 3.5mm;
      box-shadow: 0 8px 20px rgba(10, 37, 64, 0.15);
    }
    .hero-card h1 { color: #FFFFFF; }
    .hero-card p { color: #E2E8F0; font-size: 9.8pt; }

    .feature-card {
      background: #FFFFFF;
      border: 1px solid #E2E8F0;
      border-radius: 6px;
      padding: 3mm 3.5mm;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .feature-card h4 { margin: 0 0 1mm 0; font-size: 8.8pt; color: #0A2540; font-weight: 700; }
    .feature-card p { margin: 0; font-size: 8pt; color: #475569; line-height: 1.35; }
    
    .pill {
      background: rgba(255, 30, 39, 0.12);
      color: #FF1E27;
      border: 1px solid #FF1E27;
      padding: 2px 10px;
      border-radius: 12px;
      font-size: 7.8pt;
      font-weight: 800;
    }

    .step-card {
      background: #FFFFFF;
      border: 1.5px solid #E2E8F0;
      border-radius: 6px;
      padding: 2.5mm 3mm;
    }
    .step-num {
      background: #0A2540;
      color: #FFFFFF;
      font-weight: 900;
      font-size: 7.5pt;
      padding: 2px 7px;
      border-radius: 4px;
      display: inline-block;
      margin-bottom: 1.5mm;
    }
    .step-title { font-size: 8.2pt; font-weight: 800; color: #0A2540; margin-bottom: 1mm; }

    .badge-label {
      font-size: 7.5pt;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 2px 8px;
      border-radius: 4px;
      display: inline-block;
    }
  </style>
</head>
<body>

  <!-- ==================== PAGE 1: PREMIUM COVER ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Enterprise Marine Software</div>
    </div>
    
    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div class="hero-card">
        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.15); color: #FFFFFF; padding: 3px 12px; border-radius: 20px; font-size: 8pt; font-weight: 800; text-transform: uppercase; margin-bottom: 2.5mm;">
          <span>Digital Certification & Service Management</span>
        </div>
        <h1 style="font-size: 24pt; margin-bottom: 2mm;">MARINE CERTIFICATE<br>MANAGEMENT SYSTEM</h1>
        <p style="font-size: 10pt; font-weight: 700; color: #00D4B2; margin-bottom: 2mm; text-transform: uppercase; letter-spacing: 0.5px;">
          DIGITIZE EVERY INSPECTION. GENERATE EVERY CERTIFICATE. VERIFY EVERY RECORD.
        </p>
        <p style="font-size: 9.2pt; margin-bottom: 3.5mm; line-height: 1.4;">
          A purpose-built digital platform for marine FFE & LSA service operations to manage vessels, service jobs, inspections, certificates, PDF generation, and online verification from one centralized system.
        </p>
        <div style="display: flex; gap: 2.5mm; flex-wrap: wrap;">
          <span class="pill" style="background:#FFFFFF; color:#0A2540; border-color:#FFFFFF;">FFE Module</span>
          <span class="pill" style="background:#FFFFFF; color:#0A2540; border-color:#FFFFFF;">LSA Module</span>
          <span class="pill" style="background:#FFFFFF; color:#0A2540; border-color:#FFFFFF;">Dynamic Form Builder</span>
          <span class="pill" style="background:#FFFFFF; color:#0A2540; border-color:#FFFFFF;">QR Public Verification</span>
        </div>
      </div>

      <div class="grid-2" style="gap: 4mm;">
        <div style="display: flex; flex-direction: column; gap: 4mm;">
          <div class="browser-window" style="height: 54mm;">
            <div class="browser-header">
              <span class="browser-dot dot-red"></span>
              <span class="browser-dot dot-yellow"></span>
              <span class="browser-dot dot-green"></span>
              <span class="browser-title">Engineer Operations Dashboard</span>
            </div>
            <div class="browser-body">
              <img src="${imgs.engineerDashboard}" alt="Engineer Operations Dashboard">
            </div>
          </div>
          <div class="browser-window" style="height: 54mm;">
            <div class="browser-header">
              <span class="browser-dot dot-red"></span>
              <span class="browser-dot dot-yellow"></span>
              <span class="browser-dot dot-green"></span>
              <span class="browser-title">Marine Field Service Workstation</span>
            </div>
            <div class="browser-body">
              <img src="${imgs.serviceForm}" alt="Field Service Workstation">
            </div>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 4mm;">
          <div class="browser-window" style="height: 54mm;">
            <div class="browser-header">
              <span class="browser-dot dot-red"></span>
              <span class="browser-dot dot-yellow"></span>
              <span class="browser-dot dot-green"></span>
              <span class="browser-title">Official PDF Certificate Preview</span>
            </div>
            <div class="browser-body">
              <img src="${imgs.certPreview}" alt="Certificate Preview">
            </div>
          </div>
          <div class="browser-window" style="height: 54mm;">
            <div class="browser-header">
              <span class="browser-dot dot-red"></span>
              <span class="browser-dot dot-yellow"></span>
              <span class="browser-dot dot-green"></span>
              <span class="browser-title">Real-Time Public QR Verification</span>
            </div>
            <div class="browser-body">
              <img src="${imgs.verifyPage}" alt="QR Verification">
            </div>
          </div>
        </div>
      </div>

      <div class="feature-card" style="background: #F8FAFC; border-left: 4px solid #0A2540; margin-top: 2.5mm;">
        <p style="margin: 0; font-size: 8.2pt; color: #0A2540; font-weight: 600;">
          <strong>Built for Marine Service Operations:</strong> Designed specifically for marine servicing companies, FFE & LSA service providers, ship management firms, vessel operators, and safety inspection engineering teams across Qatar, UAE, India, and global maritime hubs.
        </p>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 01</div>
    </div>
  </div>


  <!-- ==================== PAGE 2: WHY THIS SOFTWARE (THE BUSINESS PROBLEM) ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Operational Challenges & Business Problem</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>Transforming Marine Certification: From Manual Workflows to Digital Precision</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          Marine FFE & LSA servicing companies often face heavy administrative overhead when managing safety equipment inspections using Word documents, Excel spreadsheets, or paper logs. Manual certification creates formatting inconsistencies, repeated vessel data entry, delayed certificate delivery, and retrieval struggles during audits.
        </p>

        <!-- Before vs After Comparison Card -->
        <div class="grid-2" style="gap: 4mm; margin-bottom: 3.5mm;">
          <div style="background: #FEF2F2; border: 1.5px solid #FCA5A5; border-radius: 8px; padding: 3.5mm 4mm;">
            <div style="font-size: 8.5pt; font-weight: 800; color: #991B1B; text-transform: uppercase; margin-bottom: 2mm;">
              BEFORE (Manual Workflows)
            </div>
            <ul style="margin: 0; padding-left: 4mm; font-size: 8pt; color: #7F1D1D;">
              <li>Repeated vessel specs & IMO entry on every service job</li>
              <li>Manual formatting prone to typos and missing fields</li>
              <li>Scattered certificate records buried in folders & binders</li>
              <li>High administrative workload for field service engineers</li>
              <li>No instant online verification for shipowners or inspectors</li>
            </ul>
          </div>

          <div style="background: #F0FDF4; border: 1.5px solid #86EFAC; border-radius: 8px; padding: 3.5mm 4mm;">
            <div style="font-size: 8.5pt; font-weight: 800; color: #166534; text-transform: uppercase; margin-bottom: 2mm;">
              AFTER (Digital Platform)
            </div>
            <ul style="margin: 0; padding-left: 4mm; font-size: 8pt; color: #14532D;">
              <li>Reusable vessel profiles populated automatically</li>
              <li>Standardized certificate templates with field validation</li>
              <li>Centralized searchable records by IMO, vessel, or client</li>
              <li>Structured field inspection workflows to save engineer time</li>
              <li>Online QR verification for fast stakeholder access</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="browser-window" style="height: 118mm;">
        <div class="browser-header">
          <span class="browser-dot dot-red"></span>
          <span class="browser-dot dot-yellow"></span>
          <span class="browser-dot dot-green"></span>
          <span class="browser-title">Centralized Marine Service Jobs & Vessel Management Workstation</span>
        </div>
        <div class="browser-body">
          <img src="${imgs.vessel}" alt="Vessel Management Workstation">
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 02</div>
    </div>
  </div>


  <!-- ==================== PAGE 3: PLATFORM OVERVIEW & ARCHITECTURE ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Platform Overview & Architecture</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>Unified Ecosystem for Marine Certification & Service Operations</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          The Marine Certificate Management System connects office management teams, field service engineers, standardized certificate templates, and end customers into one continuous digital pipeline.
        </p>

        <!-- Architecture Flow Diagram -->
        <div style="background: #F8FAFC; border: 1.5px solid #0A2540; border-radius: 8px; padding: 3mm 4mm; margin-bottom: 3.5mm;">
          <div style="font-size: 7.8pt; font-weight: 800; color: #0A2540; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2mm; text-align: center;">
            Integrated End-to-End System Architecture Flow
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; gap: 2mm;">
            <div style="background:#0A2540; color:white; padding:2mm 3mm; border-radius:6px; font-size:7.5pt; font-weight:700; text-align:center; flex:1;">CUSTOMER</div>
            <div style="color:#0A2540; font-weight:900;">&rarr;</div>
            <div style="background:#0A2540; color:white; padding:2mm 3mm; border-radius:6px; font-size:7.5pt; font-weight:700; text-align:center; flex:1;">VESSEL</div>
            <div style="color:#0A2540; font-weight:900;">&rarr;</div>
            <div style="background:#0A2540; color:white; padding:2mm 3mm; border-radius:6px; font-size:7.5pt; font-weight:700; text-align:center; flex:1;">SERVICE JOB</div>
            <div style="color:#0A2540; font-weight:900;">&rarr;</div>
            <div style="background:#0A2540; color:white; padding:2mm 3mm; border-radius:6px; font-size:7.5pt; font-weight:700; text-align:center; flex:1;">INSPECTION</div>
            <div style="color:#0A2540; font-weight:900;">&rarr;</div>
            <div style="background:#0A2540; color:white; padding:2mm 3mm; border-radius:6px; font-size:7.5pt; font-weight:700; text-align:center; flex:1;">CERTIFICATE</div>
            <div style="color:#0A2540; font-weight:900;">&rarr;</div>
            <div style="background:#FF1E27; color:white; padding:2mm 3mm; border-radius:6px; font-size:7.5pt; font-weight:700; text-align:center; flex:1;">PDF + QR</div>
            <div style="color:#0A2540; font-weight:900;">&rarr;</div>
            <div style="background:#00D4B2; color:#0A2540; padding:2mm 3mm; border-radius:6px; font-size:7.5pt; font-weight:800; text-align:center; flex:1.2;">VERIFICATION</div>
          </div>
        </div>

        <div class="grid-3" style="gap: 3mm; margin-bottom: 3.5mm;">
          <div class="feature-card">
            <h4>Fleet & Customer Management</h4>
            <p>Store complete vessel specs: IMO number, flag state, call sign, vessel owner, and classification society details.</p>
          </div>
          <div class="feature-card">
            <h4>FFE & LSA Service Records</h4>
            <p>Dedicated inspection categories for extinguishers, SCBA, EEBD, CO₂ fixed systems, life rafts, and life jackets.</p>
          </div>
          <div class="feature-card">
            <h4>Public QR Verification</h4>
            <p>Every issued certificate embeds a secure QR code for instant public online verification by authorized stakeholders.</p>
          </div>
        </div>
      </div>

      <div class="browser-window" style="height: 118mm;">
        <div class="browser-header">
          <span class="browser-dot dot-red"></span>
          <span class="browser-dot dot-yellow"></span>
          <span class="browser-dot dot-green"></span>
          <span class="browser-title">Centralized Certificate Registry & Audit Traceability Center</span>
        </div>
        <div class="browser-body">
          <img src="${imgs.history}" alt="Certificate History Registry">
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 03</div>
    </div>
  </div>


  <!-- ==================== PAGE 4: END-TO-END WORKFLOW ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">End-to-End Workflow</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>9-Step Complete Certification & Servicing Workflow</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3mm;">
          From the moment an engineer logs into the workstation to public QR verification, every step is structured, validated, and recorded cleanly.
        </p>

        <!-- 9 Step Grid -->
        <div class="grid-3" style="gap: 3mm; margin-bottom: 3.5mm;">
          <div class="step-card">
            <span class="step-num">STEP 1</span>
            <div class="step-title">Engineer Login</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Secure access for authorized service engineers and office administrators.</p>
          </div>
          <div class="step-card">
            <span class="step-num">STEP 2</span>
            <div class="step-title">Select Customer & Vessel</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Choose vessel from fleet registry; IMO, flag, and call sign populate automatically.</p>
          </div>
          <div class="step-card">
            <span class="step-num">STEP 3</span>
            <div class="step-title">Create Service Job</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Select service category (FFE, LSA, SCBA, EEBD, Life Raft, CO₂ Systems).</p>
          </div>
          <div class="step-card">
            <span class="step-num">STEP 4</span>
            <div class="step-title">Enter Inspection Data</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Record serial numbers, test pressures, gas purity, tare weights, and findings.</p>
          </div>
          <div class="step-card">
            <span class="step-num">STEP 5</span>
            <div class="step-title">Mandatory Field Validation</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Required-field validation helps prevent incomplete inspection records.</p>
          </div>
          <div class="step-card">
            <span class="step-num">STEP 6</span>
            <div class="step-title">Generate Certificate</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Dynamic template rendering generates official multi-page certificate layout.</p>
          </div>
          <div class="step-card">
            <span class="step-num">STEP 7</span>
            <div class="step-title">Deliver PDF to Client</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Export high-resolution PDF complete with signatures, stamps, and QR code.</p>
          </div>
          <div class="step-card">
            <span class="step-num">STEP 8</span>
            <div class="step-title">Scan QR Verification</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Inspectors scan embedded QR code using any smartphone or camera.</p>
          </div>
          <div class="step-card">
            <span class="step-num">STEP 9</span>
            <div class="step-title">Public Verification Page</div>
            <p style="margin:0; font-size:7.8pt; color:#475569;">Online validation page confirms certificate details and active status.</p>
          </div>
        </div>
      </div>

      <!-- 3 Visual Screenshot Strip for Steps -->
      <div class="grid-3" style="gap: 3.5mm; height: 58mm;">
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">Step 2: Vessel Registry</span></div>
          <div class="browser-body"><img src="${imgs.vessel}" alt="Step 2 Vessel"></div>
        </div>
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">Step 4: Inspection Entry</span></div>
          <div class="browser-body"><img src="${imgs.certForm}" alt="Step 4 Form"></div>
        </div>
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">Step 9: Online Verification</span></div>
          <div class="browser-body"><img src="${imgs.verifyPage}" alt="Step 9 Verification"></div>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 04</div>
    </div>
  </div>


  <!-- ==================== PAGE 5: VESSEL & CUSTOMER MANAGEMENT ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Fleet Registry & Vessel Management</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>Centralized Vessel Registry: Enter Data Once, Reuse Everywhere</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          Eliminate repetitive vessel data entry. Store technical vessel profiles once and automatically inject official specs into every service job and certificate.
        </p>

        <div class="grid-2" style="gap: 4mm; margin-bottom: 3.5mm;">
          <div class="feature-card">
            <h4>Technical Vessel Specs Stored</h4>
            <ul>
              <li><strong>Vessel Name & Flag State:</strong> Port of Registry & Official Flag</li>
              <li><strong>IMO Number & Call Sign:</strong> Unique international identification</li>
              <li><strong>Classification Society:</strong> DNV, ABS, LR, BV, RINA, NKK mapping</li>
              <li><strong>Owner / Management Company:</strong> Client association & billing</li>
              <li><strong>Gross Tonnage & Year Built:</strong> Vessel technical particulars</li>
            </ul>
          </div>
          <div class="feature-card" style="background: #F8FAFC; border-left: 3.5px solid #0A2540;">
            <h4>Core Efficiency Benefits</h4>
            <p><strong>Zero Repetitive Data Entry:</strong> Never re-type IMO numbers or vessel specifications on future certificates.</p>
            <p><strong>Fleet History Tracking:</strong> View all past servicing jobs, inspection certificates, and upcoming expiry dates per vessel.</p>
            <p><strong>Consistent Data Across Certificates:</strong> Standardized vessel profiles help ensure complete data accuracy across all engineers.</p>
          </div>
        </div>
      </div>

      <div class="browser-window" style="height: 118mm;">
        <div class="browser-header">
          <span class="browser-dot dot-red"></span>
          <span class="browser-dot dot-yellow"></span>
          <span class="browser-dot dot-green"></span>
          <span class="browser-title">Vessel Management & Technical Profile Registry</span>
        </div>
        <div class="browser-body">
          <img src="${imgs.vessel}" alt="Vessel Management Screen">
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 05</div>
    </div>
  </div>


  <!-- ==================== PAGE 6: ENGINEER WORKFLOW ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Field Operations & Workstation</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>Built for Engineers in the Field: Rapid Service Data Logging</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          Reduce repetitive administrative work so engineers can focus on inspection and servicing at port or anchorage.
        </p>

        <div class="grid-4" style="gap: 3mm; margin-bottom: 3.5mm;">
          <div class="feature-card">
            <h4>Assigned Service Jobs</h4>
            <p>Access active dispatches and schedules directly on the workstation dashboard.</p>
          </div>
          <div class="feature-card">
            <h4>Mandatory Validation</h4>
            <p>Required fields help prevent incomplete certificates from being issued.</p>
          </div>
          <div class="feature-card">
            <h4>Equipment / Inspection Data</h4>
            <p>Log cylinder serials, test pressures, tare weights, and findings rapidly.</p>
          </div>
          <div class="feature-card">
            <h4>Certificate Generation</h4>
            <p>Generate draft certificates for review immediately upon completing inspection inputs.</p>
          </div>
        </div>
      </div>

      <div class="browser-window" style="height: 118mm;">
        <div class="browser-header">
          <span class="browser-dot dot-red"></span>
          <span class="browser-dot dot-yellow"></span>
          <span class="browser-dot dot-green"></span>
          <span class="browser-title">Field Marine Engineer Dashboard & Inspection Workstation</span>
        </div>
        <div class="browser-body">
          <img src="${imgs.engineerDashboard}" alt="Engineer Dashboard Screen">
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 06</div>
    </div>
  </div>


  <!-- ==================== PAGE 7: STRUCTURED FFE & LSA MODULES ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">FFE & LSA Servicing Modules</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>Purpose-Built Workflows for Marine FFE & LSA Appliances</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          Configured specifically for safety servicing companies. Manage distinct technical inspection schedules for Fire Fighting Equipment (FFE) and Life Saving Appliances (LSA).
        </p>

        <div class="grid-2" style="gap: 4mm; margin-bottom: 3.5mm;">
          <div class="feature-card" style="border-top: 3px solid #0A2540;">
            <h4>FFE SERVICING MODULE</h4>
            <ul>
              <li><strong>Fire Extinguishers:</strong> Portable CO₂, Foam, Powder & Water units</li>
              <li><strong>SCBA Systems:</strong> Breathing Apparatus masks, cylinders & demand valves</li>
              <li><strong>EEBD Devices:</strong> Emergency Escape Breathing Devices</li>
              <li><strong>CO₂ Fixed Systems:</strong> High & Low pressure cylinder bank mass & pressure</li>
              <li><strong>Gas Detection:</strong> Portable & Fixed gas sensor calibration logging</li>
            </ul>
          </div>
          <div class="feature-card" style="border-top: 3px solid #00D4B2;">
            <h4>LSA SERVICING MODULE</h4>
            <ul>
              <li><strong>Life Rafts:</strong> SOLAS Pack A/B items, HRU units & container inspections</li>
              <li><strong>Life Jackets:</strong> Inflatable 150N/275N auto-inflation & light checks</li>
              <li><strong>Immersion Suits:</strong> Air pressure leak test & zipper seal lubrication</li>
              <li><strong>Lifeboats & Gear:</strong> Structure, engine, davit, winch & release hooks</li>
              <li><strong>Pyrotechnics:</strong> Distress signal expiry schedules & inventory checks</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="grid-2" style="gap: 4mm; height: 118mm;">
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">FFE Inspection Data Workstation</span></div>
          <div class="browser-body"><img src="${imgs.ffe}" alt="FFE Module"></div>
        </div>
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">LSA Servicing Workstation</span></div>
          <div class="browser-body"><img src="${imgs.lsa}" alt="LSA Module"></div>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 07</div>
    </div>
  </div>


  <!-- ==================== PAGE 8: CERTIFICATE GENERATION ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Certificate Generation & PDF Engine</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>From Inspection Data to Official Certificate — In Seconds</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          Transform field inspection inputs into official multi-page PDF certificates with automated page layouts, dynamic tables, authorized signatures, and embedded verification QR codes.
        </p>

        <div class="grid-4" style="gap: 2.5mm; margin-bottom: 3.5mm;">
          <div class="feature-card">
            <h4>Dynamic Templates</h4>
            <p>Automatic multi-page handling for detailed equipment records.</p>
          </div>
          <div class="feature-card">
            <h4>Company Branding</h4>
            <p>Embed custom company headers, logos, address, and accreditation seals.</p>
          </div>
          <div class="feature-card">
            <h4>Authorized Signatures</h4>
            <p>Digitized sign-offs for Service Engineers & Quality Managers.</p>
          </div>
          <div class="feature-card">
            <h4>Embedded QR Code</h4>
            <p>Instant scan verification link printed on every official document sheet.</p>
          </div>
        </div>
      </div>

      <div class="grid-2" style="gap: 4mm; height: 118mm;">
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">Certificate Form Builder & Template Engine</span></div>
          <div class="browser-body"><img src="${imgs.certForm}" alt="Certificate Builder"></div>
        </div>
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">Official Generated PDF Certificate Preview</span></div>
          <div class="browser-body"><img src="${imgs.certPreview}" alt="Generated Certificate"></div>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 08</div>
    </div>
  </div>


  <!-- ==================== PAGE 9: CERTIFICATE HISTORY & TRACEABILITY ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Historical Record Access & Registry</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>Complete Certificate History, Search & Expiry Traceability</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          Keep certificates and inspection records organized in one searchable system. Retrieve any historic certificate in seconds by IMO number, vessel name, customer, or certificate ID.
        </p>

        <div style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 6px; padding: 2.5mm 3.5mm; margin-bottom: 3.5mm;">
          <div style="font-size: 7.8pt; font-weight: 800; color: #0A2540; text-transform: uppercase; margin-bottom: 1.5mm;">
            Instant Multi-Parameter Search & Filter Engine
          </div>
          <div style="display: flex; gap: 3mm; font-size: 7.8pt; color: #334155; font-weight: 600;">
            <span>Search by IMO Number</span> &bull; 
            <span>Filter by Vessel Name</span> &bull; 
            <span>Search by Customer</span> &bull; 
            <span>Track Expiry Dates</span>
          </div>
        </div>

        <div class="grid-3" style="gap: 3mm; margin-bottom: 3.5mm;">
          <div class="feature-card">
            <h4>Instant PDF Retrieval</h4>
            <p>Access active or archived PDF certificates instantly during customer inquiries or reviews.</p>
          </div>
          <div class="feature-card">
            <h4>Proactive Expiry Tracking</h4>
            <p>Track upcoming certificate and service expiry dates to support timely follow-up.</p>
          </div>
          <div class="feature-card">
            <h4>Historical Record Access</h4>
            <p>Maintain structured digital records for fast retrieval across historical service jobs.</p>
          </div>
        </div>
      </div>

      <div class="browser-window" style="height: 118mm;">
        <div class="browser-header">
          <span class="browser-dot dot-red"></span>
          <span class="browser-dot dot-yellow"></span>
          <span class="browser-dot dot-green"></span>
          <span class="browser-title">Certificate History Registry & Search Dashboard</span>
        </div>
        <div class="browser-body">
          <img src="${imgs.history}" alt="Certificate History">
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 09</div>
    </div>
  </div>


  <!-- ==================== PAGE 10: QR PUBLIC VERIFICATION ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Public QR Verification Engine</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>Verify a Certificate in Seconds: Instant Public QR Verification</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          Shipowners, inspectors, and authorized stakeholders can access certificate verification information through the online QR verification page.
        </p>

        <!-- 4 Step Visual Flow -->
        <div class="grid-4" style="gap: 2.5mm; margin-bottom: 3.5mm;">
          <div class="step-card">
            <span class="step-num">01</span>
            <div class="step-title">SCAN QR</div>
            <p style="margin:0; font-size:7.5pt; color:#475569;">Scan code on printed or digital certificate.</p>
          </div>
          <div class="step-card">
            <span class="step-num">02</span>
            <div class="step-title">OPEN PORTAL</div>
            <p style="margin:0; font-size:7.5pt; color:#475569;">Directs to secure cloud verification route.</p>
          </div>
          <div class="step-card">
            <span class="step-num">03</span>
            <div class="step-title">CHECK DETAILS</div>
            <p style="margin:0; font-size:7.5pt; color:#475569;">Displays verified vessel name, IMO # & dates.</p>
          </div>
          <div class="step-card">
            <span class="step-num">04</span>
            <div class="step-title">VIEW STATUS</div>
            <p style="margin:0; font-size:7.5pt; color:#475569;">Confirms current status: ACTIVE or EXPIRED.</p>
          </div>
        </div>
      </div>

      <div class="grid-2" style="gap: 4mm; height: 118mm;">
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">Certificate with Embedded QR Code</span></div>
          <div class="browser-body"><img src="${imgs.qrCert}" alt="QR Certificate"></div>
        </div>
        <div class="browser-window">
          <div class="browser-header"><span class="browser-title">Live Public Online Verification Page</span></div>
          <div class="browser-body"><img src="${imgs.verifyPage}" alt="Verification Portal"></div>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 10</div>
    </div>
  </div>


  <!-- ==================== PAGE 11: BUSINESS VALUE & ROI ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Business Value & Operational ROI</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h2>Tangible Business Benefits for Marine Servicing Operations</h2>
        <p style="font-size: 8.8pt; color: #334155; margin-bottom: 3.5mm;">
          Adopting the Marine Certificate Management System delivers immediate operational efficiency, administrative time savings, and organized service documentation.
        </p>

        <div class="grid-3" style="gap: 3.5mm; margin-bottom: 3.5mm;">
          <div class="feature-card" style="border-top: 3px solid #0A2540;">
            <h4>Faster Certificate Preparation</h4>
            <p>Auto-filled vessel profiles and reusable templates reduce repetitive certificate preparation time.</p>
          </div>
          <div class="feature-card" style="border-top: 3px solid #FF1E27;">
            <h4>Reduced Data Entry Errors</h4>
            <p>Reusable vessel profiles and required-field validation help reduce repetitive entry mistakes and missing technical info.</p>
          </div>
          <div class="feature-card" style="border-top: 3px solid #00D4B2;">
            <h4>Faster Record Access & Verification</h4>
            <p>Centralized certificate records and online verification make certificate information easier to retrieve and review.</p>
          </div>
          <div class="feature-card" style="border-top: 3px solid #00D4B2;">
            <h4>Centralized Certificate Archive</h4>
            <p>Keep certificates and inspection records organized in one searchable system by IMO, vessel, or customer.</p>
          </div>
          <div class="feature-card" style="border-top: 3px solid #0A2540;">
            <h4>Better Engineer Productivity</h4>
            <p>Reduce administrative certificate preparation so engineers focus on servicing safety equipment.</p>
          </div>
          <div class="feature-card" style="border-top: 3px solid #FF1E27;">
            <h4>Proactive Expiry Tracking</h4>
            <p>Track upcoming certificate and service expiry dates to support timely client follow-up.</p>
          </div>
        </div>
      </div>

      <div class="browser-window" style="height: 110mm;">
        <div class="browser-header">
          <span class="browser-dot dot-red"></span>
          <span class="browser-dot dot-yellow"></span>
          <span class="browser-dot dot-green"></span>
          <span class="browser-title">Management Reports & Servicing Analytics Dashboard</span>
        </div>
        <div class="browser-body">
          <img src="${imgs.reports}" alt="Reports & Analytics">
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; Product Brochure</div>
      <div class="page-number">Page 11</div>
    </div>
  </div>


  <!-- ==================== PAGE 12: STRONG SALES CTA ==================== -->
  <div class="page">
    <div class="header">
      <div class="header-logo"><img src="${logoSvg}" alt="Diya Tech Solutions Logo"></div>
      <div class="header-tag">Schedule Your Demonstration</div>
    </div>

    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="background: linear-gradient(135deg, #0A2540 0%, #1E3A8A 100%); color: white; padding: 5.5mm 7mm; border-radius: 10px; margin-bottom: 4mm;">
          <h2 style="color: white; font-size: 17pt; margin-bottom: 2mm;">READY TO DIGITIZE YOUR MARINE CERTIFICATION WORKFLOW?</h2>
          <p style="color: #E2E8F0; font-size: 9.2pt; margin-bottom: 3.5mm; line-height: 1.4;">
            Discover how the Marine Certificate Management System fits into your existing FFE & LSA servicing workflow. Streamline vessel management, reduce manual paperwork, and deliver verified digital certificates to your clients.
          </p>
          <div style="display: flex; gap: 4mm;">
            <span class="pill" style="background:#FF1E27; color:white; border:none; padding:4px 16px; font-size:9.5pt; font-weight:800;">BOOK A LIVE DEMO</span>
            <span class="pill" style="background:rgba(255,255,255,0.15); color:white; border:1px solid white; padding:4px 14px; font-size:8.5pt;">REQUEST WORKFLOW CONSULTATION</span>
          </div>
        </div>

        <div class="grid-2" style="gap: 4mm; margin-bottom: 3.5mm;">
          <div class="feature-card" style="border-left: 4px solid #0A2540; padding: 3.5mm;">
            <h4 style="font-size: 9.5pt; color: #0A2540;">Direct Sales & Technical Support</h4>
            <p style="font-size: 8.8pt; margin-bottom: 1.5mm;">📞 <strong>Phone:</strong> +91-9159523221</p>
            <p style="font-size: 8.8pt; margin-bottom: 1.5mm;">✉️ <strong>Email:</strong> sales@diyatechsolutions.com</p>
            <p style="font-size: 8.8pt; margin: 0;">🌐 <strong>Website:</strong> www.diyatechsolutions.com</p>
          </div>

          <div class="feature-card" style="border-left: 4px solid #00D4B2; padding: 3.5mm;">
            <h4 style="font-size: 9.5pt; color: #0A2540;">Serving Global Maritime Hubs</h4>
            <p style="font-size: 8.2pt; margin: 0; color: #475569;">
              Providing digital certification solutions for marine servicing companies across <strong>Qatar, UAE, India, Singapore</strong>, and international maritime ports.
            </p>
          </div>
        </div>
      </div>

      <div class="browser-window" style="height: 110mm;">
        <div class="browser-header">
          <span class="browser-dot dot-red"></span>
          <span class="browser-dot dot-yellow"></span>
          <span class="browser-dot dot-green"></span>
          <span class="browser-title">Official Generated PDF Certificate Preview & Verification Portal</span>
        </div>
        <div class="browser-body">
          <img src="${imgs.certPreview}" alt="Certificate Preview Portal">
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="footer-brand">Diya Tech Solutions &bull; lighting the way with technology</div>
      <div class="page-number">Page 12</div>
    </div>
  </div>

</body>
</html>
  `;
}

function generateQuickBrochureHtml() {
  return generateFullBrochureHtml();
}

async function renderPdfs() {
  console.log('📝 Writing final refined 12-page brochure HTML files...');
  const fullHtml = generateFullBrochureHtml();
  const quickHtml = generateQuickBrochureHtml();

  const fullHtmlPath = path.join(__dirname, '../public/brochure-assets/Marine_Certificate_Management_System_Brochure.html');
  const quickHtmlPath = path.join(__dirname, '../public/brochure-assets/Marine_Certificate_Management_System_Quick_Brochure.html');

  fs.writeFileSync(fullHtmlPath, fullHtml);
  fs.writeFileSync(quickHtmlPath, quickHtml);
  console.log('✅ HTML files generated successfully.');

  console.log('🖨️ Rendering PDFs with Puppeteer...');
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  // 1. Render Full 12-Page Brochure PDF
  console.log('📄 Rendering 12-Page Full Product Brochure PDF...');
  const page1 = await browser.newPage();
  await page1.setContent(fullHtml, { waitUntil: 'networkidle0' });
  const fullPdfPath = path.join(finalDir, 'Marine_Certificate_Management_System_Brochure.pdf');
  await page1.pdf({
    path: fullPdfPath,
    format: 'A4',
    printBackground: true,
    margin: { top: 0, right: 0, bottom: 0, left: 0 }
  });

  const brainArtifactDir = 'C:\\Users\\VICTUS RTX BY HP\\.gemini\\antigravity\\brain\\24974027-3c8b-4556-8dc0-f8d68b0415e9';
  if (fs.existsSync(brainArtifactDir)) {
    fs.copyFileSync(fullPdfPath, path.join(brainArtifactDir, 'Marine_Certificate_Management_System_Brochure.pdf'));
  }

  // 2. Render Quick Sales Brochure PDF
  console.log('📄 Rendering Quick Sales Brochure PDF...');
  const page2 = await browser.newPage();
  await page2.setContent(quickHtml, { waitUntil: 'networkidle0' });
  const quickPdfPath = path.join(finalDir, 'Marine_Certificate_Management_System_Quick_Brochure.pdf');
  await page2.pdf({
    path: quickPdfPath,
    format: 'A4',
    printBackground: true,
    margin: { top: 0, right: 0, bottom: 0, left: 0 }
  });

  if (fs.existsSync(brainArtifactDir)) {
    fs.copyFileSync(quickPdfPath, path.join(brainArtifactDir, 'Marine_Certificate_Management_System_Quick_Brochure.pdf'));
  }

  await browser.close();
  console.log('🎉 Both Premium PDF Brochures rendered successfully!');
}

renderPdfs().catch(err => {
  console.error('❌ Error rendering brochure PDFs:', err);
  process.exit(1);
});
