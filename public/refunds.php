<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Refund & Cancellation Policy — ' . $SITE_NAME;
$pageDesc = 'Refund and Cancellation Policy for ' . $SITE_NAME . ' digital products.';
$activePage = 'refunds';

include __DIR__ . '/includes/header.php';
?>

  <main class="prompt-single">
    <h1 style="margin-bottom:6px;">Refund &amp; Cancellation Policy</h1>
    <p style="color:var(--muted);margin-bottom:28px;font-weight:500;">Last updated: September 2026</p>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:32px;box-shadow:var(--shadow-sm);line-height:1.7;">
      <h3 style="margin:0 0 10px;">1. Digital Product — Instant Access</h3>
      <p style="margin-bottom:18px;"><?php echo htmlspecialchars($SITE_NAME); ?> is a digital service where complete access to curated system prompts and workflows is granted immediately upon VIP subscription.</p>

      <h3 style="margin:0 0 10px;">2. Cancellation</h3>
      <p style="margin-bottom:18px;">You may cancel your recurring monthly membership at any time with zero penalty. Simply notify us via WhatsApp or email prior to your next renewal date.</p>

      <h3 style="margin:0 0 10px;">3. Support &amp; Assistance</h3>
      <p style="margin-bottom:0;">If you ever encounter technical difficulty with accessing your prompts or need assistance tailoring prompts to your specific AI tool, our dedicated team is available 24/7 on WhatsApp.</p>
    </div>
  </main>

<?php
include __DIR__ . '/includes/footer.php';
?>
