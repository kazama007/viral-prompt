<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Privacy Policy — ' . $SITE_NAME;
$pageDesc = 'Privacy Policy for ' . $SITE_NAME . ' website and prompt services.';
$activePage = 'privacy';

include __DIR__ . '/includes/header.php';
?>

  <main class="prompt-single">
    <h1 style="margin-bottom:6px;">Privacy Policy</h1>
    <p style="color:var(--muted);margin-bottom:28px;font-weight:500;">Last updated: September 2026</p>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:32px;box-shadow:var(--shadow-sm);line-height:1.7;">
      <h3 style="margin:0 0 10px;">1. Introduction</h3>
      <p style="margin-bottom:18px;"><?php echo htmlspecialchars($SITE_NAME); ?> ("we", "our", "us") respects your privacy and is committed to protecting your personal data. This privacy policy explains how we collect and use information when you use our website.</p>

      <h3 style="margin:0 0 10px;">2. Information We Collect</h3>
      <p style="margin-bottom:18px;">We only collect minimal details necessary to provide you access to our digital prompt marketplace, such as your email address and basic profile information when registering or connecting via WhatsApp.</p>

      <h3 style="margin:0 0 10px;">3. Data Security</h3>
      <p style="margin-bottom:18px;">We apply strong technical safeguards to ensure that your private data is never sold, leased, or distributed to third parties.</p>

      <h3 style="margin:0 0 10px;">4. Contact Us</h3>
      <p style="margin-bottom:0;">If you have any questions regarding this Privacy Policy, you may contact us at support@viralprompt.com or via WhatsApp at +91 94106 10800.</p>
    </div>
  </main>

<?php
include __DIR__ . '/includes/footer.php';
?>
