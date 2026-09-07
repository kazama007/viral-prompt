<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'My Account — ' . $SITE_NAME;
$pageDesc = 'Manage your subscription, profile details and VIP access on ' . $SITE_NAME;
$activePage = 'account';

include __DIR__ . '/includes/header.php';
?>

  <main>
    <div class="account-wrap">
      <h1 style="margin-bottom:24px;">My Account</h1>

      <div class="acc-card" id="profileCard">
        <h3>👤 Profile</h3>
        <p><strong>Name:</strong> <span id="accName">Loading...</span></p>
        <p><strong>Email:</strong> <span id="accEmail">Loading...</span></p>
        <p><strong>Joined:</strong> <span id="accJoined">Loading...</span></p>
      </div>

      <div class="acc-card" id="membershipCard">
        <h3>💳 Membership</h3>
        <div id="membershipLoading" style="color:var(--muted);font-size:14px;">Loading membership details...</div>
        <div id="membershipContent" style="display:none;">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px;flex-wrap:wrap;">
            <span class="badge badge-active" id="accBadge">Active</span>
            <span id="accPlanTag" style="font-size:12.5px;font-weight:700;color:var(--primary);background:var(--primary-light);padding:4px 12px;border-radius:999px;">VIP Plan — $2.5/mo</span>
          </div>

          <!-- Subscription Time Remaining Countdown Box -->
          <div class="time-remaining-box" id="timeRemainingBox">
            <div class="time-rem-head">
              <div class="time-rem-title">
                <span class="time-rem-pulse"></span>
                <span>⏳ Subscription Time Remaining (Time Left)</span>
              </div>
              <span class="time-rem-badge" id="remDaysBadge" style="font-size:12px;font-weight:700;color:#6D5BFB;background:#fff;padding:3px 10px;border-radius:999px;border:1px solid #DDD6FE;">24 Days Left</span>
            </div>

            <div class="countdown-grid" id="countdownGrid">
              <div class="countdown-card">
                <span class="countdown-card-num" id="remDays">00</span>
                <span class="countdown-card-lbl">Days</span>
              </div>
              <span class="countdown-colon">:</span>
              <div class="countdown-card">
                <span class="countdown-card-num" id="remHours">00</span>
                <span class="countdown-card-lbl">Hours</span>
              </div>
              <span class="countdown-colon">:</span>
              <div class="countdown-card">
                <span class="countdown-card-num" id="remMins">00</span>
                <span class="countdown-card-lbl">Minutes</span>
              </div>
              <span class="countdown-colon">:</span>
              <div class="countdown-card">
                <span class="countdown-card-num" id="remSecs">00</span>
                <span class="countdown-card-lbl">Seconds</span>
              </div>
            </div>

            <!-- Subscription Progress Bar -->
            <div class="sub-progress-wrap">
              <div class="sub-progress-bar" id="subProgressBar" style="width: 100%;"></div>
            </div>
            <div class="sub-progress-info">
              <span id="subProgressLabel">Calculating time remaining...</span>
              <span id="subProgressPercent">--% left</span>
            </div>
          </div>

          <p id="accValidRow" style="margin-bottom:6px;"><strong>Valid until:</strong> <span id="accValidUntil">...</span></p>
          <p id="accActivatedRow" style="font-size:13.5px;color:var(--muted);margin-bottom:14px;"><strong>Activated on:</strong> <span id="accActivated">...</span></p>

          <div id="accBtnGroup" style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
            <a href="/browse" class="btn btn-primary" id="accPrimaryBtn">Browse Prompts</a>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Premium member WhatsApp support floating button -->
  <a href="https://wa.me/919410610800?text=Hi%21+I%27m+a+premium+member+of+your+VIRAL+PROMPT+community.+I+need+some+help." target="_blank" rel="noopener" class="wa-float" title="WhatsApp Support">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="#fff"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.21 5.1 4.5.71.31 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.09 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35zM12.05 21.79h-.01a9.72 9.72 0 0 1-4.96-1.36l-.36-.21-3.69.97.98-3.6-.23-.37a9.72 9.72 0 0 1-1.49-5.18c0-5.37 4.37-9.74 9.75-9.74a9.68 9.68 0 0 1 6.89 2.86 9.68 9.68 0 0 1 2.85 6.89c0 5.38-4.37 9.74-9.73 9.74zm8.28-18.02A11.64 11.64 0 0 0 12.05.33C5.6.33.35 5.58.35 12.03c0 2.06.54 4.07 1.56 5.84L.25 23.79l6.07-1.59a11.68 11.68 0 0 0 5.72 1.46h.01c6.45 0 11.7-5.25 11.7-11.7 0-3.13-1.22-6.07-3.42-8.19z"/></svg>
    <span class="wa-float-label">Support</span>
  </a>

  <script>
    async function loadAccountData() {
      const urlParams = new URLSearchParams(window.location.search);
      const urlToken = urlParams.get('token');
      if (urlToken) {
        localStorage.setItem('promptmaster_token', urlToken);
      }
      const token = localStorage.getItem('promptmaster_token') || localStorage.getItem('vip_token') || urlToken;
      if (!token) {
        window.location.href = '/login?next=/account';
        return;
      }

      try {
        const res = await fetch('/api/auth/me', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.status === 401 || res.status === 403) {
          localStorage.removeItem('promptmaster_token');
          localStorage.removeItem('promptmaster_user');
          window.location.href = '/login?next=/account';
          return;
        }
        const data = await res.json();
        let user = data.user;
        if (!user) {
          try {
            user = JSON.parse(localStorage.getItem('promptmaster_user'));
          } catch (e) {}
        }
        if (!user) {
          window.location.href = '/login?next=/account';
          return;
        }
        localStorage.setItem('promptmaster_user', JSON.stringify(user));
        document.getElementById('accName').textContent = user.name || 'User';
        document.getElementById('accEmail').textContent = user.email || '';
        document.getElementById('accJoined').textContent = user.createdAtFormatted || '19 Aug 2026';

        const isVip = user.subscription && (user.subscription.status === 'active' || user.subscription.isActive);
        const badge = document.getElementById('accBadge');
        const validText = document.getElementById('accValidUntil');
        const validRow = document.getElementById('accValidRow');
        const btnGroup = document.getElementById('accBtnGroup');

        let countdownTimer = null;

        function startLiveCountdown(expiresAt, grantedAt) {
          if (countdownTimer) clearInterval(countdownTimer);

          const expTime = new Date(expiresAt).getTime();
          const startTime = grantedAt ? new Date(grantedAt).getTime() : (expTime - 30 * 24 * 60 * 60 * 1000);
          const totalDuration = Math.max(1, expTime - startTime);

          function updateTimer() {
            const now = Date.now();
            const diff = expTime - now;

            if (diff <= 0) {
              document.getElementById('remDays').textContent = '00';
              document.getElementById('remHours').textContent = '00';
              document.getElementById('remMins').textContent = '00';
              document.getElementById('remSecs').textContent = '00';
              document.getElementById('remDaysBadge').textContent = 'Expired';
              document.getElementById('subProgressBar').style.width = '0%';
              document.getElementById('subProgressLabel').textContent = 'Subscription has expired';
              document.getElementById('subProgressPercent').textContent = '0% left';
              badge.className = 'badge badge-inactive';
              badge.textContent = 'Expired';
              if (countdownTimer) clearInterval(countdownTimer);
              return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('remDays').textContent = String(days).padStart(2, '0');
            document.getElementById('remHours').textContent = String(hours).padStart(2, '0');
            document.getElementById('remMins').textContent = String(minutes).padStart(2, '0');
            document.getElementById('remSecs').textContent = String(seconds).padStart(2, '0');

            const daysCeil = Math.ceil(diff / (1000 * 60 * 60 * 24));
            document.getElementById('remDaysBadge').textContent = `${daysCeil} Days Left`;

            const percentLeft = Math.max(0, Math.min(100, (diff / totalDuration) * 100));
            document.getElementById('subProgressBar').style.width = percentLeft.toFixed(1) + '%';
            document.getElementById('subProgressLabel').textContent = `${days} days, ${hours} hours, ${minutes} mins left`;
            document.getElementById('subProgressPercent').textContent = `${percentLeft.toFixed(0)}% remaining`;
          }

          updateTimer();
          countdownTimer = setInterval(updateTimer, 1000);
        }

        if (isVip) {
          badge.className = 'badge badge-active';
          badge.textContent = 'Active';
          document.getElementById('accPlanTag').textContent = 'VIP Access — $2.5/mo';
          const expDate = user.subscription.expiresAtFormatted || '29 Sep 2026';
          const daysLeft = typeof user.subscription.daysLeft !== 'undefined' ? user.subscription.daysLeft : 24;
          validText.textContent = `${expDate} (${daysLeft} days left)`;
          document.getElementById('accActivated').textContent = user.subscription.grantedAtFormatted || user.createdAtFormatted || 'N/A';
          document.getElementById('timeRemainingBox').style.display = 'block';

          startLiveCountdown(user.subscription.expiresAt, user.subscription.grantedAt);

          btnGroup.innerHTML = `
            <a href="/browse" class="btn btn-primary">Browse Prompts</a>
            <a href="https://wa.me/919410610800?text=Hi%21+I%27m+a+VIRAL+PROMPT+VIP+subscriber.+I+want+to+extend+my+subscription." target="_blank" rel="noopener" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px;">
              💬 Extend on WhatsApp
            </a>
          `;
        } else {
          document.getElementById('timeRemainingBox').style.display = 'none';
          const isExp = user.subscription && user.subscription.status === 'expired';
          badge.className = 'badge badge-inactive';
          badge.textContent = isExp ? 'Expired' : 'Inactive';
          document.getElementById('accPlanTag').textContent = isExp ? 'Expired Plan' : 'Free Member';
          if (isExp && user.subscription.expiresAtFormatted) {
            validText.textContent = `${user.subscription.expiresAtFormatted} (Expired)`;
          } else {
            validRow.innerHTML = `<strong>Status:</strong> No active subscription`;
          }
          document.getElementById('accActivatedRow').style.display = 'none';
          btnGroup.innerHTML = `
            <a href="https://wa.me/919410610800?text=Hi%21+I+want+to+activate+VIP+subscription+on+VIRAL+PROMPT+for+2.5+dollars" target="_blank" rel="noopener" class="btn btn-primary" style="background:#25D366;border-color:#25D366;">Activate VIP on WhatsApp ($2.5/mo)</a>
            <a href="/browse" class="btn btn-outline" style="margin-left:8px;">Browse Free Prompts</a>
          `;
        }

        document.getElementById('membershipLoading').style.display = 'none';
        document.getElementById('membershipContent').style.display = 'block';

      } catch (err) {
        console.error('Failed to load account:', err);
        document.getElementById('membershipLoading').textContent = 'Failed to load profile details. Please try again.';
      }
    }

    loadAccountData();
  </script>

<?php
include __DIR__ . '/includes/footer.php';
?>
