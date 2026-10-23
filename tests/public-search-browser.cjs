const assert = require('node:assert/strict');
const {spawn, execFileSync} = require('node:child_process');
const path = require('node:path');
const fs = require('node:fs');
const {chromium} = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const database = process.argv[2];
if (!/^nhip_khoa_test_[a-z0-9_]+$/.test(database || '')) throw Error('Dedicated test database required');
const php = process.env.PHP_BINARY || 'C:/xampp/php/php.exe';
const root = path.resolve(__dirname, '..');
const env = {...process.env, DB_HOST:'127.0.0.1', DB_PORT:'3306', DB_NAME:database, DB_USER:'root', DB_PASSWORD:'', DB_SSL_CA:''};
const artifacts = path.join(process.env.TEMP || root, 'nhip-khoa-timkiem-qa');
fs.mkdirSync(artifacts, {recursive:true});
const base = 'http://127.0.0.1:8017';
const server = spawn(php, ['-S','127.0.0.1:8017','-t',root], {env,windowsHide:true,stdio:'ignore'});
const failedServer = spawn(php, ['-S','127.0.0.1:8018','-t',root], {env:{...env,DB_PORT:'1'},windowsHide:true,stdio:'ignore'});
function update(sql) {
  execFileSync(php, ['-r', '$p=new PDO("mysql:host=127.0.0.1;port=3306;dbname=".getenv("DB_NAME"),"root","");$p->exec($argv[1]);', sql], {env,windowsHide:true});
}
(async()=>{
 let browser;
 try {
  for(let i=0;i<40;i++){try{await fetch(base+'/about.php');break;}catch{await new Promise(r=>setTimeout(r,150));}}
  browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BINARY || 'C:/Program Files/Google/Chrome/Application/chrome.exe'});
  const page=await browser.newPage({viewport:{width:1440,height:1000}});
  const jsErrors=[];page.on('pageerror',e=>jsErrors.push(e.message));
  const get=async url=>{const response=await page.goto(base+url);await page.waitForLoadState('domcontentloaded');return response.status();};
  assert.equal(await get('/pages/tim-kiem.php'),200);
  assert.equal(await page.locator('.public-card').count(),6);
  assert.equal(await page.locator('main').count(),1);
  assert(!(await page.textContent('body')).includes('PRIVATE-'));
  await get('/pages/tim-kiem.php?q='+encodeURIComponent('học bổng')+'&page=2');
  assert.equal(await page.locator('.public-card').count(),3);
  assert((await page.textContent('.public-count')).includes('9'));
  await page.locator('.public-card h2 a').first().click();
  assert((await page.textContent('.public-content')).includes('<script>'));
  assert.equal(await page.evaluate(()=>window.injected),undefined);
  assert.equal(await page.locator('[name="submit_comment"]').count(),0);
  await page.locator('.public-back').click();
  assert(new URL(page.url()).searchParams.get('page')==='2');
  assert(new URL(page.url()).searchParams.get('q')==='học bổng');
  await get('/pages/tim-kiem.php?q='+encodeURIComponent("O'Reilly"));
  assert((await page.textContent('.public-count')).includes('9'));
  await get('/pages/tim-kiem.php?q='+encodeURIComponent("' OR 1=1 --"));
  assert.equal(await page.locator('.public-card').count(),0);
  await get('/pages/tim-kiem.php?q[]=bad&page[]=bad');
  assert.equal(await page.locator('.public-card').count(),6);
  await get('/pages/tim-kiem.php?q=khong-co-ket-qua');
  assert((await page.textContent('body')).includes('Không tìm thấy bài viết'));
  await get('/pages/tin-khoa.php?page=-1');
  assert.equal(await page.locator('.public-card').count(),6);
  await get('/pages/tin-khoa.php?page=999');
  assert.equal(await page.locator('.public-card').count(),3);
  for(const id of [0,-1,13,14,15,16,999999]) assert.equal(await get('/bai-viet.php?id='+id),404);
  assert.equal(await get('/pages/chi-tiet-bai-viet.php?id=1&from=tin-khoa&page=2'),200);
  assert(new URL(page.url()).pathname==='/bai-viet.php');
  await get('/pages/chi-tiet-thay-doi.php');
  assert(new URL(page.url()).pathname==='/dang-phat-trien.php');
  update("UPDATE posts SET status='published',published_at='2026-09-06 12:00:00' WHERE id=14");
  assert.equal(await get('/bai-viet.php?id=14'),200);
  await get('/pages/tim-kiem.php?q=PRIVATE-pending');
  assert.equal(await page.locator('.public-card').count(),1);
  await get('/pages/tin-khoa.php');
  assert((await page.textContent('body')).includes('PRIVATE-pending'));
  await get('/index.php');
  assert((await page.textContent('body')).includes('PRIVATE-pending'));
  update("UPDATE posts SET status='pending',published_at=NULL WHERE id=14");
  update("UPDATE categories SET status='hidden' WHERE slug='co-hoi'");
  await get('/pages/co-hoi.php');
  assert.equal(await page.locator('.public-card').count(),0);
  update("UPDATE categories SET status='active' WHERE slug='co-hoi'");
  for(const width of [375,768,1440]){
   await page.setViewportSize({width,height:1000});
   for(const url of ['/index.php','/pages/tim-kiem.php','/pages/tin-khoa.php','/pages/hoc-tap.php','/pages/co-hoi.php','/pages/su-kien.php','/bai-viet.php?id=1','/dang-nhap.php']){
    assert.equal(await get(url),200);
    assert(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'Horizontal overflow: '+width+' '+url);
    assert.equal(await page.locator('main').count(),1);
    if(url!='/dang-nhap.php'){
     if(width<=1000){
      await page.locator('.mobile-menu').click();
      assert.equal(await page.locator('.mobile-menu').getAttribute('aria-expanded'),'true');
      await page.locator('.desktop-nav a', {hasText:'Tin khoa'}).first().click();
      assert(new URL(page.url()).pathname==='/pages/tin-khoa.php');
      await get(url);
     }
    }
    await page.screenshot({path:path.join(artifacts,width+'-'+url.split('?')[0].replaceAll('/','_')+'.png'),fullPage:true});
   }
  }
  await page.setViewportSize({width:375,height:1000});
  await get('/index.php');
  await page.locator('.mobile-menu').click();
  await page.locator('.mobile-search-link').click();
  assert(new URL(page.url()).pathname==='/pages/tim-kiem.php');
  await page.setViewportSize({width:1440,height:1000});
  await get('/index.php');
  await page.locator('.search-circle').click();
  await page.locator('.search-input').fill('học bổng');
  await page.locator('.search-input').press('Enter');
  assert(new URL(page.url()).pathname==='/pages/tim-kiem.php');
  await page.locator('.search-circle').click();
  await page.locator('.search-input').press('Escape');
  for(const role of ['reader','author','editor','admin']){
   await get('/dang-nhap.php');
   await page.locator('[name=email]').fill(role+'@example.test');
   await page.locator('[name=password]').fill('TestOnly123!');
   await page.locator('.figma-auth-form button[type=submit]').click();
   await page.waitForURL(u=>!u.pathname.endsWith('dang-nhap.php'));
   await get('/pages/tin-khoa.php');
   assert((await page.textContent('.welcome')).includes(role));
   await page.locator('.logout-button').click();
   await get('/pages/tin-khoa.php');
   assert.equal(await page.locator('.login-pill').count(),1);
  }
  const response=await page.goto('http://127.0.0.1:8018/pages/tim-kiem.php');
  assert.equal(response.status(),503);
  assert((await page.textContent('body')).includes('Chưa thể tải bài viết'));
  assert.equal((await page.goto('http://127.0.0.1:8018/bai-viet.php?id=1')).status(),503);
  assert.deepEqual(jsErrors,[]);
  console.log('PASS: search, pagination, visibility, publishing, escaping, redirects, responsive, mobile menu, auth, database errors');
  console.log('Screenshots: '+artifacts);
 } finally {
  if(browser)await browser.close();
  server.kill();failedServer.kill();
 }
})().catch(e=>{console.error(e);process.exitCode=1;});
