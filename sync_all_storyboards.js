const fs = require('fs');
const path = require('path');

const WORKSPACE_DIR = '/Users/sachin/Documents/prompt site';
const DB_PATH = path.join(WORKSPACE_DIR, 'data', 'database.json');
const UPLOADS_DIR = path.join(WORKSPACE_DIR, 'uploads');

if (!fs.existsSync(UPLOADS_DIR)) {
  fs.mkdirSync(UPLOADS_DIR, { recursive: true });
}

async function login() {
  console.log('Logging in to soniprompts.com...');
  const loginUrl = 'https://soniprompts.com/login.php';
  const res = await fetch(loginUrl, { headers: { 'User-Agent': 'Mozilla/5.0' } });
  const html = await res.text();
  const cookies = res.headers.get('set-cookie');
  const csrfMatch = html.match(/name="csrf"\s+value="([^"]+)"/);
  const csrf = csrfMatch ? csrfMatch[1] : null;
  const initCookie = cookies ? cookies.split(';')[0] : '';

  if (!csrf) {
    throw new Error('Failed to extract CSRF token');
  }

  const params = new URLSearchParams();
  params.append('csrf', csrf);
  params.append('next', '/browse.php');
  params.append('email', 'sa812sn@gmail.com');
  params.append('password', 'Kazama#007');

  const loginRes = await fetch(loginUrl, {
    method: 'POST',
    headers: {
      'User-Agent': 'Mozilla/5.0',
      'Content-Type': 'application/x-www-form-urlencoded',
      'Cookie': initCookie,
      'Referer': loginUrl
    },
    body: params.toString(),
    redirect: 'manual'
  });

  const setCookies = loginRes.headers.getSetCookie ? loginRes.headers.getSetCookie() : [loginRes.headers.get('set-cookie')];
  const authCookies = setCookies.map(c => c.split(';')[0]).join('; ');
  console.log('Login successful! Auth cookies acquired.');
  return authCookies;
}

async function downloadFile(url, destPath) {
  if (fs.existsSync(destPath) && fs.statSync(destPath).size > 1000) {
    return true;
  }
  try {
    const res = await fetch(url, { headers: { 'User-Agent': 'Mozilla/5.0' } });
    if (!res.ok) {
      console.warn(`Failed to download ${url}: HTTP ${res.status}`);
      return false;
    }
    const buf = Buffer.from(await res.arrayBuffer());
    fs.writeFileSync(destPath, buf);
    return true;
  } catch (err) {
    console.error(`Error downloading ${url}:`, err.message);
    return false;
  }
}

async function fetchStoryboardImagesForPrompt(id, authCookies) {
  const url = `https://soniprompts.com/prompt.php?id=${id}`;
  try {
    const res = await fetch(url, {
      headers: {
        'User-Agent': 'Mozilla/5.0',
        'Cookie': authCookies
      }
    });
    if (res.status !== 200) return [];
    const html = await res.text();

    const startIdx = html.indexOf('class="prompt-gallery"');
    if (startIdx === -1) return [];

    const afterStart = html.slice(startIdx);
    const endIdx1 = afterStart.indexOf('class="prompt-content"');
    const endIdx2 = afterStart.indexOf('class="copy-bar"');
    const endIdx3 = afterStart.indexOf('class="prompt-body"');

    let endIdx = Math.min(
      endIdx1 !== -1 ? endIdx1 : 999999,
      endIdx2 !== -1 ? endIdx2 : 999999,
      endIdx3 !== -1 ? endIdx3 : 999999
    );
    if (endIdx === 999999) endIdx = 8000;

    const galleryHtml = afterStart.slice(0, endIdx);
    const imgMatches = [...galleryHtml.matchAll(/<img[^>]+src="([^">]+)"/gi)];

    const remoteUrls = [];
    for (const m of imgMatches) {
      let src = m[1].trim();
      if (!src) continue;
      if (src.startsWith('/')) src = `https://soniprompts.com${src}`;
      else if (!src.startsWith('http')) src = `https://soniprompts.com/${src}`;
      if (!remoteUrls.includes(src)) {
        remoteUrls.push(src);
      }
    }

    return remoteUrls;
  } catch (err) {
    console.error(`Error fetching prompt ${id}:`, err.message);
    return [];
  }
}

async function main() {
  const authCookies = await login();
  const db = JSON.parse(fs.readFileSync(DB_PATH, 'utf8'));

  const promptMap = new Map();
  for (const p of db.prompts) {
    promptMap.set(String(p.id), p);
    if (p.numericId) promptMap.set(String(p.numericId), p);
  }

  console.log(`Checking storyboard images across prompts 1 to 288...`);

  const MAX_ID = 288;
  const CONCURRENCY = 8;
  const multiImagePrompts = [];
  let totalStoryboardImagesCount = 0;
  let totalPromptsWithImages = 0;

  for (let start = 1; start <= MAX_ID; start += CONCURRENCY) {
    const end = Math.min(start + CONCURRENCY - 1, MAX_ID);
    const batch = [];
    for (let id = start; id <= end; id++) {
      batch.push((async () => {
        const remoteUrls = await fetchStoryboardImagesForPrompt(id, authCookies);
        if (remoteUrls.length === 0) return { id, localPaths: [] };

        const localPaths = [];
        for (let i = 0; i < remoteUrls.length; i++) {
          const remoteUrl = remoteUrls[i];
          const extMatch = remoteUrl.match(/\.(png|jpg|jpeg|webp)/i);
          const ext = extMatch ? extMatch[1].toLowerCase() : 'png';
          
          let filename;
          if (remoteUrls.length === 1) {
            filename = `storyboard_${id}.${ext}`;
          } else {
            filename = `storyboard_${id}_${i + 1}.${ext}`;
          }
          const localDest = path.join(UPLOADS_DIR, filename);
          const ok = await downloadFile(remoteUrl, localDest);
          if (ok) {
            localPaths.push(`/uploads/${filename}`);
          }
        }
        return { id, localPaths };
      })());
    }

    const batchResults = await Promise.all(batch);
    for (const res of batchResults) {
      if (res.localPaths.length > 0) {
        totalPromptsWithImages++;
        totalStoryboardImagesCount += res.localPaths.length;
        if (res.localPaths.length > 1) {
          multiImagePrompts.push({ id: res.id, count: res.localPaths.length });
        }
        const p = promptMap.get(String(res.id));
        if (p) {
          p.storyboardImages = res.localPaths;
        }
      } else {
        const p = promptMap.get(String(res.id));
        if (p && !p.storyboardImages) {
          p.storyboardImages = [];
        }
      }
    }

    process.stdout.write(`Checked up to ID ${end}/${MAX_ID} (Found ${totalStoryboardImagesCount} images in ${totalPromptsWithImages} prompts)...\r`);
  }

  console.log('\n\n--- Scraping Summary ---');
  console.log(`Total Prompts with Storyboard Images: ${totalPromptsWithImages}`);
  console.log(`Total Storyboard Images Downloaded: ${totalStoryboardImagesCount}`);
  console.log(`Prompts with MULTIPLE Storyboard Images (${multiImagePrompts.length}):`);
  multiImagePrompts.forEach(item => {
    console.log(`  - Prompt #${item.id}: ${item.count} images`);
  });

  // Save database
  fs.writeFileSync(DB_PATH, JSON.stringify(db, null, 2), 'utf8');
  console.log(`\nUpdated ${DB_PATH} successfully!`);
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
