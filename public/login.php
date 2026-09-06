<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Login — ' . $SITE_NAME;
$pageDesc = 'Sign in to access your prompts & saved collections on ' . $SITE_NAME;
$activePage = 'login';

include __DIR__ . '/includes/header.php';
?>

  <main class="container" style="padding-top:40px;padding-bottom:60px;">
    <div style="max-width:440px;margin:0 auto;background:#fff;border:1px solid var(--border);border-radius:20px;padding:36px 30px;box-shadow:var(--shadow-sm);">
      <h1 style="font-size:24px;font-weight:800;text-align:center;margin-bottom:6px;">Welcome Back</h1>
      <p style="color:var(--muted);font-size:14px;text-align:center;margin-bottom:24px;">Sign in to access your prompts &amp; saved collections</p>

      <div id="loginAlert" style="display:none;padding:10px 14px;border-radius:10px;font-size:13.5px;margin-bottom:16px;font-weight:600;"></div>

      <form onsubmit="handleLoginSubmit(event)">
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="loginEmail" class="form-control" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" id="loginPass" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:10px;">Login with Password</button>
      </form>

      <p style="text-align:center;font-size:14px;color:var(--muted);margin-top:20px;">
        Don't have an account? <a href="register.php" style="font-weight:700;">Create Account</a>
      </p>
    </div>
  </main>

  <script>
    async function handleLoginSubmit(e) {
      e.preventDefault();
      const btn = e.target.querySelector('button[type="submit"]');
      const alertBox = document.getElementById('loginAlert');
      alertBox.style.display = 'none';

      const email = document.getElementById('loginEmail').value.trim();
      const password = document.getElementById('loginPass').value;

      btn.disabled = true;
      btn.textContent = 'Signing in...';

      try {
        const res = await fetch('/api/auth/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });
        const data = await res.json();
        if (data.success) {
          localStorage.setItem('promptmaster_token', data.token);
          localStorage.setItem('promptmaster_user', JSON.stringify(data.user));
          alertBox.style.background = '#e6f9ed';
          alertBox.style.color = '#128c46';
          alertBox.style.border = '1px solid #b7ecc8';
          alertBox.innerHTML = '✅ Welcome back, ' + data.user.name + '! Redirecting...';
          alertBox.style.display = 'block';
          const nextUrl = new URLSearchParams(window.location.search).get('next') || 'index.php';
          setTimeout(() => {
            window.location.href = nextUrl;
          }, 800);
        } else {
          alertBox.style.background = '#ffebee';
          alertBox.style.color = '#c62828';
          alertBox.style.border = '1px solid #ffcdd2';
          alertBox.textContent = '❌ ' + (data.message || 'Login failed');
          alertBox.style.display = 'block';
          btn.disabled = false;
          btn.textContent = 'Login with Password';
        }
      } catch (err) {
        alertBox.style.background = '#ffebee';
        alertBox.style.color = '#c62828';
        alertBox.style.border = '1px solid #ffcdd2';
        alertBox.textContent = '❌ Server connection error: ' + err.message;
        alertBox.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Login with Password';
      }
    }
  </script>

<?php
include __DIR__ . '/includes/footer.php';
?>
