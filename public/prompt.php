<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Prompt Details — ' . $SITE_NAME;
$pageDesc = 'Viral AI video prompts for Reels, Shorts & TikTok. Works with Kling, Veo, Runway & any AI video tool.';
$activePage = 'browse';

$extraHead = '
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
  </style>
';

include __DIR__ . '/includes/header.php';
?>

  <main>
    <div class="prompt-single">
      <a href="browse.php" class="back-link">← Back to all prompts</a>

      <div class="prompt-head">
        <h1 id="pTitle">Loading Prompt...</h1>
        <div class="prompt-meta" id="pMeta">
          <span id="pCat">📂 General</span>
          <span id="pDate">🕒 Updated 29 Aug 2026</span>
          <span id="pBadge" style="display:none;background:var(--green-light);color:var(--green);border-color:var(--green-light);font-weight:700;">FREE</span>
        </div>
      </div>

      <!-- Locked State Box (Exact SoniPrompts Match) -->
      <div class="locked-box" id="lockBox" style="display:none;">
        <div class="teaser">
          This premium prompt includes the complete master prompt system — full scene structure, camera angles, timing breakdown, captions, viral hooks and reference storyboard images. Everything is ready to copy and paste into your AI video tool. Members get instant access to this and every other prompt in the library, plus all new weekly drops.
        </div>
        <div class="lock-panel">
          <div class="ico" id="lockIco">🔒</div>
          <h3 id="lockTitle">Premium Content</h3>
          <p id="lockDesc">This prompt is available for premium members only.</p>
          <div class="lock-buttons" id="lockButtons">
            <a href="pricing.php" class="btn-subscribe-premium">Subscribe — $5/month</a>
          </div>
        </div>
      </div>

      <!-- Unlocked Content Box -->
      <div id="unlockedContent" style="display:none;">
        <!-- Storyboard & Reference Section (Exact SoniPrompts Match) -->
        <div id="pStoryboardSection" style="display:none;margin-bottom:26px;">
          <h3 style="font-size:16px;margin-bottom:12px;font-weight:700;">🎬 Storyboard &amp; Reference</h3>
          <div class="prompt-gallery" id="pGallery"></div>
        </div>

        <div class="copy-bar">
          <button class="btn btn-primary btn-sm" id="copyBtn" onclick="copyPrompt()">📋 Copy Prompt</button>
        </div>

        <pre class="prompt-content" id="pMasterPrompt"></pre>

        <div id="pNegWrapper" style="display:none;margin-top:24px;">
          <h4 style="margin-bottom:8px;color:#ef4444;font-size:15px;font-weight:700;">Negative Prompt:</h4>
          <pre class="prompt-content" id="pNegPrompt" style="border-color:#fecdd3;color:#dc2626;"></pre>
        </div>
      </div>

    </div>
  </main>

  <script>
    let activePrompt = null;
    let currentUser = null;

    // Load Prompt Data
    async function loadPromptDetails() {
      const params = new URLSearchParams(window.location.search);
      const id = params.get('id') || '270';
      const token = localStorage.getItem('promptmaster_token');

      const headers = {};
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      try {
        const res = await fetch(`/api/prompts/${id}`, { headers });
        const data = await res.json();
        if (!data.success || !data.prompt) {
          document.getElementById('pTitle').textContent = 'Prompt Not Found';
          return;
        }

        const p = data.prompt;
        activePrompt = p;

        document.title = `${p.title} — <?php echo htmlspecialchars($SITE_NAME); ?>`;
        document.getElementById('pTitle').textContent = p.title;
        document.getElementById('pCat').textContent = '🏷 ' + p.category;
        document.getElementById('pDate').textContent = '🕒 Updated ' + (p.updatedDate || '29 Aug 2026');

        const badge = document.getElementById('pBadge');
        if (p.type === 'free') {
          badge.style.display = 'inline-flex';
          badge.textContent = 'FREE';
          badge.style.background = 'var(--green-light)';
          badge.style.color = 'var(--green)';
          badge.style.borderColor = 'var(--green-light)';
        } else {
          badge.style.display = 'none';
        }

        const lockBox = document.getElementById('lockBox');
        const unlockedContent = document.getElementById('unlockedContent');

        if (p.isUnlocked) {
          // Unlocked View
          lockBox.style.display = 'none';
          unlockedContent.style.display = 'block';

          // Storyboard & Reference section (Separate from browse card thumbnail)
          const sbSection = document.getElementById('pStoryboardSection');
          const gallery = document.getElementById('pGallery');

          if (p.storyboardImages && p.storyboardImages.length > 0) {
            sbSection.style.display = 'block';
            if (p.storyboardImages.length === 1) {
              gallery.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 380px))';
            } else {
              gallery.style.gridTemplateColumns = 'repeat(auto-fill, minmax(210px, 1fr))';
            }
            gallery.innerHTML = p.storyboardImages.map((imgUrl, i) => `
              <div style="text-align:center;">
                <img src="${imgUrl}" alt="Storyboard image" class="lb-img" loading="lazy" style="width:100%;border-radius:12px;display:block;margin:0 auto;box-shadow:var(--shadow-sm);border:1px solid var(--border);">
                <a href="${imgUrl}" download="storyboard-${p.numericId || p.id}${p.storyboardImages.length > 1 ? '-' + (i + 1) : ''}.png" class="btn btn-sm btn-outline" style="margin-top:8px;display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:999px;text-decoration:none;font-size:13px;font-weight:600;color:var(--text);border:1.5px solid var(--border);background:#fff;cursor:pointer;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  Download
                </a>
              </div>
            `).join('');
          } else {
            sbSection.style.display = 'none';
            gallery.innerHTML = '';
          }

          document.getElementById('pMasterPrompt').textContent = p.masterPrompt;

          if (p.negativePrompt) {
            document.getElementById('pNegWrapper').style.display = 'block';
            document.getElementById('pNegPrompt').textContent = p.negativePrompt;
          } else {
            document.getElementById('pNegWrapper').style.display = 'none';
          }
        } else {
          // Locked View
          unlockedContent.style.display = 'none';
          lockBox.style.display = 'block';

          const lockIco = document.getElementById('lockIco');
          const lockTitle = document.getElementById('lockTitle');
          const lockDesc = document.getElementById('lockDesc');
          const lockButtons = document.getElementById('lockButtons');

          const currentPath = window.location.pathname + window.location.search;
          const loginNextUrl = `login.php?next=${encodeURIComponent(currentPath)}`;

          const isUserLoggedIn = Boolean(
            p.isLoggedIn ||
            currentUser ||
            localStorage.getItem('promptmaster_token') ||
            localStorage.getItem('promptmaster_user')
          );

          if (p.type === 'free') {
            // Free Prompt — Login to Unlock
            lockIco.textContent = '🎁';
            lockTitle.textContent = 'Free Prompt — Login to Unlock';
            lockDesc.textContent = 'Sign in free with Google to view the full prompt. No payment needed.';
            lockButtons.innerHTML = `
              <a href="${loginNextUrl}" class="btn btn-primary btn-lg">Login Free — Get This Prompt</a>
            `;
          } else {
            // Premium Prompt
            lockIco.textContent = '🔒';
            lockTitle.textContent = 'Premium Content';
            lockDesc.textContent = 'This prompt is available for premium members only.';

            if (isUserLoggedIn) {
              // Registered user without subscription: show ONLY the Subscribe button
              lockButtons.innerHTML = `
                <a href="pricing.php" class="btn-subscribe-premium">Subscribe — $5/month</a>
              `;
            } else {
              // Guest user: show Subscribe button + Login link
              lockButtons.innerHTML = `
                <a href="pricing.php" class="btn-subscribe-premium">Subscribe — $5/month</a>
                <a href="${loginNextUrl}" class="btn btn-outline" style="border-radius:999px;margin-top:6px;">Already a member? Login</a>
              `;
            }
          }
        }

      } catch (err) {
        console.error('Failed to load prompt:', err);
      }
    }

    function copyPrompt() {
      if (!activePrompt || !activePrompt.masterPrompt) return;
      navigator.clipboard.writeText(activePrompt.masterPrompt).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.textContent = '✅ Copied!';
        setTimeout(() => btn.textContent = '📋 Copy Prompt', 2000);
      });
    }

    // User Auth Header Sync
    async function checkUserAuth() {
      const urlParams = new URLSearchParams(window.location.search);
      const urlToken = urlParams.get('token');
      if (urlToken) {
        localStorage.setItem('promptmaster_token', urlToken);
      }
      let token = localStorage.getItem('promptmaster_token');
      if (!token) {
        const cachedUser = localStorage.getItem('promptmaster_user');
        if (cachedUser) {
          try {
            const u = JSON.parse(cachedUser);
            if (u && u.id) {
              token = u.id;
              localStorage.setItem('promptmaster_token', token);
            }
          } catch (e) {}
        }
      }

      if (!token) return;

      try {
        const res = await fetch('/api/auth/me', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (data.success && data.user) {
          currentUser = data.user;
          localStorage.setItem('promptmaster_user', JSON.stringify(currentUser));
        }
      } catch (err) {
        console.error('Auth error:', err);
      }
    }

    function userLogout() {
      localStorage.removeItem('promptmaster_token');
      localStorage.removeItem('promptmaster_user');
      window.location.reload();
    }

    window.addEventListener('DOMContentLoaded', () => {
      checkUserAuth().then(() => {
        loadPromptDetails();
      });
    });
  </script>

<?php
include __DIR__ . '/includes/footer.php';
?>
