<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Community — ' . $SITE_NAME;
$pageDesc = 'Share your wins, results and viral video creations with the ' . $SITE_NAME . ' creator community.';
$activePage = 'community';

include __DIR__ . '/includes/header.php';
?>

  <main>
    <div class="community-wrap">

      <h1 class="section-title" style="margin-top:34px;">Community</h1>
      <p class="section-sub">Share your wins, results &amp; creations 🚀</p>

      <!-- Guest Login Banner (Hidden if logged in) -->
      <div class="mem-banner" id="communityGuestBanner" style="display:none;">
        <span>👋 Login to like posts — join community to post &amp; comment.</span>
        <a href="/login?next=/community" class="btn btn-primary" style="padding:8px 22px;border-radius:999px;font-size:14px;text-decoration:none;">Login Free</a>
      </div>

      <!-- Post Composer (Shown when logged in) -->
      <div class="composer" id="communityComposer" style="display:none;">
        <form id="communityPostForm" onsubmit="handlePostSubmit(event)">
          <textarea id="postContentInput" placeholder="Share something with the community..." required></textarea>
          
          <div id="mediaPreviewRow" style="display:none;margin-top:10px;align-items:center;gap:8px;">
            <span id="mediaPreviewName" style="color:var(--green);font-size:13px;font-weight:600;"></span>
            <button type="button" onclick="clearSelectedMedia()" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:12px;font-weight:700;">✕ Remove</button>
          </div>

          <div class="composer-foot">
            <label class="composer-attach" for="mediaFileInput">
              📎 Photo / Video
              <input type="file" id="mediaFileInput" accept="image/*,video/*" style="display:none;" onchange="handleMediaSelect(event)">
            </label>
            <button type="submit" class="btn btn-primary" id="postSubmitBtn" style="padding:9px 24px;border-radius:999px;font-weight:700;">Post</button>
          </div>
          
          <div style="font-size:12px;color:var(--muted);margin-top:8px;">
            Max 20MB · JPG, PNG, GIF, MP4, WebM · Posts appear after admin approval
          </div>
        </form>
      </div>

      <!-- Community Feed -->
      <div id="communityFeed">
        <div style="text-align:center;padding:40px 20px;color:var(--muted);">Loading community posts...</div>
      </div>

    </div>
  </main>

  <!-- WhatsApp Support Float -->
  <a href="https://wa.me/919410610800?text=Hi%21+I%27m+a+member+of+your+community.+I+need+some+help."
     target="_blank" rel="noopener" class="wa-float" title="WhatsApp Support">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.21 5.1 4.5.71.31 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.09 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35zM12.05 21.79h-.01a9.72 9.72 0 0 1-4.96-1.36l-.36-.21-3.69.97.98-3.6-.23-.37a9.72 9.72 0 0 1-1.49-5.18c0-5.37 4.37-9.74 9.75-9.74a9.68 9.68 0 0 1 6.89 2.86 9.68 9.68 0 0 1 2.85 6.89c0 5.38-4.37 9.74-9.73 9.74zm8.28-18.02A11.64 11.64 0 0 0 12.05.33C5.6.33.35 5.58.35 12.03c0 2.06.54 4.07 1.56 5.84L.25 23.79l6.07-1.59a11.68 11.68 0 0 0 5.72 1.46h.01c6.45 0 11.7-5.25 11.7-11.7 0-3.13-1.22-6.07-3.42-8.19z"/></svg>
    <span class="wa-float-label">Support</span>
  </a>

  <!-- Toast Container -->
  <div id="toastNotification" style="display:none;position:fixed;bottom:85px;left:50%;transform:translateX(-50%);background:#17151F;color:#fff;padding:12px 22px;border-radius:999px;font-size:14px;font-weight:600;z-index:9999;box-shadow:0 4px 14px rgba(0,0,0,0.25);">
    <span id="toastMsg"></span>
  </div>

  <script>
    let currentUser = null;
    let selectedMediaFile = null;

    function showToast(msg) {
      const toast = document.getElementById('toastNotification');
      document.getElementById('toastMsg').textContent = msg;
      toast.style.display = 'flex';
      setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }

    async function checkUserAuth() {
      const urlParams = new URLSearchParams(window.location.search);
      const urlToken = urlParams.get('token');
      if (urlToken) {
        localStorage.setItem('promptmaster_token', urlToken);
      }
      const token = localStorage.getItem('promptmaster_token') || localStorage.getItem('vip_token');
      if (!token) {
        renderAuthUI(null);
        return;
      }

      try {
        const res = await fetch('/api/auth/me', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (data.success && data.user) {
          currentUser = data.user;
          localStorage.setItem('promptmaster_user', JSON.stringify(currentUser));
          renderAuthUI(currentUser);
        } else {
          currentUser = null;
          localStorage.removeItem('promptmaster_token');
          renderAuthUI(null);
        }
      } catch (err) {
        console.error('Auth check error:', err);
        renderAuthUI(null);
      }
    }

    function renderAuthUI(user) {
      const composer = document.getElementById('communityComposer');
      const guestBanner = document.getElementById('communityGuestBanner');
      if (user) {
        composer.style.display = 'block';
        guestBanner.style.display = 'none';
      } else {
        composer.style.display = 'none';
        guestBanner.style.display = 'flex';
      }
    }

    function handleMediaSelect(e) {
      const file = e.target.files[0];
      if (!file) return;
      selectedMediaFile = file;
      document.getElementById('mediaPreviewName').textContent = '📎 Attached: ' + file.name + ' (' + (file.size / (1024 * 1024)).toFixed(1) + ' MB)';
      document.getElementById('mediaPreviewRow').style.display = 'flex';
    }

    function clearSelectedMedia() {
      selectedMediaFile = null;
      document.getElementById('mediaFileInput').value = '';
      document.getElementById('mediaPreviewRow').style.display = 'none';
    }

    async function handlePostSubmit(e) {
      e.preventDefault();
      const content = document.getElementById('postContentInput').value.trim();
      if (!content && !selectedMediaFile) {
        showToast('❌ Please write something to post.');
        return;
      }

      const token = localStorage.getItem('promptmaster_token') || localStorage.getItem('vip_token');
      if (!token) {
        window.location.href = '/login?next=/community';
        return;
      }

      const btn = document.getElementById('postSubmitBtn');
      btn.disabled = true;
      btn.textContent = 'Posting...';

      try {
        const formData = new FormData();
        formData.append('content', content);
        if (selectedMediaFile) {
          formData.append('media', selectedMediaFile);
        }

        const res = await fetch('/api/community/posts', {
          method: 'POST',
          headers: { 'Authorization': `Bearer ${token}` },
          body: formData
        });
        const data = await res.json();
        if (data.success && data.post) {
          document.getElementById('postContentInput').value = '';
          clearSelectedMedia();
          showToast('✅ Post published successfully!');
          loadCommunityPosts();
        } else {
          showToast('❌ ' + (data.message || 'Failed to post.'));
        }
      } catch (err) {
        console.error('Post submit error:', err);
        showToast('❌ Error posting to community.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Post';
      }
    }

    async function loadCommunityPosts() {
      const feed = document.getElementById('communityFeed');
      try {
        const res = await fetch('/api/community/posts');
        const data = await res.json();
        if (data.success && data.posts) {
          renderPosts(data.posts);
        } else {
          feed.innerHTML = '<div style="text-align:center;color:var(--muted);padding:30px;">No posts yet. Be the first to share!</div>';
        }
      } catch (err) {
        console.error('Load posts error:', err);
        feed.innerHTML = '<div style="text-align:center;color:#ef4444;padding:30px;">Failed to load posts. Please refresh.</div>';
      }
    }

    function renderPosts(posts) {
      const feed = document.getElementById('communityFeed');
      if (!posts || !posts.length) {
        feed.innerHTML = '<div style="text-align:center;color:var(--muted);padding:30px;">No posts yet. Be the first to share!</div>';
        return;
      }

      feed.innerHTML = posts.map(p => {
        const avatar = p.authorAvatar || (p.author ? p.author.charAt(0).toUpperCase() : 'U');
        const adminBadge = p.isAdmin ? `<span class="cpost-admin-badge">Creator ✔</span>` : '';
        let mediaHtml = '';
        if (p.mediaUrl) {
          if (p.mediaType === 'video' || p.mediaUrl.endsWith('.mp4') || p.mediaUrl.endsWith('.webm')) {
            mediaHtml = `<div class="cpost-media"><video src="${p.mediaUrl}" controls></video></div>`;
          } else {
            mediaHtml = `<div class="cpost-media"><img src="${p.mediaUrl}" alt="Attachment" loading="lazy"></div>`;
          }
        }

        const comments = p.comments || [];
        const commentsHtml = comments.map(c => `
          <div class="ccomment">
            <strong>${escapeHtml(c.author)}</strong>
            <span>${escapeHtml(c.text)}</span>
          </div>
        `).join('');

        return `
          <div class="cpost" id="${p.id}">
            <div class="cpost-head">
              <span class="cpost-avatar">${avatar}</span>
              <div>
                <strong>${escapeHtml(p.author)}</strong>
                ${adminBadge}
                <div class="cpost-time">${p.time || 'Recently'}</div>
              </div>
            </div>

            <div class="cpost-text">${formatPostText(p.content)}</div>
            ${mediaHtml}

            <div class="cpost-actions">
              <button type="button" class="cact" id="like-${p.id}" onclick="likePost('${p.id}')">
                🤍 <span id="like-count-${p.id}">${p.likes || 0}</span>
              </button>
              <button type="button" class="cact" onclick="toggleComments('${p.id}')">
                💬 <span id="comment-count-${p.id}">${comments.length}</span>
              </button>
              <button type="button" class="cact" onclick="sharePost('${p.id}')">
                🔗 Share
              </button>
            </div>

            <div class="cpost-comments" id="comments-${p.id}" style="display:none;">
              <div id="comment-list-${p.id}">${commentsHtml}</div>
              <div style="display:flex;gap:8px;margin-top:10px;">
                <input type="text" id="comment-input-${p.id}" placeholder="Write a comment…" 
                       style="flex:1;padding:8px 14px;border:1.5px solid var(--border);border-radius:999px;font-size:13.5px;outline:none;"
                       onkeydown="if(event.key==='Enter') submitComment('${p.id}')">
                <button type="button" class="btn btn-primary" onclick="submitComment('${p.id}')" style="padding:6px 16px;border-radius:999px;font-size:13px;">Reply</button>
              </div>
            </div>
          </div>
        `;
      }).join('');
    }

    function formatPostText(text) {
      if (!text) return '';
      let safe = escapeHtml(text);
      safe = safe.replace(/##\s+([^\n]+)/g, '<h2 style="font-size:17px;font-weight:800;margin:4px 0 10px;color:var(--text);">$1</h2>');
      safe = safe.replace(/\*\*([^*]+)\*\*/g, '<b>$1</b>');
      safe = safe.replace(/\*([^*]+)\*/g, '<i>$1</i>');
      return safe;
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    async function likePost(postId) {
      const btn = document.getElementById(`like-${postId}`);
      const countEl = document.getElementById(`like-count-${postId}`);
      try {
        const res = await fetch(`/api/community/posts/${postId}/like`, { method: 'POST' });
        const data = await res.json();
        if (data.success) {
          countEl.textContent = data.likes;
          btn.classList.add('liked');
          btn.innerHTML = `❤️ <span id="like-count-${postId}">${data.likes}</span>`;
        }
      } catch (e) {
        console.error('Like error:', e);
      }
    }

    function toggleComments(postId) {
      const box = document.getElementById(`comments-${postId}`);
      if (!box) return;
      box.style.display = box.style.display === 'none' ? 'block' : 'none';
    }

    async function submitComment(postId) {
      const input = document.getElementById(`comment-input-${postId}`);
      const text = input.value.trim();
      if (!text) return;

      const authorName = currentUser ? currentUser.name : 'Guest Member';

      try {
        const res = await fetch(`/api/community/posts/${postId}/comment`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ author: authorName, text })
        });
        const data = await res.json();
        if (data.success && data.comments) {
          input.value = '';
          const list = document.getElementById(`comment-list-${postId}`);
          list.innerHTML = data.comments.map(c => `
            <div class="ccomment">
              <strong>${escapeHtml(c.author)}</strong>
              <span>${escapeHtml(c.text)}</span>
            </div>
          `).join('');
          document.getElementById(`comment-count-${postId}`).textContent = data.comments.length;
          showToast('✅ Comment posted!');
        }
      } catch (e) {
        console.error('Comment error:', e);
      }
    }

    function sharePost(postId) {
      const url = window.location.origin + '/community#' + postId;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(url);
        showToast('🔗 Link copied to clipboard!');
      } else {
        prompt('Copy post link:', url);
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      checkUserAuth();
      loadCommunityPosts();
    });
  </script>

<?php
include __DIR__ . '/includes/footer.php';
?>
