/**
 * auth-nav.js — Navigation & Auth State Sync for VIRAL PROMPT
 * Matches soniprompts.com navigation structure 1:1
 */

(function () {
  function getActivePage() {
    const path = window.location.pathname.toLowerCase();
    if (path.includes('browse')) return 'browse';
    if (path.includes('community')) return 'community';
    if (path.includes('account')) return 'account';
    if (path.includes('pricing')) return 'pricing';
    if (path.includes('contact')) return 'contact';
    if (path.includes('privacy')) return 'privacy';
    if (path.includes('terms')) return 'terms';
    if (path.includes('refund')) return 'refund';
    if (path.includes('login')) return 'login';
    if (path.includes('register')) return 'register';
    if (path.includes('prompt')) return 'prompt';
    if (path === '/' || path.endsWith('/index') || path.endsWith('index.html') || path.endsWith('index.php')) return 'index';
    return '';
  }

  window.userLogout = function () {
    localStorage.removeItem('promptmaster_token');
    localStorage.removeItem('promptmaster_user');
    localStorage.removeItem('vip_token');
    localStorage.removeItem('isVip');
    window.location.href = '/login';
  };

  function renderNav(currentUser) {
    const nav = document.querySelector('.main-nav');
    if (!nav) return;
    const page = getActivePage();

    if (currentUser) {
      nav.innerHTML = `
        <a href="/" class="${page === 'index' ? 'active' : ''}">Home</a>
        <a href="/browse" class="${page === 'browse' ? 'active' : ''}">All Prompts</a>
        <a href="/community" class="${page === 'community' ? 'active' : ''}">Social Corner</a>
        <a href="/account" class="${page === 'account' ? 'active' : ''}">My Account</a>
        <a href="javascript:void(0)" onclick="userLogout()" class="nav-btn">Logout</a>
      `;
    } else {
      nav.innerHTML = `
        <a href="/" class="${page === 'index' ? 'active' : ''}">Home</a>
        <a href="/browse" class="${page === 'browse' ? 'active' : ''}">All Prompts</a>
        <a href="/community" class="${page === 'community' ? 'active' : ''}">Social Corner</a>
        <a href="/pricing" class="${page === 'pricing' ? 'active' : ''}">Join Community</a>
        <a href="/login" class="${page === 'login' ? 'active' : ''}">Login</a>
        <a href="/register" class="nav-btn">Get Started</a>
      `;
    }
  }

  function updateHeaderNav() {
    const nav = document.querySelector('.main-nav');
    if (!nav) return;

    const urlParams = new URLSearchParams(window.location.search);
    const urlToken = urlParams.get('token');
    if (urlToken) {
      localStorage.setItem('promptmaster_token', urlToken);
    }
    const token = localStorage.getItem('promptmaster_token') || localStorage.getItem('vip_token') || urlToken;

    let currentUser = null;
    if (token) {
      try {
        const cached = localStorage.getItem('promptmaster_user');
        if (cached) currentUser = JSON.parse(cached);
      } catch (e) {}
    }

    // Instant zero-latency render with cached user state
    renderNav(currentUser);

    // Verify token with backend in background without delaying user experience
    if (token) {
      fetch('/api/auth/me', {
        headers: { 'Authorization': `Bearer ${token}` }
      })
        .then(res => {
          if (res.ok) return res.json();
          if (res.status === 401 || res.status === 403) {
            localStorage.removeItem('promptmaster_token');
            localStorage.removeItem('promptmaster_user');
            renderNav(null);
          }
          return null;
        })
        .then(data => {
          if (data && data.success && data.user) {
            localStorage.setItem('promptmaster_user', JSON.stringify(data.user));
            if (!currentUser) renderNav(data.user);
          }
        })
        .catch(e => {
          console.warn('Auth check error (preserving local session):', e);
        });
    }
  }

  // Update when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', updateHeaderNav);
  } else {
    updateHeaderNav();
  }
})();
