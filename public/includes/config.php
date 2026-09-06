<?php
/**
 * config.php — Global Configuration for VIRAL PROMPT
 */
$SITE_NAME = 'VIRAL PROMPT';
$SITE_TAGLINE = 'Viral AI video prompts for Reels, Shorts & TikTok';
$SITE_URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:3000');
$WHATSAPP_NUMBER = '919410610800';
$WHATSAPP_CHANNEL = 'https://wa.me/' . $WHATSAPP_NUMBER;
$FACEBOOK_URL = 'https://www.facebook.com';
$CURRENT_YEAR = date('Y');
?>
