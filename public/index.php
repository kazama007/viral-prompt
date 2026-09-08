<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = $SITE_NAME . ' — Viral AI Video Prompts';
$pageDesc = 'Viral AI video prompts for Facebook Reels, YouTube Shorts, Instagram & TikTok. Works with Seedance, Kling, Veo & any AI video tool. New prompts every week.';
$activePage = 'home';

include __DIR__ . '/includes/header.php';
?>

  <main>
    <!-- Cover Prompts Marquee Track (Moving Continuously to the Right) -->
    <div class="cover-marquee-wrap" id="coverMarquee" title="Hover to pause • Click any prompt box">
      <div class="marquee-track" id="marqueeTrack">
        <!-- Set 1 (User's prompt thumbnail boxes) -->
        <div class="marquee-card" onclick="window.location.href='/prompt?id=284'" title="30 Sec Ultra Viral Infrastructure Disaster">
          <img src="assets/images/marquee/card_disaster.png" alt="Infrastructure Disaster" width="340" height="190" fetchpriority="high" loading="eager">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=283'" title="30 Sec Viral Ghost Baby Horror">
          <img src="assets/images/marquee/card_ghost.png" alt="Ghost Baby Horror" width="340" height="190" fetchpriority="high" loading="eager">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=280'" title="30 Sec Viral Prison Reunion">
          <img src="assets/images/marquee/card_prison.png" alt="Prison Reunion" width="340" height="190" loading="eager">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=285'" title="30 Sec Cosmic Space Journey">
          <img src="assets/images/marquee/card_cosmic.png" alt="Cosmic Space Journey" width="340" height="190" loading="eager">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=270'" title="30-Second Giant Beehive">
          <img src="assets/images/marquee/card_5.png" alt="Giant Beehive" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge is-free">🎁 Free</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=264'" title="Nano Banana Bulk Image Generation">
          <img src="assets/images/marquee/card_7.png" alt="Nano Banana Bulk Generator" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge is-free">🎁 Free</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=284'" title="Cyber Realism Video Engine">
          <img src="assets/images/marquee/card_6.png" alt="Cyber Realism Engine" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=283'" title="High-Octane Cinematic Chase">
          <img src="assets/images/marquee/card_8.png" alt="Cinematic Chase" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge">🔒 Premium</span>
        </div>

        <!-- Set 2 (Seamless loop duplicate) -->
        <div class="marquee-card" onclick="window.location.href='/prompt?id=284'" title="30 Sec Ultra Viral Infrastructure Disaster">
          <img src="assets/images/marquee/card_disaster.png" alt="Infrastructure Disaster" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=283'" title="30 Sec Viral Ghost Baby Horror">
          <img src="assets/images/marquee/card_ghost.png" alt="Ghost Baby Horror" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=280'" title="30 Sec Viral Prison Reunion">
          <img src="assets/images/marquee/card_prison.png" alt="Prison Reunion" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=285'" title="30 Sec Cosmic Space Journey">
          <img src="assets/images/marquee/card_cosmic.png" alt="Cosmic Space Journey" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=270'" title="30-Second Giant Beehive">
          <img src="assets/images/marquee/card_5.png" alt="Giant Beehive" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge is-free">🎁 Free</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=264'" title="Nano Banana Bulk Image Generation">
          <img src="assets/images/marquee/card_7.png" alt="Nano Banana Bulk Generator" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge is-free">🎁 Free</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=284'" title="Cyber Realism Video Engine">
          <img src="assets/images/marquee/card_6.png" alt="Cyber Realism Engine" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
        <div class="marquee-card" onclick="window.location.href='/prompt?id=283'" title="High-Octane Cinematic Chase">
          <img src="assets/images/marquee/card_8.png" alt="Cinematic Chase" width="340" height="190" loading="lazy" decoding="async">
          <span class="marquee-badge">🔒 Premium</span>
        </div>
      </div>
    </div>

    <!-- Profile Strip -->
    <div class="profile-strip">
      <div class="container" style="text-align:center;">
        <div class="avatar-wrap">
          <img src="assets/images/avatar.jpg" alt="VIRAL PROMPT" class="profile-avatar">
          <span class="verified-badge" title="Verified Creator">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
        </div>
        <h1 class="profile-name">VIRAL <b>PROMPT</b></h1>
        <p class="byline">Viral Prompts by <strong>VIRAL PROMPT</strong></p>
      </div>
    </div>

    <!-- Prompts Feed -->
    <div class="plist">

      <!-- Free Prompts Section -->
      <h2 class="plist-head">Free Prompts</h2>
      <div id="freeList">
        <div class="sk-prow"><div class="sk-thumb sk-shimmer"></div><div class="sk-meta"><div class="sk-line-title sk-shimmer"></div><div class="sk-line-sub sk-shimmer"></div></div><div class="sk-btn-box sk-shimmer"></div></div>
        <div class="sk-prow"><div class="sk-thumb sk-shimmer"></div><div class="sk-meta"><div class="sk-line-title sk-shimmer"></div><div class="sk-line-sub sk-shimmer"></div></div><div class="sk-btn-box sk-shimmer"></div></div>
      </div>
      <p style="text-align:center;margin:10px 0 30px;">
        <a href="/browse?type=free" class="btn btn-primary">Load More Free Prompts →</a>
      </p>

      <!-- Premium Prompts Section -->
      <h2 class="plist-head">Premium Prompts</h2>
      <div id="premiumList">
        <div class="sk-prow"><div class="sk-thumb sk-shimmer"></div><div class="sk-meta"><div class="sk-line-title sk-shimmer"></div><div class="sk-line-sub sk-shimmer"></div></div><div class="sk-btn-box sk-shimmer"></div></div>
        <div class="sk-prow"><div class="sk-thumb sk-shimmer"></div><div class="sk-meta"><div class="sk-line-title sk-shimmer"></div><div class="sk-line-sub sk-shimmer"></div></div><div class="sk-btn-box sk-shimmer"></div></div>
        <div class="sk-prow"><div class="sk-thumb sk-shimmer"></div><div class="sk-meta"><div class="sk-line-title sk-shimmer"></div><div class="sk-line-sub sk-shimmer"></div></div><div class="sk-btn-box sk-shimmer"></div></div>
      </div>
      <p style="text-align:center;margin:10px 0 30px;">
        <a href="/browse?type=premium" class="btn btn-primary">Load More Prompts →</a>
      </p>

    </div>
  </main>

  <!-- Prompt Details Modal -->
  <div class="modal-overlay" id="promptModal">
    <div class="prompt-modal-box">
      <button class="close-modal" onclick="closePromptModal()">✕</button>
      <div style="margin-bottom:12px;">
        <span id="mBadge" class="prow-price is-free" style="font-size:12px;">FREE PROMPT</span>
        <span id="mCat" class="prow-cat" style="font-size:12px;margin-left:8px;">Category</span>
      </div>
      <h2 id="mTitle" style="font-size:22px;line-height:1.3;margin-bottom:14px;">Prompt Title</h2>

      <div id="mLockedNotice" style="display:none;background:#fef3c7;border:1px solid #fde68a;border-radius:16px;padding:24px 20px;text-align:center;margin:20px 0;">
        <div id="mLockIco" style="font-size:38px;margin-bottom:8px;">🔒</div>
        <h3 id="mLockHeading" style="font-size:18px;font-weight:800;margin-bottom:6px;">Premium Member Prompt</h3>
        <p id="mLockText" style="font-size:14px;color:#78350f;margin-bottom:18px;">This prompt includes the full master system prompt, camera movements, and audio hooks. Join our community to unlock all 280+ prompts.</p>
        <div id="mLockBtnContainer" style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
          <a href="/pricing" class="btn btn-primary btn-sm" style="background:#6d5dfc;border-radius:999px;padding:10px 24px;">Subscribe — $2.5/month</a>
          <a href="https://wa.me/919410610800?text=Hello%20VIRAL%20PROMPT%2C%20I%20want%20to%20unlock%20prompts" target="_blank" class="btn btn-green btn-sm" style="border-radius:999px;padding:10px 24px;">Order on WhatsApp</a>
        </div>
      </div>

      <div id="mContentBox">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;">
          <strong style="font-size:13.5px;color:var(--muted);">MASTER PROMPT SYSTEM</strong>
          <button class="btn btn-primary btn-sm" id="mCopyBtn" onclick="copyMasterPrompt()">📋 Copy Prompt</button>
        </div>
        <div class="prompt-code-container" id="mCodeText"></div>

        <div id="mNegBox" style="display:none;margin-top:14px;">
          <strong style="font-size:13.5px;color:var(--muted);">NEGATIVE RULES:</strong>
          <div class="prompt-code-container" id="mNegText" style="color:#f87171;margin-top:6px;"></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let activePromptData = null;

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    // Dynamic Header Marquee: Randomizes 10 prompt thumbnails on EVERY refresh from all available prompts
    function renderMarqueeCards(selectedPrompts) {
      const track = document.getElementById('marqueeTrack');
      if (!track || !Array.isArray(selectedPrompts) || !selectedPrompts.length) return;

      const makeCard = (p, isSet2) => {
        const id = p.numericId || p.id;
        const title = escapeHtml(p.title || 'Viral AI Video Prompt');
        const thumb = p.thumbnail || p.rawThumbnail || '';
        const raw = p.rawThumbnail || p.thumbnail || '';
        const isFree = p.type === 'free';
        const badgeClass = isFree ? 'marquee-badge is-free' : 'marquee-badge';
        const badgeText = isFree ? '🎁 Free' : '🔒 Premium';

        return `
          <div class="marquee-card" onclick="window.location.href='/prompt?id=${id}'" title="${title}">
            <img src="${thumb}" alt="${title}" width="340" height="190" ${isSet2 ? 'loading="lazy"' : 'loading="eager" fetchpriority="high"'} decoding="async" onerror="if(this.dataset.failed!=='1'){this.dataset.failed='1';this.src='${raw}';}">
            <span class="${badgeClass}">${badgeText}</span>
          </div>
        `;
      };

      // Set 1 (10 cards) + Set 2 (10 duplicate cards) for seamless 50% infinite CSS marquee translation
      const set1 = selectedPrompts.map(p => makeCard(p, false)).join('');
      const set2 = selectedPrompts.map(p => makeCard(p, true)).join('');

      track.innerHTML = set1 + set2;
    }

    function pickRandomPrompts(pool, count = 10) {
      if (!Array.isArray(pool) || !pool.length) return [];
      const shuffled = [...pool];
      for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
      }
      return shuffled.slice(0, Math.min(count, shuffled.length));
    }

    async function initMarqueeRandomizer() {
      // 1. Instant zero-latency random shuffle on EVERY refresh from local pool
      let pool = [];
      try {
        const cached = localStorage.getItem('pm_marquee_pool_v1');
        if (cached) {
          pool = JSON.parse(cached);
          if (Array.isArray(pool) && pool.length >= 8) {
            renderMarqueeCards(pickRandomPrompts(pool, 10));
          }
        }
      } catch (e) {}

      // 2. Fetch full marquee pool of all prompts from server in background
      try {
        const res = await fetch('/api/prompts/marquee-pool');
        const data = await res.json();
        if (data && Array.isArray(data.prompts) && data.prompts.length) {
          try {
            localStorage.setItem('pm_marquee_pool_v1', JSON.stringify(data.prompts));
          } catch (e) {}
          // If no local pool existed yet, render immediately
          if (!pool || pool.length < 8) {
            renderMarqueeCards(pickRandomPrompts(data.prompts, 10));
          }
        }
      } catch (err) {
        console.warn('Marquee pool fetch error:', err);
      }
    }

    // Run marquee randomizer immediately as script parses
    initMarqueeRandomizer();

    let slideIdx = 0;
    const slides = document.querySelectorAll('.cover-slide');
    if (slides.length > 1) {
      setInterval(() => {
        slides[slideIdx].classList.remove('active');
        slideIdx = (slideIdx + 1) % slides.length;
        slides[slideIdx].classList.add('active');
      }, 4500);
    }

    let allPromptsCache = [];
    let currentUser = null;

    function isUserVip() {
      if (currentUser && currentUser.subscription && (currentUser.subscription.status === 'active' || currentUser.subscription.isActive)) {
        return true;
      }
      try {
        const cached = JSON.parse(localStorage.getItem('promptmaster_user') || 'null');
        if (cached && cached.subscription && (cached.subscription.status === 'active' || cached.subscription.isActive)) {
          return true;
        }
      } catch (e) {}
      return false;
    }

    async function loadPrompts() {
      // 1. Instant zero-latency render from client cache
      try {
        const cached = localStorage.getItem('pm_home_prompts_v2');
        if (cached) {
          const parsed = JSON.parse(cached);
          if (Array.isArray(parsed) && parsed.length) {
            allPromptsCache = parsed;
            renderAllPrompts();
          }
        }
      } catch (e) {}

      // 2. Fetch latest prompts asynchronously without freezing UI
      try {
        const res = await fetch('/api/prompts?limit=30');
        const data = await res.json();
        if (data && data.prompts && data.prompts.length) {
          allPromptsCache = data.prompts;
          renderAllPrompts();
          try {
            localStorage.setItem('pm_home_prompts_v2', JSON.stringify(data.prompts));
          } catch (e) {}
        }
      } catch (err) {
        console.error('Failed to load prompts from backend:', err);
      }
    }

    function renderAllPrompts() {
      if (!allPromptsCache.length) return;
      renderFreePrompts(allPromptsCache.filter(p => p.type === 'free'));
      renderPremiumPrompts(allPromptsCache.filter(p => p.type === 'premium'));
    }

    function renderFreePrompts(freeList) {
      const container = document.getElementById('freeList');
      if (!freeList.length) {
        container.innerHTML = `<p style="color:var(--muted);text-align:center;padding:16px;">No free prompts available yet.</p>`;
        return;
      }
      container.innerHTML = freeList.slice(0, 4).map((p, idx) => `
        <a class="prow" href="/prompt?id=${p.numericId || p.id}">
          <div class="prow-thumb">
            <img src="${p.thumbnail}"
                 alt="${p.title}"
                 ${idx < 2 ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"'}
                 decoding="async"
                 onerror="if(this.dataset.failed!=='1'){this.dataset.failed='1';this.src='${p.rawThumbnail || p.thumbnail}';}"
                 onload="this.parentElement.classList.add('is-loaded')">
            <span class="prow-file-badge">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </span>
          </div>
          <div class="prow-body">
            <h3>${p.title}</h3>
            <span class="prow-cat">${p.promptCountBadge || '🗂 1 prompt'}</span>
            <span class="prow-price is-free">Free</span>
            <span class="prow-btn prow-btn-free">View</span>
          </div>
        </a>
      `).join('');
    }

    function renderPremiumPrompts(premList) {
      const container = document.getElementById('premiumList');
      if (!premList.length) {
        container.innerHTML = `<p style="color:var(--muted);text-align:center;padding:16px;">No premium prompts available yet.</p>`;
        return;
      }

      const isVip = isUserVip();

      container.innerHTML = premList.slice(0, 10).map((p, idx) => {
        const priorityAttr = idx < 2 ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"';
        const fallbackAttr = `onerror="if(this.dataset.failed!=='1'){this.dataset.failed='1';this.src='${p.rawThumbnail || p.thumbnail}';}" onload="this.parentElement.classList.add('is-loaded')"`;

        if (isVip) {
          return `
            <a class="prow" href="/prompt?id=${p.numericId || p.id}">
              <div class="prow-thumb">
                <img src="${p.thumbnail}" alt="${p.title}" ${priorityAttr} decoding="async" ${fallbackAttr}>
                <span class="prow-file-badge">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </span>
              </div>
              <div class="prow-body">
                <h3>${p.title}</h3>
                <span class="prow-cat">${p.promptCountBadge || '🗂 1 prompt'}</span>
                <span class="prow-price">Included</span>
                <span class="prow-btn">View</span>
              </div>
            </a>
          `;
        } else {
          return `
            <a class="prow" href="/prompt?id=${p.numericId || p.id}">
              <div class="prow-thumb">
                <img src="${p.thumbnail}" alt="${p.title}" ${priorityAttr} decoding="async" ${fallbackAttr}>
                <span class="prow-file-badge is-locked">🔒</span>
              </div>
              <div class="prow-body">
                <h3>${p.title}</h3>
                <span class="prow-cat">${p.promptCountBadge || '🗂 ' + p.category}</span>
                <span class="prow-price is-premium">Premium</span>
                <span class="prow-btn">Join to unlock</span>
              </div>
            </a>
          `;
        }
      }).join('');
    }

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
          renderAllPrompts();
        }
      } catch (err) {
        console.error('Auth verify error:', err);
      }
    }

    function userLogout() {
      localStorage.removeItem('promptmaster_token');
      localStorage.removeItem('promptmaster_user');
      window.location.reload();
    }

    async function openPromptModal(id) {
      try {
        const token = localStorage.getItem('promptmaster_token');
        const headers = {};
        if (token) {
          headers['Authorization'] = `Bearer ${token}`;
        }

        const res = await fetch(`/api/prompts/${id}`, { headers });
        const data = await res.json();
        if (!data.success) return;

        const p = data.prompt;
        activePromptData = p;

        document.getElementById('mTitle').textContent = p.title;
        document.getElementById('mCat').textContent = '🗂 ' + p.category;

        const badge = document.getElementById('mBadge');
        const lockedNotice = document.getElementById('mLockedNotice');
        const contentBox = document.getElementById('mContentBox');
        const negBox = document.getElementById('mNegBox');

        const isUnlocked = Boolean(p.isUnlocked);

        if (isUnlocked) {
          if (p.type === 'free') {
            badge.className = 'prow-price is-free';
            badge.textContent = 'FREE PROMPT';
          } else {
            badge.className = 'prow-price';
            badge.style.background = '#e6f9ed';
            badge.style.color = '#128c46';
            badge.style.borderColor = '#b7ecc8';
            badge.textContent = '⭐ VIP UNLOCKED';
          }
          lockedNotice.style.display = 'none';
          contentBox.style.display = 'block';
          document.getElementById('mCodeText').textContent = p.masterPrompt || '';
          if (p.negativePrompt) {
            negBox.style.display = 'block';
            document.getElementById('mNegText').textContent = p.negativePrompt;
          } else {
            negBox.style.display = 'none';
          }
        } else {
          contentBox.style.display = 'none';
          lockedNotice.style.display = 'block';

          const lockIco = document.getElementById('mLockIco');
          const lockHeading = document.getElementById('mLockHeading');
          const lockText = document.getElementById('mLockText');
          const lockBtns = document.getElementById('mLockBtnContainer');

          if (p.type === 'free') {
            badge.className = 'prow-price is-free';
            badge.textContent = 'FREE PROMPT';
            lockedNotice.style.background = '#f8f7fd';
            lockedNotice.style.borderColor = '#e0dbf8';
            if (lockIco) lockIco.textContent = '🎁';
            if (lockHeading) {
              lockHeading.textContent = 'Free Prompt — Login to Unlock';
              lockHeading.style.color = '#17151f';
            }
            if (lockText) {
              lockText.textContent = 'Sign in free with Google to view the full prompt. No payment needed.';
              lockText.style.color = '#645f75';
            }
            if (lockBtns) {
              lockBtns.innerHTML = `
                <a href="/login?next=${encodeURIComponent('/prompt?id=' + p.id)}" class="btn btn-primary" style="padding:11px 28px;border-radius:999px;font-weight:700;text-decoration:none;">Login Free — Get This Prompt</a>
              `;
            }
          } else {
            badge.className = 'prow-price';
            badge.style.background = '';
            badge.style.color = '';
            badge.style.borderColor = '';
            badge.textContent = '⭐ VIP PREMIUM';
            lockedNotice.style.background = '#fef3c7';
            lockedNotice.style.borderColor = '#fde68a';
            if (lockIco) lockIco.textContent = '🔒';
            if (lockHeading) {
              lockHeading.textContent = 'Premium Member Prompt';
              lockHeading.style.color = '#78350f';
            }
            if (lockText) {
              lockText.textContent = 'This prompt includes the full master system prompt, camera movements, and audio hooks. Join our VIP community to unlock all prompts.';
              lockText.style.color = '#78350f';
            }
            if (lockBtns) {
              lockBtns.innerHTML = `
                <a href="/pricing" class="btn btn-primary btn-sm" style="background:#6d5dfc;border-radius:999px;padding:10px 24px;text-decoration:none;">Subscribe — $2.5/month</a>
                <a href="https://wa.me/919410610800?text=Hello%20VIRAL%20PROMPT%2C%20I%20want%20to%20unlock%20prompts" target="_blank" class="btn btn-green btn-sm" style="border-radius:999px;padding:10px 24px;text-decoration:none;">Order on WhatsApp</a>
              `;
            }
          }
        }

        document.getElementById('promptModal').classList.add('active');
      } catch (err) {
        console.error('Error opening prompt modal:', err);
      }
    }

    function closePromptModal() {
      document.getElementById('promptModal').classList.remove('active');
    }

    function copyMasterPrompt() {
      if (!activePromptData) return;
      navigator.clipboard.writeText(activePromptData.masterPrompt).then(() => {
        const btn = document.getElementById('mCopyBtn');
        btn.textContent = '✅ Copied!';
        setTimeout(() => btn.textContent = '📋 Copy Prompt', 2000);
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      initMarqueeRandomizer();
      loadPrompts();
      checkUserAuth();
    });
  </script>

<?php
include __DIR__ . '/includes/footer.php';
?>
