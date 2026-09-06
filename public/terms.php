<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Terms & Conditions — ' . $SITE_NAME;
$pageDesc = 'Terms and Conditions for using ' . $SITE_NAME . ' AI prompts and VIP membership.';
$activePage = 'terms';

include __DIR__ . '/includes/header.php';
?>

  <main class="prompt-single">
    <h1 style="margin-bottom:6px;">Terms &amp; Conditions</h1>
    <p style="color:var(--muted);margin-bottom:28px;font-weight:500;">Last updated: September 2026</p>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:32px;box-shadow:var(--shadow-sm);line-height:1.7;">
      <h3 style="margin:0 0 10px;">1. About the Service</h3>
      <p style="margin-bottom:18px;"><?php echo htmlspecialchars($SITE_NAME); ?> provides curated and engineered digital prompt systems for AI video and image creators, compatible with tools including Kling AI, Seedance, Runway, Luma, and Veo.</p>

      <h3 style="margin:0 0 10px;">2. License &amp; Intellectual Property</h3>
      <p style="margin-bottom:18px;">Users may use generated videos and outputs for both personal and commercial social media accounts (e.g. YouTube Shorts, Reels, TikTok). Direct re-selling or redistribution of the prompt code itself as a prompt database is strictly prohibited.</p>

      <h3 style="margin:0 0 10px;">3. Community Rules</h3>
      <p style="margin-bottom:0;">Members of the VIP community are expected to maintain respectful, productive discourse. Violations of guidelines may result in termination of community privileges.</p>
    </div>
  </main>

<?php
include __DIR__ . '/includes/footer.php';
?>
