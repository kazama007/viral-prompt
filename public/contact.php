<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Contact Us — ' . $SITE_NAME;
$pageDesc = 'Contact ' . $SITE_NAME . ' for inquiries, custom prompt engineering, and VIP community access.';
$activePage = 'contact';

include __DIR__ . '/includes/header.php';
?>

  <main class="prompt-single" style="max-width:640px;margin:40px auto;padding:0 20px;">
    <h1 style="margin-bottom:6px;">Contact Us</h1>
    <p style="color:var(--muted);margin-bottom:28px;font-weight:500;">We usually reply within 24 hours.</p>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;margin-bottom:24px;box-shadow:var(--shadow-sm);">
      <p style="margin-bottom:12px;font-size:15px;"><strong>💬 WhatsApp Support:</strong> +91 91314 21048</p>
      <p style="margin-bottom:12px;font-size:15px;"><strong>📧 Email:</strong> support@viralprompt.com</p>
      <p style="margin-bottom:0;font-size:15px;"><strong>⏰ Working Hours:</strong> Mon – Sat, 10:00 AM – 8:00 PM IST</p>
    </div>

    <p style="text-align:center;">
      <a href="https://wa.me/919131421048?text=Hello%20PROMPT%20MASTER%2C%20I%20have%20an%20inquiry" target="_blank" rel="noopener" class="btn btn-green btn-lg btn-block">
        💬 Chat Directly on WhatsApp
      </a>
    </p>
  </main>

<?php
include __DIR__ . '/includes/footer.php';
?>
