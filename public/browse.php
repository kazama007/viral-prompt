<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'All Master Prompts — ' . $SITE_NAME;
$pageDesc = 'Explore our complete database of tested viral AI video prompts. Filter by category, AI tool and free/premium status.';
$activePage = 'browse';

include __DIR__ . '/includes/header.php';
?>

  <main class="container" style="padding-top: 36px; padding-bottom: 60px;">
    <h1 class="section-title">All Prompts</h1>
    <p class="section-sub" id="browseSubTitle">285 prompts</p>

    <!-- Search & Filter Controls -->
    <div class="filters">
      <form class="search-form" id="browseSearchForm" onsubmit="event.preventDefault(); handleFilterChange();">
        <input type="text" id="browseSearchInput" placeholder="Search prompts…" oninput="handleFilterChange()">
        <select id="browseSortSelect" onchange="handleFilterChange()">
          <option value="new" selected>Newest First</option>
          <option value="old">Oldest First</option>
          <option value="az">Name A–Z</option>
          <option value="za">Name Z–A</option>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
      </form>

      <!-- Free / VIP Type Filter -->
      <div class="cat-pills" style="margin-bottom: 10px;">
        <button type="button" class="pill active" data-type="all" onclick="selectTypeFilter('all')">All Prompts</button>
        <button type="button" class="pill" data-type="free" onclick="selectTypeFilter('free')">🎁 Free</button>
        <button type="button" class="pill" data-type="premium" onclick="selectTypeFilter('premium')">⭐ Premium</button>
      </div>

      <!-- Categories Pills Bar -->
      <div class="cat-pills" id="browseCategoryPills">
        <button type="button" class="pill active" onclick="selectCategoryFilter('all')">All Categories</button>
      </div>
    </div>

    <!-- Prompts Grid (3-column responsive layout) -->
    <div class="grid" id="browseGrid">
      <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted);">Loading prompt catalog...</div>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination" id="browsePagination"></div>
  </main>

  <!-- Prompt Details Modal -->
  <div class="modal-overlay" id="promptModal">
    <div class="prompt-modal-box">
      <button class="close-modal" onclick="closePromptModal()">✕</button>
      <div style="margin-bottom:12px;">
        <span id="mBadge" class="card-overlay-type free" style="position:static;display:inline-block;">FREE PROMPT</span>
        <span id="mCat" class="card-overlay-cat" style="position:static;display:inline-block;margin-left:8px;background:#F1F5F9;color:var(--text-soft);">Category</span>
      </div>
      <h2 id="mTitle" style="font-size:22px;line-height:1.3;margin-bottom:14px;">Prompt Title</h2>

      <div id="mLockedNotice" style="display:none;background:#FFFBEB;border:1.5px solid var(--amber-border);border-radius:16px;padding:26px;text-align:center;margin:20px 0;">
        <div id="mLockIco" style="font-size:36px;margin-bottom:10px;">👑</div>
        <h3 id="mLockHeading" style="font-size:18px;margin-bottom:8px;color:#92400E;font-weight:800;">VIP Community Prompt</h3>
        <p id="mLockText" style="font-size:14.5px;color:#78350F;margin-bottom:20px;max-width:480px;margin-left:auto;margin-right:auto;">This prompt includes the full master prompt system, camera movement rules, and audio hooks. Join the VIRAL PROMPT VIP community to unlock all 280+ prompts.</p>
        <div id="mLockBtnContainer" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
          <a href="/pricing" class="btn btn-primary" style="background:#6d5dfc;border-radius:999px;padding:12px 28px;">Subscribe — $2.5/month</a>
          <a href="https://wa.me/919410610800?text=Hello%20VIRAL%20PROMPT%2C%20I%20want%20to%20unlock%20prompts" target="_blank" class="btn btn-green" style="border-radius:999px;padding:12px 28px;">Order on WhatsApp</a>
        </div>
      </div>

      <div id="mContentBox">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;">
          <strong style="font-size:13.5px;color:var(--muted);letter-spacing:0.04em;">MASTER PROMPT SYSTEM:</strong>
          <button class="btn btn-primary btn-sm" id="mCopyBtn" onclick="copyMasterPrompt()">📋 Copy Prompt</button>
        </div>
        <div class="prompt-code-container" id="mCodeText"></div>

        <div id="mNegBox" style="display:none;margin-top:14px;">
          <strong style="font-size:13.5px;color:var(--muted);letter-spacing:0.04em;">NEGATIVE RULES:</strong>
          <div class="prompt-code-container" id="mNegText" style="color:#F87171;margin-top:6px;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="toastNotification" style="position:fixed;bottom:24px;right:24px;background:#0F172A;color:#fff;padding:12px 22px;border-radius:999px;font-size:14px;font-weight:700;display:none;z-index:9999;box-shadow:var(--shadow-md);align-items:center;gap:8px;">
    <span>✅</span> <span id="toastMsg">Prompt Copied to Clipboard!</span>
  </div>

  <!-- WhatsApp Support Float -->
  <a href="https://wa.me/919410610800?text=Hi!+I'm+a+member+of+your+community.+I+need+some+help."
     target="_blank" rel="noopener" class="wa-float" title="WhatsApp Support">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.21 5.1 4.5.71.31 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.09 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35zM12.05 21.79h-.01a9.72 9.72 0 0 1-4.96-1.36l-.36-.21-3.69.97.98-3.6-.23-.37a9.72 9.72 0 0 1-1.49-5.18c0-5.37 4.37-9.74 9.75-9.74a9.68 9.68 0 0 1 6.89 2.86 9.68 9.68 0 0 1 2.85 6.89c0 5.38-4.37 9.74-9.73 9.74zm8.28-18.02A11.64 11.64 0 0 0 12.05.33C5.6.33.35 5.58.35 12.03c0 2.06.54 4.07 1.56 5.84L.25 23.79l6.07-1.59a11.68 11.68 0 0 0 5.72 1.46h.01c6.45 0 11.7-5.25 11.7-11.7 0-3.13-1.22-6.07-3.42-8.19z"/></svg>
    <span class="wa-float-label">Support</span>
  </a>

  <script>
    let activeType = 'all';
    let activeCategory = 'all';
    let activePromptData = null;
    let allFetchedPrompts = [];
    let currentPage = 1;
    const itemsPerPage = 15;

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('type')) activeType = urlParams.get('type');
    if (urlParams.get('cat')) activeCategory = urlParams.get('cat');
    if (urlParams.get('category')) activeCategory = urlParams.get('category');

    async function initBrowse() {
      document.querySelectorAll('.cat-pills button[data-type]').forEach(b => {
        b.classList.toggle('active', b.getAttribute('data-type') === activeType);
      });
      await loadCategories();
      await fetchAndRenderPrompts();
    }

    async function loadCategories() {
      try {
        const res = await fetch('/api/categories');
        const data = await res.json();
        const pillsContainer = document.getElementById('browseCategoryPills');

        const soniOrder = [
          'Animal & Pets',
          'Art & Animation',
          'ASMR & Satisfying',
          'Comedy & Entertainment',
          'DIY & Crafts',
          'Emotional & Inspirational',
          'Fantasy & Sci-Fi',
          'Food & Cooking',
          'Historical & Nostalgia',
          'Kids & Family',
          'Nature & Wildlife',
          'Sports & Action'
        ];

        const rendered = new Set();
        const catHtml = [`<button type="button" class="pill ${activeCategory === 'all' ? 'active' : ''}" onclick="selectCategoryFilter('all')">All Categories</button>`];

        soniOrder.forEach(name => {
          const c = (data.categories || []).find(item => item.name.toLowerCase() === name.toLowerCase());
          if (c) {
            rendered.add(c.name.toLowerCase());
            const isActive = activeCategory.toLowerCase() === c.name.toLowerCase();
            catHtml.push(`<button type="button" class="pill ${isActive ? 'active' : ''}" onclick="selectCategoryFilter('${c.name.replace(/'/g, "\\'")}')">${c.name} (${c.count})</button>`);
          }
        });

        (data.categories || []).forEach(c => {
          if (!rendered.has(c.name.toLowerCase())) {
            rendered.add(c.name.toLowerCase());
            const isActive = activeCategory.toLowerCase() === c.name.toLowerCase();
            catHtml.push(`<button type="button" class="pill ${isActive ? 'active' : ''}" onclick="selectCategoryFilter('${c.name.replace(/'/g, "\\'")}')">${c.name} (${c.count})</button>`);
          }
        });

        pillsContainer.innerHTML = catHtml.join('');
      } catch (err) {
        console.error('Error loading categories:', err);
      }
    }

    function renderBrowseSkeletons() {
      const grid = document.getElementById('browseGrid');
      if (!grid) return;
      grid.innerHTML = Array(6).fill(0).map(() => `
        <div class="card" style="border:1px solid var(--border);border-radius:16px;overflow:hidden;background:#fff;padding:0;">
          <div class="sk-shimmer" style="width:100%;aspect-ratio:16/9;"></div>
          <div style="padding:16px;display:flex;flex-direction:column;gap:10px;">
            <div class="sk-shimmer" style="height:18px;width:70%;border-radius:6px;"></div>
            <div class="sk-shimmer" style="height:12px;width:40%;border-radius:4px;"></div>
            <div class="sk-shimmer" style="height:36px;width:100%;border-radius:999px;margin-top:8px;"></div>
          </div>
        </div>
      `).join('');
    }

    async function fetchAndRenderPrompts() {
      const search = document.getElementById('browseSearchInput').value;
      const sort = document.getElementById('browseSortSelect').value;

      const isDefault = activeType === 'all' && activeCategory === 'all' && !search && (sort === 'new' || !sort);

      // Instant render from local cache if default view
      if (isDefault && !allFetchedPrompts.length) {
        try {
          const cached = localStorage.getItem('pm_browse_prompts_v1');
          if (cached) {
            const parsed = JSON.parse(cached);
            if (Array.isArray(parsed) && parsed.length) {
              allFetchedPrompts = parsed;
              document.getElementById('browseSubTitle').textContent = `${allFetchedPrompts.length} prompts`;
              renderGrid();
            }
          }
        } catch (e) {}
      }

      if (!allFetchedPrompts.length) {
        renderBrowseSkeletons();
      }

      const query = new URLSearchParams({
        type: activeType,
        category: activeCategory,
        search,
        sort
      });

      try {
        const res = await fetch(`/api/prompts?${query.toString()}`);
        const data = await res.json();
        if (data && data.prompts) {
          allFetchedPrompts = data.prompts;
          document.getElementById('browseSubTitle').textContent = `${allFetchedPrompts.length} prompts`;
          currentPage = 1;
          renderGrid();
          if (isDefault) {
            try { localStorage.setItem('pm_browse_prompts_v1', JSON.stringify(data.prompts)); } catch (e) {}
          }
        }
      } catch (err) {
        console.error('Error loading prompts:', err);
      }
    }

    function isUserVip() {
      let u = currentUser;
      if (!u) {
        try {
          u = JSON.parse(localStorage.getItem('promptmaster_user') || 'null');
        } catch (e) {
          u = null;
        }
      }
      if (!u) return false;
      if (u.isVip === true || u.vipStatus === 'active') return true;
      if (u.subscription && (u.subscription.status === 'active' || u.subscription.isActive)) return true;
      return false;
    }

    function renderGrid() {
      const grid = document.getElementById('browseGrid');
      if (!allFetchedPrompts.length) {
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--muted);font-size:15px;">No prompts found matching your criteria.</div>`;
        renderPagination(0);
        return;
      }

      const isVip = isUserVip();
      const totalPages = Math.ceil(allFetchedPrompts.length / itemsPerPage);
      if (currentPage > totalPages && totalPages > 0) currentPage = 1;

      const startIdx = (currentPage - 1) * itemsPerPage;
      const pagePrompts = allFetchedPrompts.slice(startIdx, startIdx + itemsPerPage);

      grid.innerHTML = pagePrompts.map(p => {
        const isFree = p.type === 'free';
        const isUnlocked = isVip || isFree;
        const thumbUrl = p.thumbnail
          ? (p.thumbnail.startsWith('http://') || p.thumbnail.startsWith('https://')
              ? p.thumbnail
              : (p.thumbnail.startsWith('/') ? p.thumbnail : '/' + p.thumbnail))
          : '';
        const detailUrl = `/prompt?id=${p.numericId || p.id}`;

        let footBtn = '';
        if (isFree) {
          footBtn = `<a href="${detailUrl}" class="btn btn-sm btn-green btn-block">Get Free Prompt</a>`;
        } else if (isVip) {
          footBtn = `<a href="${detailUrl}" class="btn btn-sm btn-primary btn-block">View Prompt</a>`;
        } else {
          footBtn = `<a href="${detailUrl}" class="btn btn-sm btn-outline btn-block">Unlock</a>`;
        }

        let badgeHtml = '';
        if (isFree) {
          badgeHtml = `<span class="free-badge">FREE</span>`;
        } else if (!isVip) {
          badgeHtml = `<span class="lock-badge">🔒 Premium</span>`;
        }

        const excerpt = p.summary || (p.masterPrompt ? p.masterPrompt.slice(0, 100) + '…' : '');

        return `
          <div class="pcard ${!isUnlocked && p.type === 'premium' ? 'locked' : ''}">
            <div class="pcard-thumb">
              <a href="${detailUrl}" style="display:block;width:100%;height:100%;">
                <img src="${thumbUrl}" alt="${p.title}" loading="lazy" decoding="async" width="660" height="371">
              </a>
              ${badgeHtml}
            </div>
            <div class="pcard-body">
              <span class="pcard-cat">${p.category}</span>
              <h3 class="pcard-title"><a href="${detailUrl}">${p.title}</a></h3>
              <p class="pcard-ex">${excerpt}</p>
            </div>
            <div class="pcard-foot">
              ${footBtn}
            </div>
          </div>
        `;
      }).join('');

      renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
      const pag = document.getElementById('browsePagination');
      if (!pag) return;
      if (totalPages <= 1) {
        pag.innerHTML = '';
        return;
      }

      let html = [];
      for (let i = 1; i <= totalPages; i++) {
        html.push(`<a href="javascript:void(0)" class="page-link ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</a>`);
      }
      pag.innerHTML = html.join('');
    }

    function goToPage(pg) {
      currentPage = pg;
      renderGrid();
      const grid = document.getElementById('browseGrid');
      if (grid) {
        grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }

    function selectTypeFilter(type) {
      activeType = type;
      document.querySelectorAll('.cat-pills button[data-type]').forEach(b => {
        b.classList.toggle('active', b.getAttribute('data-type') === type);
      });
      fetchAndRenderPrompts();
    }

    function selectCategoryFilter(cat) {
      activeCategory = cat;
      loadCategories();
      fetchAndRenderPrompts();
    }

    function handleFilterChange() {
      fetchAndRenderPrompts();
    }

    function showToast(msg) {
      const toast = document.getElementById('toastNotification');
      document.getElementById('toastMsg').textContent = msg;
      toast.style.display = 'flex';
      setTimeout(() => toast.style.display = 'none', 3000);
    }

    let currentUser = null;

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
          fetchAndRenderPrompts();
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
        document.getElementById('mCat').textContent = p.category;

        const badge = document.getElementById('mBadge');
        const lockedNotice = document.getElementById('mLockedNotice');
        const contentBox = document.getElementById('mContentBox');
        const negBox = document.getElementById('mNegBox');

        const isUnlocked = Boolean(p.isUnlocked);

        if (isUnlocked) {
          if (p.type === 'free') {
            badge.className = 'card-overlay-type free';
            badge.textContent = '🎁 FREE PROMPT';
          } else {
            badge.className = 'card-overlay-type vip';
            badge.style.background = '#e6f9ed';
            badge.style.color = '#128c46';
            badge.style.border = '1px solid #b7ecc8';
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
            badge.className = 'card-overlay-type free';
            badge.textContent = '🎁 FREE PROMPT';
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
            badge.className = 'card-overlay-type vip';
            badge.style.background = '';
            badge.style.color = '';
            badge.style.border = '';
            badge.textContent = '⭐ VIP MEMBER PROMPT';
            lockedNotice.style.background = '#FFFBEB';
            lockedNotice.style.borderColor = 'var(--amber-border)';
            if (lockIco) lockIco.textContent = '👑';
            if (lockHeading) {
              lockHeading.textContent = 'VIP Community Prompt';
              lockHeading.style.color = '#92400E';
            }
            if (lockText) {
              lockText.textContent = 'This prompt includes the full master prompt system, camera movement rules, and audio hooks. Join the VIRAL PROMPT VIP community to unlock all 280+ prompts.';
              lockText.style.color = '#78350F';
            }
            if (lockBtns) {
              lockBtns.innerHTML = `
                <a href="/pricing" class="btn btn-primary" style="text-decoration:none;background:#6d5dfc;border-radius:999px;padding:12px 28px;">Subscribe — $2.5/month</a>
                <a href="https://wa.me/919410610800?text=Hello%20VIRAL%20PROMPT%2C%20I%20want%20to%20unlock%20prompts" target="_blank" class="btn btn-green" style="text-decoration:none;border-radius:999px;padding:12px 28px;">Order on WhatsApp</a>
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
        showToast('✅ Master Prompt Copied to Clipboard!');
        setTimeout(() => btn.textContent = '📋 Copy Prompt', 2000);
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      initBrowse();
      checkUserAuth();
    });
  </script>

<?php
include __DIR__ . '/includes/footer.php';
?>
