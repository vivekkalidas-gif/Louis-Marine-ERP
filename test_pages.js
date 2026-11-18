import puppeteer from 'puppeteer';

async function test() {
  const browser = await puppeteer.launch({ headless: 'new' });
  const page = await browser.newPage();

  await page.goto('http://127.0.0.1:8089/login', { waitUntil: 'networkidle0' });
  await page.type('#email', 'admin@louismarine.qa');
  await page.type('#password', 'password');
  await Promise.all([
    page.click('button[type="submit"]'),
    page.waitForNavigation({ waitUntil: 'networkidle0' })
  ]);

  console.log('Admin Dashboard URL:', page.url());
  const content = await page.content();
  console.log('Dashboard Has Exception:', content.includes('Exception') || content.includes('QueryException'));

  await page.goto('http://127.0.0.1:8089/admin/vessels', { waitUntil: 'networkidle0' });
  const contentVessel = await page.content();
  console.log('Vessels Has Exception:', contentVessel.includes('Exception') || contentVessel.includes('QueryException'));

  await browser.close();
}

test().catch(console.error);
