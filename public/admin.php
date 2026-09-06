<?php require_once __DIR__ . "/includes/config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VIRAL PROMPT — Admin Backend (Prompt Publisher)</title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="css/soni-style.css">
  <style>
    @media (min-width: 1024px) {
      .admin-container { max-width: 1320px !important; }
      .admin-grid-stats { grid-template-columns: repeat(6, 1fr) !important; gap: 14px !important; }
    }
    @media (max-width: 1023px) and (min-width: 641px) {
      .admin-grid-stats { grid-template-columns: repeat(3, 1fr) !important; }
    }
    @media (max-width: 640px) {
      .admin-grid-stats { grid-template-columns: repeat(2, 1fr) !important; }
    }
    .admin-table th, .admin-table td {
      padding: 12px 10px !important;
    }
  </style>
</head>
<body style="background:#f4f3f9;">

  <header class="site-header">
    <div class="container header-inner admin-container">
      <a href="index.php" class="brand">
        <img src="assets/images/logo.png" alt="VIRAL PROMPT" class="brand-icon-img" width="36" height="36">
        <span>PROMPT <b>MASTER</b> <span style="font-size:13px;background:#111827;color:#fff;padding:2px 8px;border-radius:4px;font-weight:600;">ADMIN BACKEND</span></span>
      </a>
      <div style="display:flex;align-items:center;gap:12px;">
        <a href="index.php" class="btn btn-outline btn-sm">👁 View Live Website</a>
        <button id="logoutBtn" class="btn btn-sm btn-danger" style="display:none;" onclick="adminLogout()">Logout</button>
      </div>
    </div>
  </header>

  <main class="container admin-container" style="padding-top: 30px; padding-bottom: 60px;">

    <!-- 1. Login Gate -->
    <div id="loginGate" style="max-width: 420px; margin: 40px auto;">
      <div class="admin-card" style="text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 12px;">🔐</div>
        <h2 style="margin-bottom: 8px;">Admin Login</h2>
        <p style="color:var(--muted);font-size:14px;margin-bottom:24px;">Login to publish daily category-wise prompts</p>

        <form onsubmit="handleAdminLogin(event)">
          <div class="form-group" style="text-align:left;">
            <label>USERNAME</label>
            <input type="text" id="adminUser" class="form-control" placeholder="admin" value="admin" required>
          </div>
          <div class="form-group" style="text-align:left;">
            <label>PASSWORD</label>
            <input type="password" id="adminPass" class="form-control" placeholder="admin123" value="admin123" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Log In to Backend</button>
        </form>
        <p style="font-size:12px;color:var(--muted);margin-top:16px;">Default credentials: <b>admin</b> / <b>admin123</b></p>
      </div>
    </div>

    <!-- 2. Admin Main Dashboard -->
    <div id="adminDashboard" style="display: none;">

      <!-- Quick Stats -->
      <div class="admin-grid-stats" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));">
        <div class="stat-box">
          <h4>Total Prompts</h4>
          <div class="stat-val" id="statTotalPrompts">0</div>
        </div>
        <div class="stat-box">
          <h4>Free Prompts</h4>
          <div class="stat-val" style="color:var(--green);" id="statFreePrompts">0</div>
        </div>
        <div class="stat-box">
          <h4>Premium Prompts</h4>
          <div class="stat-val" style="color:var(--amber);" id="statPremiumPrompts">0</div>
        </div>
        <div class="stat-box">
          <h4>Active Categories</h4>
          <div class="stat-val" id="statTotalCategories">0</div>
        </div>
        <div class="stat-box">
          <h4>Total Users</h4>
          <div class="stat-val" style="color:#2563eb;" id="statTotalUsers">0</div>
        </div>
        <div class="stat-box">
          <h4>Active VIP (1-Mo)</h4>
          <div class="stat-val" style="color:#059669;" id="statActiveVipUsers">0</div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="admin-tabs">
        <button type="button" class="admin-tab active" data-tab="publish" onclick="switchAdminTab('publish')">
          <span>➕</span> Publish New Prompt
        </button>
        <button type="button" class="admin-tab" data-tab="manage" onclick="switchAdminTab('manage')">
          <span>📋</span> Manage Prompts <span class="tab-badge" id="tabCount">0</span>
        </button>
        <button type="button" class="admin-tab" data-tab="categories" onclick="switchAdminTab('categories')">
          <span>🗂</span> Manage Categories
        </button>
        <button type="button" class="admin-tab" data-tab="users" onclick="switchAdminTab('users')">
          <span>👥</span> Users & VIP Subscriptions <span class="tab-badge" id="tabUserCount">0</span>
        </button>
      </div>

      <!-- TAB 1: Publish New Prompt -->
      <div id="tabPanel_publish">
        <div class="admin-card">
          <h3 style="margin-bottom: 20px;">Daily Prompt Publisher</h3>

          <form onsubmit="handlePublishPrompt(event)" id="publishForm">
            <div class="form-group">
              <label>PROMPT TITLE *</label>
              <input type="text" id="pTitle" class="form-control" placeholder="e.g. 30-SEC CYBERPUNK TOKYO NEON DRIFT" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
              <div class="form-group">
                <label>CATEGORY *</label>
                <div style="display:flex;gap:8px;">
                  <select id="pCategory" class="form-control" required style="flex:1;">
                    <!-- Dynamically loaded categories -->
                  </select>
                  <button type="button" class="btn btn-outline btn-sm" onclick="quickAddCategoryPrompt()">+ New</button>
                </div>
              </div>

              <div class="form-group">
                <label>ACCESS TYPE *</label>
                <select id="pType" class="form-control" required>
                  <option value="premium">⭐ Premium (Subscribers Only)</option>
                  <option value="free">🎁 Free (Available for Everyone)</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>THUMBNAIL IMAGE</label>
              <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                <input type="file" id="pThumbFile" accept="image/*" class="form-control" style="flex:1;min-width:240px;" onchange="handleThumbnailUpload(this)">
                <span style="font-size:13px;color:var(--muted);">OR Image URL:</span>
                <input type="text" id="pThumbUrl" class="form-control" placeholder="https://..." style="flex:1;min-width:240px;">
              </div>
              <div id="thumbPreviewBox" style="display:none;margin-top:10px;">
                <img id="thumbPreviewImg" src="" style="width:140px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
              </div>
            </div>

            <div class="form-group">
              <label>SHORT SUMMARY / HOOK</label>
              <input type="text" id="pSummary" class="form-control" placeholder="1-sentence viral hook description for subscribers">
            </div>

            <div class="form-group">
              <label>MASTER SYSTEM PROMPT (READY TO COPY) *</label>
              <textarea id="pMasterPrompt" rows="8" class="form-control" style="font-family:monospace;font-size:13.5px;"
                        placeholder="SYSTEM PROMPT: ...&#10;SCENE BREAKDOWN: ...&#10;CAMERA DIRECTION: ...&#10;LIGHTING: ..." required></textarea>
            </div>

            <div class="form-group">
              <label>NEGATIVE PROMPT RULES</label>
              <textarea id="pNegativePrompt" rows="2" class="form-control" style="font-family:monospace;font-size:13px;"
                        placeholder="cartoon, low quality, jitter, blurred hands, distorted face"></textarea>
            </div>

            <div class="form-group">
              <label>COMPATIBLE AI TOOLS</label>
              <input type="text" id="pTools" class="form-control" placeholder="Kling AI, Seedance, Runway Gen-3, Luma Dream, Veo 2" value="Kling AI, Seedance, Runway Gen-3, Veo 2">
            </div>

            <button type="submit" class="btn btn-primary btn-lg" id="publishSubmitBtn" style="margin-top:10px;">
              🚀 Publish Prompt Directly to Website
            </button>
          </form>
        </div>
      </div>

      <!-- TAB 2: Manage Prompts -->
      <div id="tabPanel_manage" style="display:none;">
        <div class="admin-card">
          <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
            <h3>All Published Prompts</h3>
            <div style="display:flex;gap:10px;align-items:center;">
              <select id="manageCategoryFilter" class="form-control" style="width:200px;" onchange="renderPromptsTable()">
                <option value="all">All Categories</option>
              </select>
              <input type="text" id="manageSearchInput" class="form-control" placeholder="Search prompts..." style="width:220px;" oninput="renderPromptsTable()">
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table class="admin-table">
              <thead>
                <tr>
                  <th style="width:80px;">Image</th>
                  <th>Title</th>
                  <th>Category</th>
                  <th>Type</th>
                  <th>Date</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody id="promptsTableBody">
                <!-- Loaded dynamically -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB 3: Manage Categories -->
      <div id="tabPanel_categories" style="display:none;">
        <div class="admin-card">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3>Category Manager</h3>
            <div style="display:flex;gap:10px;">
              <input type="text" id="newCategoryInput" class="form-control" placeholder="Category Name (e.g. 3D Architecture)">
              <button class="btn btn-primary btn-sm" onclick="addCategorySubmit()">Add Category</button>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Category Name</th>
                  <th>Prompts Count</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody id="categoriesTableBody">
                <!-- Loaded dynamically -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB 4: Users & VIP Subscriptions -->
      <div id="tabPanel_users" style="display:none;">
        <div class="admin-card">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:20px;">
            <div>
              <h3 style="margin-bottom:4px;font-size:20px;">👥 User Accounts & 1-Month VIP Subscriptions</h3>
              <p style="color:var(--muted);font-size:13.5px;margin:0;">
                Search registered creators by name or email, grant 1-month (30-day) VIP access, and track automatic expiry in real-time.
              </p>
            </div>
            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
              <input type="text" id="adminUserSearchInput" class="form-control" placeholder="🔍 Search by name or email..." style="width:250px;" oninput="filterAdminUsers()">
              <select id="adminUserStatusFilter" class="form-control" style="width:165px;" onchange="filterAdminUsers()">
                <option value="all">All Statuses</option>
                <option value="active">🟢 Active VIP Only</option>
                <option value="expired">🔴 Expired Only</option>
                <option value="inactive">⚪ Inactive Free Only</option>
              </select>
              <button class="btn btn-outline btn-sm" onclick="loadAdminUsers()">🔄 Refresh</button>
            </div>
          </div>

          <!-- Automated 1-Month Rule Banner -->
          <div style="background:linear-gradient(135deg, #eef2ff, #f5f3ff);border:1px solid #c7d2fe;border-radius:12px;padding:14px 18px;margin-bottom:22px;display:flex;align-items:center;gap:14px;">
            <div style="width:38px;height:38px;border-radius:10px;background:#6366f1;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">⚡</div>
            <div style="font-size:13.5px;color:#312e81;line-height:1.5;">
              <strong>Automatic 1-Month Rule:</strong> Clicking <b>"⚡ Grant 1-Month VIP"</b> grants full premium copy access for exactly <b>30 days</b>. The backend continuously calculates time elapsed ("Started X days ago") and time remaining ("X days left"). Once 30 days pass, access <b>automatically expires</b> with zero manual action required.
            </div>
          </div>

          <!-- Users Table -->
          <div style="overflow-x:auto;">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>User & Email</th>
                  <th>Registered</th>
                  <th>VIP Status</th>
                  <th>Time Elapsed</th>
                  <th>Expiry Date</th>
                  <th>Days Remaining</th>
                  <th style="text-align:right;">Admin Actions</th>
                </tr>
              </thead>
              <tbody id="usersTableBody">
                <!-- Loaded dynamically -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

  </main>

  <!-- Edit Prompt Modal -->
  <div class="modal-overlay" id="editModalOverlay">
    <div class="prompt-modal-box" style="max-width:700px;">
      <button class="close-modal" onclick="closeEditModal()">✕</button>
      <h3 style="margin-bottom:18px;">Edit Prompt</h3>
      <form onsubmit="handleUpdatePrompt(event)">
        <input type="hidden" id="editPromptId">
        <div class="form-group">
          <label>TITLE</label>
          <input type="text" id="editTitle" class="form-control" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label>CATEGORY</label>
            <select id="editCategory" class="form-control" required></select>
          </div>
          <div class="form-group">
            <label>TYPE</label>
            <select id="editType" class="form-control" required>
              <option value="free">🎁 Free</option>
              <option value="premium">⭐ Premium</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>THUMBNAIL URL</label>
          <input type="text" id="editThumbnail" class="form-control">
        </div>
        <div class="form-group">
          <label>MASTER PROMPT</label>
          <textarea id="editMasterPrompt" rows="6" class="form-control" style="font-family:monospace;font-size:13px;" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
      </form>
    </div>
  </div>

  <footer class="site-footer">
    <div class="container">
      <p class="footer-copy">© 2026 VIRAL PROMPT Backend · Admin Management System</p>
    </div>
  </footer>

  <script>
    let allPrompts = [];
    let allCategories = [];
    let allUsers = [];

    // Authentication
    function checkAuth() {
      const urlParams = new URLSearchParams(window.location.search);
      const urlToken = urlParams.get('token');
      if (urlToken) {
        localStorage.setItem('promptmaster_admin_token', urlToken);
      }
      const token = localStorage.getItem('promptmaster_admin_token');
      if (token) {
        document.getElementById('loginGate').style.display = 'none';
        document.getElementById('adminDashboard').style.display = 'block';
        document.getElementById('logoutBtn').style.display = 'inline-block';
        loadAdminData();
        const tab = urlParams.get('tab');
        if (tab) switchAdminTab(tab);
      } else {
        document.getElementById('loginGate').style.display = 'block';
        document.getElementById('adminDashboard').style.display = 'none';
        document.getElementById('logoutBtn').style.display = 'none';
      }
    }

    async function handleAdminLogin(e) {
      e.preventDefault();
      const username = document.getElementById('adminUser').value;
      const password = document.getElementById('adminPass').value;

      try {
        const res = await fetch('/api/admin/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username, password })
        });
        const data = await res.json();
        if (data.success) {
          localStorage.setItem('promptmaster_admin_token', data.token);
          checkAuth();
        } else {
          alert('Login failed: ' + data.message);
        }
      } catch(err) {
        alert('Could not connect to server: ' + err.message);
      }
    }

    function adminLogout() {
      localStorage.removeItem('promptmaster_admin_token');
      checkAuth();
    }

    // Load Data
    async function loadAdminData() {
      try {
        const [promptsRes, catsRes] = await Promise.all([
          fetch('/api/prompts'),
          fetch('/api/categories')
        ]);
        const promptsData = await promptsRes.json();
        const catsData = await catsRes.json();

        allPrompts = promptsData.prompts || [];
        allCategories = catsData.categories || [];

        updateStats();
        populateCategoryDropdowns();
        renderPromptsTable();
        renderCategoriesTable();
        loadAdminUsers();
      } catch (err) {
        console.error('Error loading data:', err);
      }
    }

    function updateStats() {
      document.getElementById('statTotalPrompts').textContent = allPrompts.length;
      document.getElementById('statFreePrompts').textContent = allPrompts.filter(p => p.type === 'free').length;
      document.getElementById('statPremiumPrompts').textContent = allPrompts.filter(p => p.type === 'premium').length;
      document.getElementById('statTotalCategories').textContent = allCategories.length;
      document.getElementById('tabCount').textContent = allPrompts.length;
    }

    function populateCategoryDropdowns() {
      const pCat = document.getElementById('pCategory');
      const filterCat = document.getElementById('manageCategoryFilter');
      const editCat = document.getElementById('editCategory');

      const options = allCategories.map(c => `<option value="${c.name}">${c.name}</option>`).join('');

      pCat.innerHTML = options;
      editCat.innerHTML = options;
      filterCat.innerHTML = `<option value="all">All Categories</option>` + options;
    }

    // Tab Switching
    function switchAdminTab(tabName) {
      document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
      const activeBtn = document.querySelector(`.admin-tab[data-tab="${tabName}"]`);
      if (activeBtn) activeBtn.classList.add('active');

      document.getElementById('tabPanel_publish').style.display = tabName === 'publish' ? 'block' : 'none';
      document.getElementById('tabPanel_manage').style.display = tabName === 'manage' ? 'block' : 'none';
      document.getElementById('tabPanel_categories').style.display = tabName === 'categories' ? 'block' : 'none';
      document.getElementById('tabPanel_users').style.display = tabName === 'users' ? 'block' : 'none';

      if (tabName === 'users') {
        loadAdminUsers();
      }
    }

    // Upload Thumbnail
    async function handleThumbnailUpload(input) {
      if (!input.files || !input.files[0]) return;
      const formData = new FormData();
      formData.append('thumbnail', input.files[0]);

      try {
        const res = await fetch('/api/admin/upload', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById('pThumbUrl').value = data.url;
          document.getElementById('thumbPreviewImg').src = data.url;
          document.getElementById('thumbPreviewBox').style.display = 'block';
        }
      } catch(err) {
        alert('Upload failed: ' + err.message);
      }
    }

    // Publish Prompt
    async function handlePublishPrompt(e) {
      e.preventDefault();
      const btn = document.getElementById('publishSubmitBtn');
      btn.disabled = true;
      btn.textContent = 'Publishing...';

      const promptData = {
        title: document.getElementById('pTitle').value,
        category: document.getElementById('pCategory').value,
        type: document.getElementById('pType').value,
        thumbnail: document.getElementById('pThumbUrl').value || 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&auto=format&fit=crop&q=80',
        summary: document.getElementById('pSummary').value,
        masterPrompt: document.getElementById('pMasterPrompt').value,
        negativePrompt: document.getElementById('pNegativePrompt').value,
        tools: document.getElementById('pTools').value.split(',').map(s => s.trim())
      };

      try {
        const res = await fetch('/api/admin/prompts', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(promptData)
        });
        const data = await res.json();
        if (data.success) {
          alert('✅ Prompt published successfully! It is now live on the website.');
          document.getElementById('publishForm').reset();
          document.getElementById('thumbPreviewBox').style.display = 'none';
          loadAdminData();
          switchAdminTab('manage');
        } else {
          alert('Error: ' + data.message);
        }
      } catch(err) {
        alert('Could not save prompt: ' + err.message);
      } finally {
        btn.disabled = false;
        btn.textContent = '🚀 Publish Prompt Directly to Website';
      }
    }

    // Render Manage Table
    function renderPromptsTable() {
      const tbody = document.getElementById('promptsTableBody');
      const catFilter = document.getElementById('manageCategoryFilter').value;
      const search = document.getElementById('manageSearchInput').value.toLowerCase().trim();

      let filtered = allPrompts;
      if (catFilter !== 'all') {
        filtered = filtered.filter(p => p.category.toLowerCase() === catFilter.toLowerCase());
      }
      if (search) {
        filtered = filtered.filter(p => p.title.toLowerCase().includes(search));
      }

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--muted);">No prompts found.</td></tr>`;
        return;
      }

      tbody.innerHTML = filtered.map(p => `
        <tr>
          <td>
            <img src="${p.thumbnail}" style="width:60px;height:38px;object-fit:cover;border-radius:6px;">
          </td>
          <td><strong>${p.title}</strong></td>
          <td><span class="prow-cat">${p.category}</span></td>
          <td>
            <span class="prow-price ${p.type === 'free' ? 'is-free' : ''}">
              ${p.type === 'free' ? '🎁 Free' : '⭐ Premium'}
            </span>
          </td>
          <td style="color:var(--muted);">${p.updatedDate || 'Recent'}</td>
          <td style="text-align:right;white-space:nowrap;">
            <button class="btn-action-extend" onclick="openEditModal('${p.id}')">✏️ Edit</button>
            <button class="btn-action-delete" onclick="deletePrompt('${p.id}', '${p.title.replace(/'/g, "\\'")}')">🗑️ Delete</button>
          </td>
        </tr>
      `).join('');
    }

    // Edit Prompt
    function openEditModal(promptId) {
      const p = allPrompts.find(item => item.id === promptId || String(item.numericId) === promptId);
      if (!p) return;

      document.getElementById('editPromptId').value = p.id;
      document.getElementById('editTitle').value = p.title;
      document.getElementById('editCategory').value = p.category;
      document.getElementById('editType').value = p.type;
      document.getElementById('editThumbnail').value = p.thumbnail;
      document.getElementById('editMasterPrompt').value = p.masterPrompt;

      document.getElementById('editModalOverlay').classList.add('active');
    }

    function closeEditModal() {
      document.getElementById('editModalOverlay').classList.remove('active');
    }

    async function handleUpdatePrompt(e) {
      e.preventDefault();
      const id = document.getElementById('editPromptId').value;
      const payload = {
        title: document.getElementById('editTitle').value,
        category: document.getElementById('editCategory').value,
        type: document.getElementById('editType').value,
        thumbnail: document.getElementById('editThumbnail').value,
        masterPrompt: document.getElementById('editMasterPrompt').value
      };

      try {
        const res = await fetch(`/api/admin/prompts/${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          alert('Prompt updated successfully!');
          closeEditModal();
          loadAdminData();
        }
      } catch(err) {
        alert('Update failed: ' + err.message);
      }
    }

    // Delete Prompt
    async function deletePrompt(id, title) {
      if (!confirm(`Are you sure you want to delete "${title}"?`)) return;

      try {
        const res = await fetch(`/api/admin/prompts/${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) {
          loadAdminData();
        }
      } catch(err) {
        alert('Delete failed: ' + err.message);
      }
    }

    // Categories Table & Add
    function renderCategoriesTable() {
      const tbody = document.getElementById('categoriesTableBody');
      tbody.innerHTML = allCategories.map(c => `
        <tr>
          <td><strong>${c.name}</strong></td>
          <td>${c.count} prompts</td>
          <td style="text-align:right;">
            <button class="btn-action-delete" onclick="deleteCategory('${c.name}')">🗑️ Delete</button>
          </td>
        </tr>
      `).join('');
    }

    async function addCategorySubmit() {
      const input = document.getElementById('newCategoryInput');
      const name = input.value.trim();
      if (!name) return;

      try {
        const res = await fetch('/api/admin/categories', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name })
        });
        const data = await res.json();
        if (data.success) {
          input.value = '';
          loadAdminData();
        } else {
          alert(data.message);
        }
      } catch(err) {
        alert('Could not add category: ' + err.message);
      }
    }

    async function quickAddCategoryPrompt() {
      const name = prompt("Enter new category name:");
      if (!name || !name.trim()) return;

      try {
        const res = await fetch('/api/admin/categories', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name: name.trim() })
        });
        const data = await res.json();
        if (data.success) {
          await loadAdminData();
          document.getElementById('pCategory').value = name.trim();
        }
      } catch(err) {
        alert(err.message);
      }
    }

    // ─── User & Subscription Management ───
    async function loadAdminUsers() {
      try {
        const res = await fetch('/api/admin/users');
        const data = await res.json();
        if (data.success) {
          allUsers = data.users || [];
          const stats = data.stats || {};

          document.getElementById('statTotalUsers').textContent = stats.totalUsers ?? allUsers.length;
          document.getElementById('statActiveVipUsers').textContent = stats.activeVipUsers ?? 0;
          document.getElementById('tabUserCount').textContent = allUsers.length;

          filterAdminUsers();
        }
      } catch (err) {
        console.error('Error loading admin users:', err);
      }
    }

    function filterAdminUsers() {
      const search = (document.getElementById('adminUserSearchInput')?.value || '').toLowerCase().trim();
      const statusFilter = document.getElementById('adminUserStatusFilter')?.value || 'all';

      let filtered = allUsers;

      if (search) {
        filtered = filtered.filter(u =>
          (u.name && u.name.toLowerCase().includes(search)) ||
          (u.email && u.email.toLowerCase().includes(search))
        );
      }

      if (statusFilter !== 'all') {
        filtered = filtered.filter(u => u.subscription?.status === statusFilter);
      }

      renderUsersTable(filtered);
    }

    function renderUsersTable(usersList) {
      const tbody = document.getElementById('usersTableBody');
      if (!tbody) return;

      if (!usersList || usersList.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--muted);">No users found matching your search.</td></tr>`;
        return;
      }

      tbody.innerHTML = usersList.map(u => {
        const sub = u.subscription || {};
        const isActive = sub.status === 'active';
        const isExpired = sub.status === 'expired';

        // VIP Status Badge
        let statusBadge = '';
        if (isActive) {
          statusBadge = `<span style="background:#e6f9ed;color:#128c46;border:1px solid #b7ecc8;padding:4px 10px;border-radius:999px;font-weight:700;font-size:12px;display:inline-flex;align-items:center;gap:4px;">🟢 Active VIP</span>`;
        } else if (isExpired) {
          statusBadge = `<span style="background:#ffebee;color:#c62828;border:1px solid #ffcdd2;padding:4px 10px;border-radius:999px;font-weight:700;font-size:12px;display:inline-flex;align-items:center;gap:4px;">🔴 Expired</span>`;
        } else {
          statusBadge = `<span style="background:#f4f4f6;color:#6b7280;border:1px solid #e5e7eb;padding:4px 10px;border-radius:999px;font-weight:600;font-size:12px;display:inline-flex;align-items:center;gap:4px;">⚪ Inactive Free</span>`;
        }

        // Time Elapsed
        let elapsedHtml = '—';
        if (isActive) {
          elapsedHtml = `<span style="font-weight:600;color:#374151;">⏱️ ${sub.daysElapsed} day(s) passed</span>`;
        } else if (isExpired) {
          elapsedHtml = `<span style="color:#c62828;font-size:13px;">⏱️ ${sub.daysElapsed} day(s) passed</span>`;
        }

        // Expiry Date
        let expiryHtml = '—';
        if (sub.expiresAtFormatted && sub.expiresAtFormatted !== 'N/A') {
          expiryHtml = `<span style="font-weight:600;color:${isActive ? '#111827' : '#9ca3af'};">📅 ${sub.expiresAtFormatted}</span>`;
        }

        // Days Remaining
        let remainingHtml = '<span style="color:var(--muted);">None</span>';
        if (isActive) {
          remainingHtml = `<span style="font-weight:700;color:#059669;background:#ecfdf5;padding:3px 8px;border-radius:6px;">${sub.daysLeft} days left</span>`;
        } else if (isExpired) {
          remainingHtml = `<span style="color:#dc2626;font-weight:700;background:#fef2f2;padding:3px 8px;border-radius:6px;">Auto-Expired</span>`;
        }

        return `
          <tr>
            <td>
              <div style="font-weight:700;font-size:14.5px;color:var(--text);">${u.name}</div>
              <div style="font-size:12.5px;color:var(--muted);">${u.email}</div>
            </td>
            <td style="color:var(--muted);font-size:13px;white-space:nowrap;">
              ${u.createdAtFormatted || 'Recent'}
            </td>
            <td style="white-space:nowrap;">
              ${statusBadge}
            </td>
            <td style="white-space:nowrap;font-size:13px;">
              ${elapsedHtml}
            </td>
            <td style="white-space:nowrap;font-size:13px;">
              ${expiryHtml}
            </td>
            <td style="white-space:nowrap;font-size:13px;">
              ${remainingHtml}
            </td>
            <td style="text-align:right;white-space:nowrap;">
              <button class="btn-action-grant" onclick="grantUserVip('${u.id}', 'grant_1_month', '${u.name.replace(/'/g, "\\'")}')" title="Grant or restart 30-day VIP">⚡ Grant 1-Mo VIP</button>
              <button class="btn-action-extend" onclick="grantUserVip('${u.id}', 'extend_30', '${u.name.replace(/'/g, "\\'")}')" title="Extend subscription by 30 days">+30d</button>
              ${isActive ? `<button class="btn-action-revoke" onclick="grantUserVip('${u.id}', 'revoke', '${u.name.replace(/'/g, "\\'")}')" title="Revoke VIP Access">Revoke</button>` : ''}
              <button class="btn-action-delete" onclick="deleteAdminUser('${u.id}', '${u.name.replace(/'/g, "\\'")}')" title="Delete User">✕</button>
            </td>
          </tr>
        `;
      }).join('');
    }

    async function grantUserVip(userId, action, userName) {
      if (action === 'revoke' && !confirm(`Are you sure you want to revoke VIP subscription for "${userName}"?`)) {
        return;
      }

      try {
        const res = await fetch(`/api/admin/users/${userId}/subscription`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action, durationDays: 30 })
        });
        const data = await res.json();
        if (data.success) {
          alert(`✅ ${data.message} for ${userName}`);
          loadAdminUsers();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Action failed: ' + err.message);
      }
    }

    async function deleteAdminUser(userId, userName) {
      if (!confirm(`Are you sure you want to delete user account "${userName}"?`)) return;

      try {
        const res = await fetch(`/api/admin/users/${userId}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) {
          loadAdminUsers();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (err) {
        alert('Delete failed: ' + err.message);
      }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', checkAuth);
  </script>
</body>
</html>
