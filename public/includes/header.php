<?php
require_once __DIR__ . '/config.php';
$pageTitle = isset($pageTitle) ? $pageTitle : $SITE_NAME . ' — Viral AI Video Prompts';
$pageDesc = isset($pageDesc) ? $pageDesc : 'Viral AI video prompts for Reels, Shorts & TikTok. Works with Kling, Veo, Runway & any AI video tool.';
$activePage = isset($activePage) ? $activePage : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="icon" type="image/png" href="/favicon.png">
  <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="dns-prefetch" href="https://fonts.googleapis.com">
  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
  <link rel="dns-prefetch" href="https://raw.githubusercontent.com">
  <link rel="preconnect" href="https://raw.githubusercontent.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="/css/soni-style.css">
  <?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
<body>

  <!-- Site Header -->
  <header class="site-header">
    <div class="container header-inner">
      <a href="/" class="brand">
        <img src="/assets/images/logo.png" alt="<?php echo htmlspecialchars($SITE_NAME); ?>" class="brand-icon-img" width="36" height="36">
        <span>VIRAL <b>PROMPT</b></span>
      </a>

      <input type="checkbox" id="nav-toggle" class="nav-toggle-cb">
      <label for="nav-toggle" class="nav-toggle" aria-label="Toggle navigation">☰</label>

      <nav class="main-nav">
        <a href="/" class="<?php echo $activePage === 'home' ? 'active' : ''; ?>">Home</a>
        <a href="/browse" class="<?php echo $activePage === 'browse' ? 'active' : ''; ?>">All Prompts</a>
        <a href="/community" class="<?php echo $activePage === 'community' ? 'active' : ''; ?>">Social Corner</a>
        <a href="/pricing" class="<?php echo $activePage === 'pricing' ? 'active' : ''; ?>">Join Community</a>
        <div id="authNavButtons" style="display:inline-flex;align-items:center;gap:12px;">
          <a href="/login">Login</a>
          <a href="/register" class="nav-btn">Get Started</a>
        </div>
      </nav>
    </div>
  </header>
