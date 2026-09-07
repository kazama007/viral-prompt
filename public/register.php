<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Get Started — ' . $SITE_NAME;
$pageDesc = 'Join thousands of creators using ' . $SITE_NAME . ' systems';
$activePage = 'register';

include __DIR__ . '/includes/header.php';
?>

  <main class="container" style="padding-top:40px;padding-bottom:60px;">
    <div style="max-width:440px;margin:0 auto;background:#fff;border:1px solid var(--border);border-radius:20px;padding:36px 30px;box-shadow:var(--shadow-sm);">
      <h1 style="font-size:24px;font-weight:800;text-align:center;margin-bottom:6px;">Create Account</h1>
      <p style="color:var(--muted);font-size:14px;text-align:center;margin-bottom:24px;">Join thousands of creators using <?php echo htmlspecialchars($SITE_NAME); ?> systems</p>

      <div id="regAlert" style="display:none;padding:10px 14px;border-radius:10px;font-size:13.5px;margin-bottom:16px;font-weight:600;"></div>

      <form onsubmit="handleRegisterSubmit(event)">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" id="regName" class="form-control" placeholder="Your Name" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="regEmail" class="form-control" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" id="regPass" class="form-control" placeholder="Create Password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:10px;">Get Started Free</button>
      </form>

      <p style="text-align:center;font-size:14px;color:var(--muted);margin-top:20px;">
        Already have an account? <a href="/login" style="font-weight:700;">Login</a>
      </p>
    </div>
  </main>

  <script>
    async function handleRegisterSubmit(e) {
      e.preventDefault();
      const btn = e.target.querySelector('button[type="submit"]');
      const alertBox = document.getElementById('regAlert');
      alertBox.style.display = 'none';

      const name = document.getElementById('regName').value.trim();
      const email = document.getElementById('regEmail').value.trim();
      const password = document.getElementById('regPass').value;

      btn.disabled = true;
      btn.textContent = 'Creating Account...';

      try {
        const res = await fetch('/api/auth/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, email, password })
        });
        const data = await res.json();
        if (data.success) {
          localStorage.setItem('promptmaster_token', data.token);
          localStorage.setItem('promptmaster_user', JSON.stringify(data.user));
          alertBox.style.background = '#e6f9ed';
          alertBox.style.color = '#128c46';
          alertBox.style.border = '1px solid #b7ecc8';
          alertBox.innerHTML = '✅ Account created successfully! Redirecting...';
          alertBox.style.display = 'block';
          let nextUrl = new URLSearchParams(window.location.search).get('next') || '/';
          if (nextUrl === 'index.php' || nextUrl === '/index.php') nextUrl = '/';
          else if (nextUrl.includes('.php')) nextUrl = nextUrl.replace(/\.php(\?|$)/, '$1');
          setTimeout(() => {
            window.location.href = nextUrl;
          }, 800);
        } else {
          alertBox.style.background = '#ffebee';
          alertBox.style.color = '#c62828';
          alertBox.style.border = '1px solid #ffcdd2';
          alertBox.textContent = '❌ ' + (data.message || 'Registration failed');
          alertBox.style.display = 'block';
          btn.disabled = false;
          btn.textContent = 'Get Started Free';
        }
      } catch (err) {
        alertBox.style.background = '#ffebee';
        alertBox.style.color = '#c62828';
        alertBox.style.border = '1px solid #ffcdd2';
        alertBox.textContent = '❌ Server connection error: ' + err.message;
        alertBox.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Get Started Free';
      }
    }
  </script>

<?php
include __DIR__ . '/includes/footer.php';
?>
