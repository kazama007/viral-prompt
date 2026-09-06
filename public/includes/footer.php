<?php
require_once __DIR__ . '/config.php';
?>
  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="container footer-grid">

      <div class="f-left">
        <div class="footer-brand">
          <img src="assets/images/logo.png" alt="<?php echo htmlspecialchars($SITE_NAME); ?>" class="brand-icon-img" width="30" height="30" style="width:30px;height:30px;">
          <span>VIRAL <b>PROMPT</b></span>
        </div>
        <p class="footer-tag">Viral AI video prompts by <?php echo htmlspecialchars($SITE_NAME); ?></p>
      </div>

      <nav class="footer-nav f-center">
        <a href="browse.php">All Prompts</a>
        <span class="footer-dot">·</span>
        <a href="community.php">Social Corner</a>
        <span class="footer-dot">·</span>
        <a href="pricing.php">Join Community</a>
        <span class="footer-dot">·</span>
        <a href="contact.php">Contact</a>
        <span class="footer-dot">·</span>
        <a href="privacy.php">Privacy</a>
        <span class="footer-dot">·</span>
        <a href="terms.php">Terms</a>
        <span class="footer-dot">·</span>
        <a href="refunds.php">Refunds</a>
      </nav>

      <div class="f-right">
        <p class="follow-label">Follow us</p>
        <div class="social-row">
          <a href="<?php echo htmlspecialchars($FACEBOOK_URL); ?>" target="_blank" rel="noopener" class="social-ic" aria-label="Facebook">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07c0 6.02 4.39 11.02 10.13 11.93v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.7 4.53-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.26h3.33l-.53 3.49h-2.8V24C19.61 23.09 24 18.09 24 12.07z"/></svg>
          </a>
          <a href="<?php echo htmlspecialchars($WHATSAPP_CHANNEL); ?>" target="_blank" rel="noopener" class="social-ic" aria-label="WhatsApp">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.21 5.1 4.5.71.31 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.09 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35zM12.05 21.79h-.01a9.72 9.72 0 0 1-4.96-1.36l-.36-.21-3.69.97.98-3.6-.23-.37a9.72 9.72 0 0 1-1.49-5.18c0-5.37 4.37-9.74 9.75-9.74a9.68 9.68 0 0 1 6.89 2.86 9.68 9.68 0 0 1 2.85 6.89c0 5.38-4.37 9.74-9.73 9.74zm8.28-18.02A11.64 11.64 0 0 0 12.05.33C5.6.33.35 5.58.35 12.03c0 2.06.54 4.07 1.56 5.84L.25 23.79l6.07-1.59a11.68 11.68 0 0 0 5.72 1.46h.01c6.45 0 11.7-5.25 11.7-11.7 0-3.13-1.22-6.07-3.42-8.19z"/></svg>
          </a>
        </div>
      </div>

    </div>
    <p class="footer-copy">© 2026 <a href="admin.php" style="color:inherit;text-decoration:none;">VIRAL PROMPT</a></p>
  </footer>

  <script src="js/auth-nav.js"></script>
<?php if (!empty($extraScripts)) echo $extraScripts; ?>
</body>
</html>
