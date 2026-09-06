require('dotenv').config();
const express = require('express');
const cors = require('cors');
const fs = require('fs');
const path = require('path');
const multer = require('multer');
const { uploadToGitHub } = require('./lib/githubStorage');
const {
  registerCloudUser,
  loginCloudUser,
  getCloudUserProfile,
  listCloudUsers,
  updateCloudUserSubscription,
  deleteCloudUser
} = require('./lib/supabaseAuth');

const app = express();
const PORT = process.env.PORT || 3000;
const DB_FILE = path.join(__dirname, 'data', 'database.json');

// Middleware
app.use(cors());
app.use(express.json({ limit: '15mb' }));
app.use(express.urlencoded({ extended: true, limit: '15mb' }));

// Redirect any .html requests to .php
app.use((req, res, next) => {
  if (req.path && req.path.endsWith('.html')) {
    const target = req.path.replace(/\.html$/, '.php');
    const qs = req.url.includes('?') ? req.url.slice(req.url.indexOf('?')) : '';
    return res.redirect(301, target + qs);
  }
  next();
});

// Intercept .php requests and render as HTML with includes
app.get(/.*\.php$/, (req, res, next) => {
  const base = path.basename(req.path, '.php');
  const target = path.join(__dirname, 'public', `${base}.php`);
  if (fs.existsSync(target)) {
    const rendered = renderPhpFile(target);
    if (rendered) {
      return res.type('html').send(rendered);
    }
  }
  next();
});

// Static Folders (CSS, JS, images, uploads)
app.use(express.static(path.join(__dirname, 'public')));
app.use('/uploads', express.static(path.join(__dirname, 'public', 'uploads')));
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Configure Multer for thumbnail uploads
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const uploadDir = path.join(__dirname, 'public', 'uploads');
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    cb(null, uploadDir);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    const ext = path.extname(file.originalname) || '.jpg';
    cb(null, 'thumb-' + uniqueSuffix + ext);
  }
});
const upload = multer({ storage: storage });

// Database Helper Functions with Vercel Serverless /tmp Support
const TMP_DB_FILE = path.join('/tmp', 'database.json');

function getActiveDbPath() {
  if (process.env.VERCEL) {
    if (fs.existsSync(TMP_DB_FILE)) {
      return TMP_DB_FILE;
    }
  }
  return DB_FILE;
}

function readDatabase() {
  try {
    const activePath = getActiveDbPath();
    if (!fs.existsSync(activePath)) {
      if (fs.existsSync(DB_FILE)) {
        const data = fs.readFileSync(DB_FILE, 'utf8');
        if (process.env.VERCEL) {
          try { fs.writeFileSync(TMP_DB_FILE, data, 'utf8'); } catch (e) {}
        }
        return JSON.parse(data);
      }
      return { admin: { username: 'admin', password: 'admin123' }, categories: [], prompts: [] };
    }
    const data = fs.readFileSync(activePath, 'utf8');
    return JSON.parse(data);
  } catch (err) {
    console.error('Error reading database:', err);
    try {
      if (fs.existsSync(DB_FILE)) {
        return JSON.parse(fs.readFileSync(DB_FILE, 'utf8'));
      }
    } catch (e) {}
    return { admin: { username: 'admin', password: 'admin123' }, categories: [], prompts: [] };
  }
}

function writeDatabase(db) {
  const jsonStr = JSON.stringify(db, null, 2);
  // If running on Vercel, write to /tmp/database.json
  if (process.env.VERCEL) {
    try {
      fs.writeFileSync(TMP_DB_FILE, jsonStr, 'utf8');
      return true;
    } catch (err) {
      console.error('Error writing database to /tmp on Vercel:', err);
    }
  }
  // Standard local write
  try {
    fs.writeFileSync(DB_FILE, jsonStr, 'utf8');
    return true;
  } catch (err) {
    // If read-only filesystem (e.g. Lambda/Vercel without VERCEL env), fallback to /tmp
    if (err.code === 'EROFS') {
      try {
        fs.writeFileSync(TMP_DB_FILE, jsonStr, 'utf8');
        return true;
      } catch (tmpErr) {
        console.error('Error writing fallback to /tmp:', tmpErr);
      }
    }
    console.error('Error writing database:', err);
    return false;
  }
}

// ─── User Subscription Evaluator & Token Helpers ───

function generateUserToken(userId) {
  return Buffer.from(`${userId}:::${Date.now()}`).toString('base64');
}

