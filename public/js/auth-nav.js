/**
 * auth-nav.js — Navigation & Auth State Sync for NavPrompts
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
    if (path === '/' || path.endsWith('index.html') || path.endsWith('index.php')) return 'index';
    return '';
  }

  window.userLogout = function () {
    localStorage.removeItem('promptmaster_token');
    localStorage.removeItem('promptmaster_user');
    localStorage.removeItem('vip_token');
    localStorage.removeItem('isVip');
    window.location.href = 'login.html';
  };

  async function updateHeaderNav() {
    const nav = document.querySelector('.main-nav');
    if (!nav) return;

    const page = getActivePage();
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

      try {
        const res = await fetch('/api/auth/me', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.ok) {
          const data = await res.json();
          if (data && data.success && data.user) {
            currentUser = data.user;
            localStorage.setItem('promptmaster_user', JSON.stringify(currentUser));
          }
        } else if (res.status === 401 || res.status === 403) {
          currentUser = null;
          localStorage.removeItem('promptmaster_token');
          localStorage.removeItem('promptmaster_user');
        }
      } catch (e) {
        console.warn('Auth check error (preserving local session):', e);
      }
    }

    if (currentUser) {
      // Subscribed / Logged In User Navigation — Exact soniprompts.com format
      nav.innerHTML = `
        <a href="index.html" class="${page === 'index' ? 'active' : ''}">Home</a>
        <a href="browse.html" class="${page === 'browse' ? 'active' : ''}">All Prompts</a>
        <a href="community.html" class="${page === 'community' ? 'active' : ''}">Social Corner</a>
        <a href="account.html" class="${page === 'account' ? 'active' : ''}">My Account</a>
        <a href="javascript:void(0)" onclick="userLogout()" class="nav-btn">Logout</a>
      `;
    } else {
      // Guest / Logged Out Navigation — Exact soniprompts.com format
      nav.innerHTML = `
        <a href="index.html" class="${page === 'index' ? 'active' : ''}">Home</a>
        <a href="browse.html" class="${page === 'browse' ? 'active' : ''}">All Prompts</a>
        <a href="community.html" class="${page === 'community' ? 'active' : ''}">Social Corner</a>
        <a href="pricing.html" class="${page === 'pricing' ? 'active' : ''}">Join Community</a>
        <a href="login.html" class="${page === 'login' ? 'active' : ''}">Login</a>
        <a href="register.html" class="nav-btn">Get Started</a>
      `;
    }
  }

  // Update when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', updateHeaderNav);
  } else {
    updateHeaderNav();
  }
})();
