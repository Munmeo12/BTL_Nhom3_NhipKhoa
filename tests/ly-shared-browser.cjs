const assert = require('node:assert/strict');
const {spawn,execFileSync} = require('node:child_process');
const path = require('node:path');
const fs = require('node:fs');
const crypto = require('node:crypto');
if (!process.argv.includes('--allow-shared-vps')) throw Error('Explicit opt-in required');
const {chromium} = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const root = path.resolve(__dirname,'..');
const php = process.env.PHP_BINARY || 'C:/xampp/php/php.exe';
const run = 'qa-ly-'+crypto.randomBytes(8).toString('hex');
const artifacts = path.join(process.env.TEMP || root,run);
fs.mkdirSync(artifacts,{recursive:true});
function fixture(action) {
  return JSON.parse(execFileSync(php,[path.join(__dirname,'ly-shared-fixture.php'),'--allow-shared-vps',action,run],{cwd:root,encoding:'utf8',windowsHide:true}));
}
const servers=[];let browser,data;
let checks=0;
function check(value,message) {assert(value,message);checks++;}
async function start(port,docroot) {
  const server=spawn(php,['-S','127.0.0.1:'+port,'-t',docroot],{cwd:root,env:process.env,windowsHide:true,stdio:'ignore'});
  servers.push(server);
  for(let i=0;i<40;i++) {try{await fetch('http://127.0.0.1:'+port+'/');return;}catch{await new Promise(r=>setTimeout(r,150));}}
  throw Error('Test server unavailable');
}
async function login(page,base,role) {
  await page.goto(base+'/dang-nhap.php');
  await page.locator('[name=email]').fill(data.users[role].email);
  await page.locator('[name=password]').fill(data.password);
  await Promise.all([page.waitForURL(u=>!u.pathname.endsWith('/dang-nhap.php')),page.locator('.figma-auth-form button[type=submit]').click()]);
}
async function token(page) {return page.locator('[name=csrf_token]').first().inputValue();}
async function post(page,url,form) {
  const response=await page.request.post(url,{form,maxRedirects:0});
  return {status:response.status(),body:await response.text(),headers:response.headers()};
}
(async()=>{
  try {
    data=fixture('setup');console.log('Fixture created:',run);
    await start(8021,root);await start(8022,path.dirname(root));
    browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BINARY || 'C:/Program Files/Google/Chrome/Application/chrome.exe'});
    for(const base of ['http://127.0.0.1:8021','http://127.0.0.1:8022/'+path.basename(root)]) {
      const guestContext=await browser.newContext();const guestPage=await guestContext.newPage();
      await guestPage.goto(base+'/index.php');
      check((await guestPage.textContent('#featured')).includes('Lưu lại những bài viết quan trọng'),'Guest sees Impact Box login state');
      check(await guestPage.locator('#featured a[href="dang-nhap.php"]').count()>=1,'Guest Impact Box login link');
      check((await guestPage.goto(base+'/bai-viet.php?id='+data.posts.published)).status()===200,'Guest article opens');
      check(await guestPage.locator('.comment-login-prompt').count()===1,'Guest sees login prompt');
      check(await guestPage.locator('.comment-form').count()===0,'Guest has no comment form');
      check((await post(guestPage,base+'/bai-viet.php?id='+data.posts.published,{comment_submit:'1',content:data.prefix+' guest',csrf_token:''})).status===419,'Guest comment CSRF');
      await guestPage.goto(base+'/dang-nhap.php');const guestCsrf=await token(guestPage);
      const guestSubmit=await post(guestPage,base+'/bai-viet.php?id='+data.posts.published,{comment_submit:'1',content:data.prefix+' guest',csrf_token:guestCsrf});
      check(guestSubmit.status===200 && guestSubmit.body.includes('Vui lòng đăng nhập để bình luận.'),'Guest must log in to comment');
      await guestContext.close();
      const context=await browser.newContext({viewport:{width:1440,height:1000}});
      const admin=await context.newPage();
      await login(admin,base,'admin');
      for(const route of ['/admin/dashboard.php','/views/categories.php','/admin/binhluan.php']) {
        check((await admin.goto(base+route)).status()===200,'Admin route '+route);
        check(!(await admin.textContent('body')).includes('Fatal error'),'No PHP fatal');
        for(const href of await admin.locator('.admin-nav a').evaluateAll(nodes=>nodes.map(n=>n.href))) {
          check((await admin.request.get(href,{maxRedirects:0})).status()===200,'Menu '+href);
        }
        for(const href of await admin.locator('link[rel=stylesheet]').evaluateAll(nodes=>nodes.map(n=>n.href).filter(h=>h.startsWith(location.origin)))) {
          check((await admin.request.get(href)).status()===200,'CSS '+href);
        }
      }
      for(const width of [375,768,1440]) {
        await admin.setViewportSize({width,height:1000});
        for(const route of ['/views/categories.php','/admin/binhluan.php','/bai-viet.php?id='+data.posts.published]) {
          check((await admin.goto(base+route)).status()===200,'Responsive page');
          check(await admin.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'Horizontal overflow '+width+' '+route);
          check((await admin.locator('main').count())===1,'One main region '+route);
          await admin.screenshot({path:path.join(artifacts,(base.includes('8022')?'sub-':'root-')+width+'-'+route.split('?')[0].replaceAll('/','_')+'.png'),fullPage:true});
        }
      }
      await admin.setViewportSize({width:1440,height:1000});
      await admin.goto(base+'/views/category-add.php');
      const csrf=await token(admin);
      const suffix=base.includes('8022')?'sub':'root';
      const name=data.prefix+' ui-'+suffix, slug=run+'-ui-'+suffix;
      const form={csrf_token:csrf,name,slug,description:'[TEST] mô tả',status:'active'};
      check((await post(admin,base+'/views/category-add.php',{...form,csrf_token:''})).status===419,'Category CSRF');
      const invalid=await post(admin,base+'/views/category-add.php',{...form,status:'pending'});
      check(invalid.body.includes('Trạng thái danh mục không hợp lệ'),'Category status validation');
      await admin.locator('[name=name]').fill(name);await admin.locator('[name=slug]').fill(slug);
      await Promise.all([admin.waitForURL('**/views/categories.php'),admin.locator('form[method=POST] button[type=submit]').click()]);
      const cat=fixture('state').categories.find(c=>c.slug===slug);check(!!cat,'Create category via form');
      for(const collision of [{...form,slug:slug+'-other'},{...form,name:name+' other'}]) {
        const r=await post(admin,base+'/views/category-add.php',collision);
        check(r.status===200 && r.body.includes('đã tồn tại') && !r.body.includes('SQLSTATE'),'Duplicate category safe message');
        check(r.body.includes(collision.slug),'Preserve form');
      }
      let r=await post(admin,base+'/views/category-edit.php?id='+cat.id,{...form,name:data.prefix+' active'});
      check(r.status===200 && r.body.includes('đã tồn tại'),'Edit duplicate name');
      r=await post(admin,base+'/views/category-edit.php?id='+cat.id,{...form,slug:run+'-active'});
      check(r.status===200 && r.body.includes('đã tồn tại'),'Edit duplicate slug');
      check((await post(admin,base+'/views/category-edit.php?id='+cat.id,{...form,status:'wrong'})).body.includes('không hợp lệ'),'Edit invalid status');
      check((await post(admin,base+'/views/category-edit.php?id='+cat.id,{...form,csrf_token:''})).status===419,'Edit CSRF');
      check((await post(admin,base+'/views/category-edit.php?id='+cat.id,{...form,name:name+' edited'})).status===302,'Edit valid');
      check((await post(admin,base+'/views/category-delete.php?id='+cat.id,{csrf_token:''})).status===419,'Delete CSRF');
      check((await post(admin,base+'/views/category-delete.php?id='+data.categories.active,{csrf_token:csrf})).body.includes('đang có bài viết'),'Cannot delete used category');
      check((await post(admin,base+'/views/category-delete.php?id='+cat.id,{csrf_token:csrf})).status===302,'Delete empty category');
      if(suffix==='root') {
        await admin.goto(base+'/editor/posts.php?status=published&view='+data.posts.published);
        check(await admin.evaluate(()=>window.qaInjected===undefined),'Editor preview blocks injected HTML');
        check((await admin.textContent('.article-content')).includes('<script>'),'Editor preview shows HTML as text');
        await admin.goto(base+'/admin/binhluan.php?keyword='+encodeURIComponent(data.prefix));
        const button=admin.locator('.btn-duyet[data-id="'+data.comment+'"]');
        check(await button.count()===1,'Pending comment shown');
        const response=admin.waitForResponse(r=>r.url().endsWith('/api/duyet-binh-luan.php')&&r.request().method()==='POST');
        await button.click();check((await (await response).json()).success,'Approve comment UI');
        const api=base+'/admin/api/duyet-binh-luan.php';
        check(fixture('state').comments[0].status==='approved','Approval persisted');
        check((await post(admin,api,{comment_id:data.comment,action:'hide',status:'hidden',csrf_token:''})).status===419,'Comment CSRF');
        for(const [action,status] of [['hide','hidden'],['show','approved']]) {
          check(JSON.parse((await post(admin,api,{comment_id:data.comment,action,status,csrf_token:csrf})).body).success,'Comment '+action);
          check(fixture('state').comments[0].status===status,'Comment persisted '+status);
        }
      }
      console.log('PASS admin/categories/layout:',suffix);
      const readerContext=await browser.newContext();const reader=await readerContext.newPage();await login(reader,base,'reader');
      for(const route of ['/views/categories.php','/views/category-add.php','/views/category-edit.php?id='+data.categories.active,'/views/category-delete.php?id='+data.categories.active,'/admin/binhluan.php']) {
        check((await reader.goto(base+route)).status()===403,'Reader forbidden '+route);
      }
      check((await post(reader,base+'/admin/api/duyet-binh-luan.php',{comment_id:data.comment,action:'delete'})).status===403,'Reader cannot moderate');
      await reader.goto(base+'/bai-viet.php?id='+data.posts.published);
      check(await reader.evaluate(()=>window.qaInjected===undefined),'Escaped article content');
      check((await reader.textContent('.public-content')).includes('<script>'),'HTML shown as text');
      const readerCsrf=await token(reader);
      const initialComments=fixture('state').comments.length;
      let commentResponse=await post(reader,base+'/bai-viet.php?id='+data.posts.published,{comment_submit:'1',content:'',csrf_token:readerCsrf});
      check(commentResponse.status===200 && commentResponse.body.includes('Vui lòng nhập nội dung bình luận.'),'Reject empty comment');
      commentResponse=await post(reader,base+'/bai-viet.php?id='+data.posts.published,{comment_submit:'1',content:'x'.repeat(1001),csrf_token:readerCsrf});
      check(commentResponse.status===200 && commentResponse.body.includes('1.000 ký tự'),'Reject overlong comment');
      check(fixture('state').comments.length===initialComments,'Invalid comments are not stored');
      fixture('lock-reader');
      commentResponse=await post(reader,base+'/bai-viet.php?id='+data.posts.published,{comment_submit:'1',content:data.prefix+' locked',csrf_token:readerCsrf});
      check(commentResponse.status===200 && commentResponse.body.includes('không còn hoạt động'),'Locked session cannot comment');
      check(fixture('state').comments.length===initialComments,'Locked comment is not stored');
      fixture('unlock-reader');
      check(fixture('state').reader_status==='active','Reader test account restored');
      if(suffix==='root') {
        const submitted=data.prefix+' submitted <img src=x onerror=window.commentInjected=1>';
        commentResponse=await post(reader,base+'/bai-viet.php?id='+data.posts.published,{comment_submit:'1',content:submitted,csrf_token:readerCsrf});
        check(commentResponse.status===302,'Valid comment uses PRG redirect');
        const pending=fixture('state').comments.find(c=>c.content===submitted);
        check(!!pending && pending.status==='pending','New comment waits for approval');
        await reader.goto(base+'/bai-viet.php?id='+data.posts.published);
        check((await reader.textContent('body')).includes('Bình luận đã được gửi và đang chờ duyệt.'),'Comment flash shown once');
        check(!(await reader.textContent('.comments-list')).includes(submitted),'Pending comment is private');
        await reader.reload();
        check(!(await reader.textContent('body')).includes('Bình luận đã được gửi và đang chờ duyệt.'),'Comment flash clears after reload');
        check(fixture('state').comments.filter(c=>c.content===submitted).length===1,'Reload does not duplicate comment');
        const adminCsrf=csrf;
        const commentApi=base+'/admin/api/duyet-binh-luan.php';
        check(JSON.parse((await post(admin,commentApi,{comment_id:pending.id,action:'show',status:'approved',csrf_token:adminCsrf})).body).success,'Approve submitted comment');
        await reader.reload();
        check((await reader.textContent('.comments-list')).includes(submitted),'Approved comment becomes public');
        check(await reader.evaluate(()=>window.commentInjected===undefined),'Comment HTML is escaped');
        check(JSON.parse((await post(admin,commentApi,{comment_id:pending.id,action:'hide',status:'hidden',csrf_token:adminCsrf})).body).success,'Hide submitted comment');
        await reader.reload();check(!(await reader.textContent('.comments-list')).includes(submitted),'Hidden comment is not public');
        check(JSON.parse((await post(admin,commentApi,{comment_id:pending.id,action:'delete',csrf_token:adminCsrf})).body).success,'Delete submitted comment');
        check(!fixture('state').comments.some(c=>c.id==pending.id),'Deleted submitted comment absent');
      }
      check((await post(reader,base+'/impact-box-action.php',{action:'add',post_id:data.posts.published,csrf_token:''})).status===419,'Save CSRF');
      // Exercise the visible save dialog and form.
      await reader.locator('.article-save-button').click();
      await reader.locator('.save-modal [name=note]').fill(data.prefix+' note');
      await Promise.all([reader.waitForURL('**/views/impact-box.php'),reader.locator('.save-modal button[type=submit]').click()]);
      check((await reader.textContent('body')).includes(data.prefix+' published'),'Saved article visible');
      check((await reader.locator('.impact-card-image').getAttribute('src')).includes('/assets/images/figma/home-card-1.png'),'Saved article image path');
      await reader.goto(base+'/index.php');
      check((await reader.textContent('#featured')).includes(data.prefix+' published'),'Saved article appears in home Impact Box');
      check((await reader.locator('#featured .home-impact-card').count())===1,'Home Impact Box uses current user data');
      const save=async(action,id,note='')=>post(reader,base+'/impact-box-action.php',{action,post_id:id,note,csrf_token:readerCsrf});
      await save('add',data.posts.published);
      check(fixture('state').saved.filter(r=>r.user_id==data.users.reader.id&&r.post_id==data.posts.published).length===1,'No duplicate saves');
      await save('update_note',data.posts.published,data.prefix+' updated');
      check(fixture('state').saved.find(r=>r.user_id==data.users.reader.id).note===data.prefix+' updated','Note persisted');
      await save('clear_note',data.posts.published);
      check(fixture('state').saved.find(r=>r.user_id==data.users.reader.id).note===null,'Note cleared');
      for(const type of ['draft','pending','hidden']) {
        check((await reader.goto(base+'/bai-viet.php?id='+data.posts[type])).status()===404,'Private article '+type);
        await save('add',data.posts[type]);
        check(!fixture('state').saved.some(r=>r.post_id==data.posts[type]),'Cannot save private '+type);
      }
      const authorContext=await browser.newContext();const author=await authorContext.newPage();await login(author,base,'author');
      await author.goto(base+'/views/impact-box.php');const authorCsrf=await token(author);
      check(!(await author.textContent('body')).includes(data.prefix+' published'),'Saved items isolated');
      await post(author,base+'/impact-box-action.php',{action:'delete',post_id:data.posts.published,csrf_token:authorCsrf});
      check(fixture('state').saved.some(r=>r.user_id==data.users.reader.id),'Cannot remove another user save');
      fixture('hide');
      check((await reader.goto(base+'/bai-viet.php?id='+data.posts.published)).status()===404,'Category hidden detail');
      await reader.goto(base+'/views/impact-box.php');check(!(await reader.textContent('body')).includes(data.prefix+' published'),'Hidden saved article absent');
      await reader.goto(base+'/pages/tim-kiem.php?q='+encodeURIComponent(data.prefix));check(!(await reader.textContent('body')).includes(data.prefix+' published'),'Hidden search absent');
      fixture('unhide');await save('delete',data.posts.published);
      check(!fixture('state').saved.some(r=>r.user_id==data.users.reader.id),'Unsave persisted');
      check((await reader.goto(base+'/bai-viet.php?id='+data.posts.published)).status()===200,'Unsave preserves post');
      await reader.goto(base+'/index.php');await reader.locator('.logout-button').click();
      check(new URL(reader.url()).pathname.endsWith('/dang-nhap.php'),'Logout navigation');
      const guest=await reader.request.get(base+'/views/impact-box.php',{maxRedirects:0});check(guest.status()===302,'Logout invalidates session');
      await authorContext.close();await readerContext.close();await context.close();
      console.log('PASS visibility/save/auth:',suffix);
    }
    // Delete only the fixture comment via the tested moderation endpoint.
    const context=await browser.newContext();const page=await context.newPage();const base='http://127.0.0.1:8021';
    await login(page,base,'admin');const csrf=await token(page);
    check(JSON.parse((await post(page,base+'/admin/api/duyet-binh-luan.php',{comment_id:data.comment,action:'delete',csrf_token:csrf})).body).success,'Delete fixture comment');
    check(fixture('state').comments.length===0,'Deleted comment absent');await context.close();
    console.log('PASS total assertions:',checks);console.log('Screenshots:',artifacts);
  } finally {
    if(browser)await browser.close();for(const server of servers)server.kill();
    if(data) {
      const result=fixture('cleanup');console.log('Cleanup:',JSON.stringify(result));
      check(result.existing_rows_changed.length===0,'Existing rows changed during test; review required');
    }
  }
})().catch(e=>{console.error(e);console.log('Fixture run:',run);process.exitCode=1;});