function getUserIdFromToken(token) {
  if (!token || typeof token !== 'string') return null;
  const cleanToken = token.trim();
  try {
    // Format 1: base64 encoded "userId:::timestamp"
    const decoded = Buffer.from(cleanToken, 'base64').toString('utf8');
    if (decoded.includes(':::')) {
      const parts = decoded.split(':::');
      if (parts[0]) return parts[0];
    }
    // Format 2: legacy "usr_<b64>_<timestamp>"
    if (cleanToken.startsWith('usr_')) {
      const lastUnderscore = cleanToken.lastIndexOf('_');
      if (lastUnderscore > 4) {
        const b64 = cleanToken.substring(4, lastUnderscore);
        const decodedLegacy = Buffer.from(b64, 'base64').toString('utf8');
        if (decodedLegacy) return decodedLegacy;
      }
    }
    // Format 3: direct userId if token equals user ID
    return cleanToken;
  } catch (e) {
    return cleanToken;
  }
}

function formatDateDayMonthYear(d) {
  if (!d) return 'N/A';
  const date = new Date(d);
  if (isNaN(date.getTime())) return 'N/A';
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

function evaluateSubscription(user) {
  if (!user.subscription || user.subscription.status !== 'active') {
    const isExp = Boolean(user.subscription && user.subscription.status === 'expired');
    return {
      status: isExp ? 'expired' : 'inactive',
      isActive: false,
      isExpired: isExp,
      daysLeft: 0,
      daysElapsed: (user.subscription && user.subscription.grantedAt)
        ? Math.max(0, Math.floor((Date.now() - new Date(user.subscription.grantedAt).getTime()) / (1000 * 60 * 60 * 24)))
        : 0,
      expiresAtFormatted: (user.subscription && user.subscription.expiresAt)
        ? formatDateDayMonthYear(user.subscription.expiresAt)
        : 'N/A',
      grantedAtFormatted: (user.subscription && user.subscription.grantedAt)
        ? formatDateDayMonthYear(user.subscription.grantedAt)
        : 'N/A'
    };
  }

  const now = Date.now();
  const expiresTime = new Date(user.subscription.expiresAt).getTime();
  const grantedTime = new Date(user.subscription.grantedAt || now).getTime();

  // Automatic Expiry Check: If current time is past expiry date
  if (now > expiresTime) {
    user.subscription.status = 'expired';
    const elapsed = Math.max(0, Math.floor((now - grantedTime) / (1000 * 60 * 60 * 24)));
    return {
      status: 'expired',
      isActive: false,
      isExpired: true,
      daysLeft: 0,
      daysElapsed: elapsed,
      expiresAtFormatted: formatDateDayMonthYear(expiresTime),
      grantedAtFormatted: formatDateDayMonthYear(grantedTime)
    };
  }

  const msLeft = expiresTime - now;
  const daysLeft = Math.ceil(msLeft / (1000 * 60 * 60 * 24));
  const daysElapsed = Math.max(0, Math.floor((now - grantedTime) / (1000 * 60 * 60 * 24)));

  return {
    status: 'active',
    isActive: true,
    isExpired: false,
    daysLeft,
    daysElapsed,
    expiresAtFormatted: formatDateDayMonthYear(expiresTime),
    grantedAtFormatted: formatDateDayMonthYear(grantedTime)
  };
}

function sanitizeUser(user) {
  const subInfo = evaluateSubscription(user);
  return {
    id: user.id,
    name: user.name,
    email: user.email,
    createdAt: user.createdAt,
    createdAtFormatted: formatDateDayMonthYear(user.createdAt),
    subscription: {
      ...user.subscription,
      ...subInfo
    }
  };
}

// ─── Authentication API Routes (Supabase Cloud Auth) ───

// Register New User (Cloud Stored)
app.post('/api/auth/register', async (req, res) => {
  const { name, email, password } = req.body;
  if (!name || !email || !password) {
    return res.status(400).json({ success: false, message: 'Name, email, and password are required' });
  }

  try {
    const result = await registerCloudUser(name, email, password);
    res.json({
      success: true,
      message: 'Account created successfully in cloud',
      token: result.token,
      user: result.user
    });
  } catch (err) {
    console.error('[Cloud Register Error]:', err.message);
    let msg = err.message || 'Registration failed';
    if (msg.toLowerCase().includes('already registered')) {
      msg = 'An account with this email already exists';
    }
    res.status(400).json({ success: false, message: msg });
  }
});

// Login User (Cloud Stored)
app.post('/api/auth/login', async (req, res) => {
  const { email, password } = req.body;
  if (!email || !password) {
    return res.status(400).json({ success: false, message: 'Email and password are required' });
  }

  try {
    const result = await loginCloudUser(email, password);
    res.json({
      success: true,
      message: 'Logged in successfully',
      token: result.token,
      user: result.user
    });
  } catch (err) {
    console.error('[Cloud Login Error]:', err.message);
    res.status(401).json({ success: false, message: 'Invalid email or password' });
  }
});

// Current User Profile (Me) (Cloud Validated)
app.get('/api/auth/me', async (req, res) => {
  const authHeader = req.headers.authorization || '';
  const token = authHeader.replace('Bearer ', '').trim() || (req.query.token ? req.query.token.trim() : '');
  
  if (!token) {
    return res.status(401).json({ success: false, message: 'Unauthorized / token missing' });
  }

  try {
    const user = await getCloudUserProfile(token);
    if (!user) {
      return res.status(404).json({ success: false, message: 'User not found or session expired' });
    }

    res.json({
      success: true,
      user
    });
  } catch (err) {
    console.error('[Cloud Me Error]:', err.message);
    res.status(401).json({ success: false, message: 'Invalid token' });
  }
});

// ─── API Routes ───

// 1. Get Prompts (with filter by type, category, and search)
app.get('/api/prompts', (req, res) => {
  const db = readDatabase();
  let prompts = db.prompts || [];

  const { category, type, search, sort } = req.query;

  // Filter by Type (free / premium)
  if (type && type !== 'all') {
    prompts = prompts.filter(p => p.type === type);
  }

  // Filter by Category
  if (category && category !== 'all') {
    prompts = prompts.filter(p => p.category.toLowerCase() === category.toLowerCase());
  }

  // Search by title, summary, master prompt
  if (search) {
    const q = search.toLowerCase();
    prompts = prompts.filter(p =>
      (p.title && p.title.toLowerCase().includes(q)) ||
      (p.category && p.category.toLowerCase().includes(q)) ||
      (p.summary && p.summary.toLowerCase().includes(q))
    );
  }

  // Sort
  if (sort === 'new' || !sort) {
    prompts.sort((a, b) => (b.numericId || 0) - (a.numericId || 0));
  } else if (sort === 'old') {
    prompts.sort((a, b) => (a.numericId || 0) - (b.numericId || 0));
  } else if (sort === 'az') {
    prompts.sort((a, b) => a.title.localeCompare(b.title));
  } else if (sort === 'za') {
    prompts.sort((a, b) => b.title.localeCompare(a.title));
  }

  res.json({
    success: true,
    total: prompts.length,
    prompts: prompts
  });
});

// 2. Get Single Prompt (Supports Login & VIP unlock check)
app.get('/api/prompts/:id', async (req, res) => {
  const db = readDatabase();
  const prompt = (db.prompts || []).find(p => p.id === req.params.id || String(p.numericId) === req.params.id);
  if (!prompt) {
    return res.status(404).json({ success: false, message: 'Prompt not found' });
  }

  // Check user token for Login & VIP status
  let isLoggedIn = false;
  let isVip = false;
  const authHeader = req.headers.authorization || '';
  const token = authHeader.replace('Bearer ', '').trim() || (req.query.token ? req.query.token.trim() : '');
  if (token) {
    try {
      const cloudUser = await getCloudUserProfile(token);
      if (cloudUser) {
        isLoggedIn = true;
        if (cloudUser.subscription && cloudUser.subscription.isActive) {
          isVip = true;
        }
      }
    } catch (e) {
      // Fallback to local DB check
      const userId = getUserIdFromToken(token);
      let user = null;
      if (userId) {
        user = (db.users || []).find(u => u.id === userId || u.email.toLowerCase() === userId.toLowerCase());
      }
      if (!user) {
        user = (db.users || []).find(u => u.id === token || u.email.toLowerCase() === token.toLowerCase());
      }
      if (user) {
        isLoggedIn = true;
        const sub = evaluateSubscription(user);
        if (sub.isActive) {
          isVip = true;
        }
      }
    }
  }

  // Free prompt is unlocked only if user is logged in!
  // Premium prompt is unlocked only if user has active VIP!
  const isUnlocked = prompt.type === 'free' ? isLoggedIn : isVip;

  res.json({
    success: true,
    prompt: {
      ...prompt,
      // If unlocked, deliver full master prompt & negative prompt; otherwise keep protected
      masterPrompt: isUnlocked ? prompt.masterPrompt : null,
      negativePrompt: isUnlocked ? prompt.negativePrompt : null,
      isLoggedIn,
      isVip,
      isUnlocked,
      isVipUnlocked: isVip
    }
  });
});

// ─── Robust PHP Page Template Renderer ───
function renderPhpFile(filePath, context = {}) {
  if (!fs.existsSync(filePath)) return null;
  let rawContent = fs.readFileSync(filePath, 'utf8');

  // Extract page variables
  let pageTitle = context.pageTitle || 'VIRAL PROMPT — Viral AI Video Prompts';
  let pageDesc = context.pageDesc || 'Viral AI video prompts for Reels, Shorts & TikTok.';
  let activePage = 'home';

  const titleMatch = rawContent.match(/\$pageTitle\s*=\s*(?:\$SITE_NAME\s*\.\s*)?['"]([^'"]+)['"];/);
  if (titleMatch) {
    pageTitle = rawContent.includes('$SITE_NAME .') ? 'VIRAL PROMPT' + titleMatch[1] : titleMatch[1];
  }

  const descMatch = rawContent.match(/\$pageDesc\s*=\s*['"]([^'"]+)['"];/);
  if (descMatch) pageDesc = descMatch[1];

  const pageMatch = rawContent.match(/\$activePage\s*=\s*['"]([^'"]+)['"];/);
  if (pageMatch) activePage = pageMatch[1];

  let extraHead = '';
  const extraHeadMatch = rawContent.match(/\$extraHead\s*=\s*'([\s\S]*?)';/);
  if (extraHeadMatch) {
    extraHead = extraHeadMatch[1];
  } else {
    extraHead = `
    <style>
      .btn-subscribe-premium {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #6d5dfc;
        color: #ffffff !important;
        font-size: 15.5px;
        font-weight: 600;
        padding: 13px 44px;
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(109, 93, 252, 0.35);
        border: none;
        cursor: pointer;
        margin: 0 auto;
        align-self: center;
      }
      .btn-subscribe-premium:hover {
        background: #5b4af7;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(109, 93, 252, 0.45);
      }
    </style>`;
  }

  let extraScripts = '';
  const extraScriptsMatch = rawContent.match(/\$extraScripts\s*=\s*'([\s\S]*?)';/);
  if (extraScriptsMatch) extraScripts = extraScriptsMatch[1];

  // Load and render header and footer templates
  const headerPath = path.join(__dirname, 'public', 'includes', 'header.php');
  const footerPath = path.join(__dirname, 'public', 'includes', 'footer.php');
  let headerContent = fs.existsSync(headerPath) ? fs.readFileSync(headerPath, 'utf8') : '';
  let footerContent = fs.existsSync(footerPath) ? fs.readFileSync(footerPath, 'utf8') : '';

  headerContent = headerContent.replace(/<\?php[\s\S]*?\?>/g, (phpBlock) => {
    if (phpBlock.includes('echo')) {
      if (phpBlock.includes('$pageTitle')) return pageTitle;
      if (phpBlock.includes('$pageDesc')) return pageDesc;
      if (phpBlock.includes('$SITE_NAME')) return 'VIRAL PROMPT';
      if (phpBlock.includes("'home'")) return activePage === 'home' ? 'active' : '';
      if (phpBlock.includes("'browse'")) return activePage === 'browse' ? 'active' : '';
      if (phpBlock.includes("'community'")) return activePage === 'community' ? 'active' : '';
      if (phpBlock.includes("'pricing'")) return activePage === 'pricing' ? 'active' : '';
      if (phpBlock.includes('$extraHead')) return extraHead;
    }
    return '';
  });

  footerContent = footerContent.replace(/<\?php[\s\S]*?\?>/g, (phpBlock) => {
    if (phpBlock.includes('echo')) {
      if (phpBlock.includes('$SITE_NAME')) return 'VIRAL PROMPT';
      if (phpBlock.includes('$FACEBOOK_URL')) return 'https://www.facebook.com';
      if (phpBlock.includes('$WHATSAPP_CHANNEL')) return 'https://whatsapp.com/channel/0029VbCl6nB002TFkWMBP43S';
      if (phpBlock.includes('$CURRENT_YEAR')) return '2026';
      if (phpBlock.includes('$extraScripts')) return extraScripts;
    }
    return '';
  });

  // Replace inline template variables in raw content first
  rawContent = rawContent.replace(/<\?php\s+echo\s+htmlspecialchars\(\$SITE_NAME\);?\s*\?>/g, 'VIRAL PROMPT');
  rawContent = rawContent.replace(/<\?php\s+echo\s+\$CURRENT_YEAR;?\\s*\?>/g, '2026');

  // Safely remove only the top header include and bottom footer include
  let bodyContent = rawContent.replace(/^\s*<\?php[\s\S]*?includes\/header\.php['"];?\s*\?>/i, '');
  bodyContent = bodyContent.replace(/<\?php\s*(?:include|require|include_once|require_once)[^?]*?includes\/footer\.php['"];?\s*\?>/i, '');
  // Strip any remaining PHP code in body
  bodyContent = bodyContent.replace(/<\?php[\s\S]*?\?>/g, '');

  return headerContent.trim() + '\n' + bodyContent.trim() + '\n' + footerContent.trim();
}

function sendPhpOrHtml(req, res, baseName) {
  const phpPath = path.join(__dirname, 'public', `${baseName}.php`);
  if (fs.existsSync(phpPath)) {
    const rendered = renderPhpFile(phpPath);
    if (rendered) {
      return res.type('html').send(rendered);
    }
  }
  const htmlPath = path.join(__dirname, 'public', `${baseName}.html`);
  if (fs.existsSync(htmlPath)) {
    return res.sendFile(htmlPath);
  }
  res.status(404).send('Page not found');
}


// Page Routes supporting .php and clean URLs
app.get(['/', '/index', '/index.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'index');
});

app.get(['/prompt', '/prompt.html', '/prompt.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'prompt');
});

app.get(['/browse', '/browse.html', '/browse.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'browse');
});

app.get(['/account', '/account.html', '/account.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'account');
});

app.get(['/pricing', '/pricing.html', '/pricing.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'pricing');
});

app.get(['/community', '/community.html', '/community.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'community');
});

app.get(['/contact', '/contact.html', '/contact.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'contact');
});

app.get(['/privacy', '/privacy.html', '/privacy.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'privacy');
});

app.get(['/terms', '/terms.html', '/terms.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'terms');
});

app.get(['/refunds', '/refunds.html', '/refunds.php', '/refund.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'refunds');
});

app.get(['/login', '/login.html', '/login.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'login');
});

app.get(['/register', '/register.html', '/register.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'register');
});

app.get(['/admin', '/admin.html', '/admin.php'], (req, res) => {
  sendPhpOrHtml(req, res, 'admin');
});

app.get('/logout.php', (req, res) => {
  res.redirect('/login.php?logout=1');
});

// Generic catch-all for any requested .php file in public
app.get(/.*\.php$/, (req, res, next) => {
  const baseName = req.path.replace(/^\//, '').replace(/\.php$/, '');
  const phpPath = path.join(__dirname, 'public', `${baseName}.php`);
  if (fs.existsSync(phpPath)) {
    return sendPhpOrHtml(req, res, baseName);
  }
  next();
});

// ─── Community API Routes ───

function getSeedCommunityPosts() {
  return [
    {
      id: 'post-8',
      userId: 'usr_creator',
      author: 'VIRAL PROMPT',
      authorAvatar: '⚡',
      isAdmin: true,
      time: '30 Aug 2026 · 07:01',
      content: '## MASTER PROMPT USE GUIDE\n\nA Master Prompt is not a normal one-time prompt. It is a complete reusable AI system where the character, camera style, story DNA, duration, audio, continuity, negative rules, and output format are already defined.\n\n**Simple workflow:**\n\n1. Open a new ChatGPT or Claude chat and paste the complete Master Prompt.\n2. Type: **“Give me 10 topics.”**\n3. Select your favorite idea: **“Topic 4.”**\n4. Then type: **“Make full prompt.”**\n5. For changes, do not paste the full Master Prompt again. Simply say: **“Keep everything the same, change the location,” “make the hook stronger,” “make it 15 seconds,” “make the ending funnier,” or “keep the same character.”**\n6. For more ideas, type: **“Give me 10 more topics”** or **“Next video.”**\n\nThink of the Master Prompt as your permanent **Creative Director + Script Writer + Camera Director + Prompt Engineer.**\n\n**The Master Prompt defines HOW to create.\nYour next command defines WHAT to create.**\n\nFor a new chat, paste the Master Prompt again for the safest workflow.',
      likes: 5,
      likedBy: [],
      comments: [
        { author: 'John', text: 'Hello bhai iske jaise promte bna do aur page name Look in History' }
      ]
    },
    {
      id: 'post-5',
      userId: 'usr_arman',
      author: 'Arman',
      authorAvatar: 'A',
      isAdmin: false,
      time: '22 Aug 2026 · 07:52',
      content: 'post viral nahi ho raha keya karu bhai?',
      likes: 3,
      likedBy: [],
      comments: []
    },
    {
      id: 'post-4',
      userId: 'usr_gobango',
      author: 'Gobango',
      authorAvatar: 'G',
      isAdmin: false,
      time: '22 Aug 2026 · 05:30',
      content: 'story board ko kaisay use krein kisi bhi prompt say video generation ky liye',
      likes: 1,
      likedBy: [],
      comments: []
    }
  ];
}

// 1. Get Community Posts
app.get('/api/community/posts', (req, res) => {
  const db = readDatabase();
  if (!db.communityPosts || !db.communityPosts.length) {
    db.communityPosts = getSeedCommunityPosts();
    writeDatabase(db);
  }
  res.json({ success: true, posts: db.communityPosts });
});

// 2. Create Community Post (Authenticated Users)
app.post('/api/community/posts', upload.single('media'), async (req, res) => {
  const db = readDatabase();
  db.users = db.users || [];
  if (!db.communityPosts || !db.communityPosts.length) {
    db.communityPosts = getSeedCommunityPosts();
  }

  const authHeader = req.headers.authorization || '';
  const token = authHeader.replace('Bearer ', '').trim() || (req.body.token ? req.body.token.trim() : '') || (req.query.token ? req.query.token.trim() : '');

  if (!token) {
    return res.status(401).json({ success: false, message: 'Please login to post in the community.' });
  }

  let user = await getCloudUserProfile(token);
  if (!user) {
    const userId = getUserIdFromToken(token);
    if (userId) {
      user = db.users.find(u => u.id === userId || u.email.toLowerCase() === userId.toLowerCase());
    }
  }

  if (!user) {
    return res.status(401).json({ success: false, message: 'Please login to post in the community.' });
  }

  const content = (req.body.content || '').trim();
  if (!content && !req.file) {
    return res.status(400).json({ success: false, message: 'Post content or media attachment is required.' });
  }

  const now = new Date();
  const timeStr = formatDateDayMonthYear(now) + ' · ' + now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });

  let mediaUrl = null;
  let mediaType = null;
  if (req.file) {
    mediaUrl = '/uploads/' + req.file.filename;
    mediaType = req.file.mimetype.startsWith('video') ? 'video' : 'image';
    try {
      const fileBuf = fs.readFileSync(req.file.path);
      uploadToGitHub(fileBuf, req.file.originalname || req.file.filename, req.file.mimetype)
        .then(ghUrl => { if (ghUrl) newPost.mediaUrl = ghUrl; })
        .catch(e => console.error('[GitHub Post Media Error]:', e.message));
    } catch (e) {}
  }

  const newPost = {
    id: 'post-' + Date.now(),
    userId: user.id,
    author: user.name || 'Community Member',
    authorAvatar: (user.name || 'U').trim().charAt(0).toUpperCase(),
    isAdmin: user.email === 'admin@promptmaster.com' || user.email === 'sa812sn@gmail.com' || user.role === 'admin',
    time: timeStr,
    content: content,
    mediaUrl: mediaUrl,
    mediaType: mediaType,
    likes: 0,
    likedBy: [],
    comments: []
  };

  db.communityPosts.unshift(newPost);
  writeDatabase(db);

  res.json({ success: true, post: newPost });
});

// 3. Like Post
app.post('/api/community/posts/:id/like', (req, res) => {
  const db = readDatabase();
  db.communityPosts = db.communityPosts || getSeedCommunityPosts();
  const post = db.communityPosts.find(p => p.id === req.params.id);
  if (!post) {
    return res.status(404).json({ success: false, message: 'Post not found' });
  }
  post.likes = (post.likes || 0) + 1;
  writeDatabase(db);
  res.json({ success: true, likes: post.likes });
});

// 4. Add Comment
app.post('/api/community/posts/:id/comment', (req, res) => {
  const db = readDatabase();
  db.communityPosts = db.communityPosts || getSeedCommunityPosts();
  const post = db.communityPosts.find(p => p.id === req.params.id);
  if (!post) {
    return res.status(404).json({ success: false, message: 'Post not found' });
  }
  const { author, text } = req.body;
  if (!text || !text.trim()) {
    return res.status(400).json({ success: false, message: 'Comment text is required' });
  }
  post.comments = post.comments || [];
  post.comments.push({
    author: author || 'Member',
    text: text.trim(),
    time: formatDateDayMonthYear(new Date())
  });
  writeDatabase(db);
  res.json({ success: true, comments: post.comments });
});



// 3. Get Categories with accurate counts
app.get('/api/categories', (req, res) => {
  const db = readDatabase();
  const categories = db.categories || [];
  const prompts = db.prompts || [];

  // Calculate live count per category
  const categoriesWithCounts = categories.map(cat => {
    const count = prompts.filter(p => p.category.toLowerCase() === cat.name.toLowerCase()).length;
    return { ...cat, count };
  });

  res.json({
    success: true,
    totalPrompts: prompts.length,
    categories: categoriesWithCounts
  });
});

// 4. Admin Login
app.post('/api/admin/login', (req, res) => {
  const { username, password } = req.body;
  const db = readDatabase();
  const admin = db.admin || { username: 'admin', password: 'admin123' };

  if (username === admin.username && password === admin.password) {
    return res.json({
      success: true,
      message: 'Logged in successfully',
      token: 'admin-auth-token-valid'
    });
  }
  return res.status(401).json({ success: false, message: 'Invalid username or password' });
});

// 5. Admin Upload Thumbnail
app.post('/api/admin/upload', upload.single('thumbnail'), async (req, res) => {
  if (!req.file) {
    return res.status(400).json({ success: false, message: 'No file uploaded' });
  }
  let fileUrl = '/uploads/' + req.file.filename;
  try {
    const fileBuf = fs.readFileSync(req.file.path);
    const ghUrl = await uploadToGitHub(fileBuf, req.file.originalname || req.file.filename, req.file.mimetype);
    if (ghUrl) {
      fileUrl = ghUrl;
    }
  } catch (err) {
    console.error('[GitHub Upload Error]:', err.message);
  }
  res.json({ success: true, url: fileUrl });
});

// 6. Admin Add New Prompt
app.post('/api/admin/prompts', (req, res) => {
  const db = readDatabase();
  const { title, category, type, promptCountBadge, thumbnail, summary, masterPrompt, negativePrompt, tools } = req.body;

  if (!title || !category || !masterPrompt) {
    return res.status(400).json({ success: false, message: 'Title, category, and master prompt are required' });
  }

  // Calculate next numeric ID
  const maxId = (db.prompts || []).reduce((max, p) => Math.max(max, p.numericId || 0), 284);
  const nextId = maxId + 1;

  const newPrompt = {
    id: String(nextId),
    numericId: nextId,
    title: title.trim(),
    category: category.trim(),
    type: type === 'free' ? 'free' : 'premium',
    promptCountBadge: promptCountBadge || (type === 'free' ? '🗂 1 prompt' : `🗂 ${category}`),
    thumbnail: thumbnail || 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&auto=format&fit=crop&q=80',
    summary: summary ? summary.trim() : 'Viral AI video engine system ready to copy and paste.',
    masterPrompt: masterPrompt.trim(),
    negativePrompt: negativePrompt ? negativePrompt.trim() : 'cartoon, low quality, jitter, blurred objects',
    tools: Array.isArray(tools) ? tools : ['Kling AI', 'Seedance', 'Runway Gen-3'],
    views: 0,
    likes: 0,
    updatedDate: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
  };

  db.prompts.unshift(newPrompt);

  // Auto-add category if it doesn't exist yet
  const catExists = (db.categories || []).some(c => c.name.toLowerCase() === category.trim().toLowerCase());
  if (!catExists) {
    const nextCatId = (db.categories || []).length + 1;
    db.categories.push({ id: nextCatId, name: category.trim(), count: 1 });
  }

  writeDatabase(db);
  res.json({ success: true, message: 'Prompt added successfully', prompt: newPrompt });
});

// 7. Admin Edit Existing Prompt
app.put('/api/admin/prompts/:id', (req, res) => {
  const db = readDatabase();
  const index = (db.prompts || []).findIndex(p => p.id === req.params.id || String(p.numericId) === req.params.id);

  if (index === -1) {
    return res.status(404).json({ success: false, message: 'Prompt not found' });
  }

  const existing = db.prompts[index];
  const { title, category, type, promptCountBadge, thumbnail, summary, masterPrompt, negativePrompt, tools } = req.body;

  db.prompts[index] = {
    ...existing,
    title: title !== undefined ? title.trim() : existing.title,
    category: category !== undefined ? category.trim() : existing.category,
    type: type !== undefined ? type : existing.type,
    promptCountBadge: promptCountBadge !== undefined ? promptCountBadge : existing.promptCountBadge,
    thumbnail: thumbnail !== undefined ? thumbnail : existing.thumbnail,
    summary: summary !== undefined ? summary.trim() : existing.summary,
    masterPrompt: masterPrompt !== undefined ? masterPrompt.trim() : existing.masterPrompt,
    negativePrompt: negativePrompt !== undefined ? negativePrompt.trim() : existing.negativePrompt,
    tools: tools !== undefined ? tools : existing.tools,
    updatedDate: new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
  };

  writeDatabase(db);
  res.json({ success: true, message: 'Prompt updated successfully', prompt: db.prompts[index] });
});

// 8. Admin Delete Prompt
app.delete('/api/admin/prompts/:id', (req, res) => {
  const db = readDatabase();
  const initialLength = (db.prompts || []).length;
  db.prompts = (db.prompts || []).filter(p => p.id !== req.params.id && String(p.numericId) !== req.params.id);

  if (db.prompts.length === initialLength) {
    return res.status(404).json({ success: false, message: 'Prompt not found' });
  }

  writeDatabase(db);
  res.json({ success: true, message: 'Prompt deleted successfully' });
});

// 9. Admin Add New Category
app.post('/api/admin/categories', (req, res) => {
  const db = readDatabase();
  const { name } = req.body;
  if (!name || !name.trim()) {
    return res.status(400).json({ success: false, message: 'Category name is required' });
  }

  const exists = (db.categories || []).some(c => c.name.toLowerCase() === name.trim().toLowerCase());
  if (exists) {
    return res.status(400).json({ success: false, message: 'Category already exists' });
  }

  const nextId = (db.categories || []).length + 1;
  const newCat = { id: nextId, name: name.trim(), count: 0 };
  db.categories.push(newCat);
  writeDatabase(db);

  res.json({ success: true, message: 'Category created successfully', category: newCat });
});

// 10. Admin Delete Category
app.delete('/api/admin/categories/:name', (req, res) => {
  const db = readDatabase();
  const catName = decodeURIComponent(req.params.name).toLowerCase();
  db.categories = (db.categories || []).filter(c => c.name.toLowerCase() !== catName);
  writeDatabase(db);
  res.json({ success: true, message: 'Category deleted' });
});

// 11. Admin Get All Users (Live from Supabase Cloud)
app.get('/api/admin/users', async (req, res) => {
  try {
    let users = await listCloudUsers();
    const { search, status } = req.query;

    if (search && search.trim()) {
      const q = search.trim().toLowerCase();
      users = users.filter(u =>
        (u.name && u.name.toLowerCase().includes(q)) ||
        (u.email && u.email.toLowerCase().includes(q))
      );
    }

    if (status && status !== 'all') {
      users = users.filter(u => u.subscription?.status === status);
    }

    const allUsers = await listCloudUsers();
    const stats = {
      totalUsers: allUsers.length,
      activeVipUsers: allUsers.filter(u => u.subscription?.status === 'active').length,
      expiredUsers: allUsers.filter(u => u.subscription?.status === 'expired').length,
      inactiveUsers: allUsers.filter(u => u.subscription?.status === 'inactive').length
    };

    res.json({
      success: true,
      stats,
      users
    });
  } catch (err) {
    console.error('[Cloud admin/users error]:', err.message);
    res.status(500).json({ success: false, message: 'Failed to fetch users from cloud' });
  }
});

// 12. Admin Update User Subscription (Live in Supabase Cloud)
app.post('/api/admin/users/:id/subscription', async (req, res) => {
  const { action = 'grant_1_month', durationDays = 30 } = req.body;
  const now = Date.now();
  const daysMs = (parseInt(durationDays) || 30) * 24 * 60 * 60 * 1000;

  try {
    const allUsers = await listCloudUsers();
    const targetUser = allUsers.find(u => u.id === req.params.id);

    if (!targetUser) {
      return res.status(404).json({ success: false, message: 'User not found in cloud' });
    }

    let newSubscription = {
      status: 'inactive',
      plan: 'free',
      grantedAt: null,
      expiresAt: null,
      durationDays: 0
    };

    if (action === 'grant_1_month' || action === 'grant') {
      newSubscription = {
        status: 'active',
        plan: '1_month_vip',
        grantedAt: new Date(now).toISOString(),
        expiresAt: new Date(now + daysMs).toISOString(),
        durationDays: 30
      };
    } else if (action === 'extend_30' || action === 'extend') {
      const currentExpiresTime = targetUser?.subscription?.expiresAt ? new Date(targetUser.subscription.expiresAt).getTime() : 0;
      const baseTime = (currentExpiresTime > now) ? currentExpiresTime : now;
      const grantedAt = targetUser?.subscription?.grantedAt || new Date(now).toISOString();
      newSubscription = {
        status: 'active',
        plan: '1_month_vip',
        grantedAt,
        expiresAt: new Date(baseTime + daysMs).toISOString(),
        durationDays: (targetUser?.subscription?.durationDays || 0) + 30
      };
    } else if (action === 'revoke') {
      newSubscription = {
        status: 'inactive',
        plan: 'free',
        grantedAt: null,
        expiresAt: null,
        durationDays: 0
      };
    }

    const updated = await updateCloudUserSubscription(req.params.id, newSubscription);
    res.json({
      success: true,
      message: action === 'revoke'
        ? 'VIP subscription revoked'
        : `1-Month VIP subscription active until ${new Date(newSubscription.expiresAt).toLocaleDateString('en-GB')}`,
      user: updated
    });
  } catch (err) {
    console.error('[Cloud update subscription error]:', err.message);
    res.status(500).json({ success: false, message: 'Failed to update subscription in cloud' });
  }
});

// 13. Admin Delete User (Live from Supabase Cloud)
app.delete('/api/admin/users/:id', async (req, res) => {
  try {
    await deleteCloudUser(req.params.id);
    res.json({ success: true, message: 'User deleted from cloud successfully' });
  } catch (err) {
    console.error('[Cloud delete user error]:', err.message);
    res.status(500).json({ success: false, message: 'Failed to delete user from cloud' });
  }
});

// 14. Catch-all: Route to public files or index.php
app.use((req, res) => {
  const filePath = path.join(__dirname, 'public', req.path);
  if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
    return res.sendFile(filePath);
  }
  sendPhpOrHtml(req, res, 'index');
});

// Start Server locally if run directly via node server.js
if (require.main === module) {
  app.listen(PORT, () => {
    console.log(`🚀 VIRAL PROMPT Server is running on http://localhost:${PORT}`);
    console.log(`🔑 Admin Panel available at http://localhost:${PORT}/admin.php`);
  });
}

module.exports = app;
