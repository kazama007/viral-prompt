const path = require('path');
require('dotenv').config();

const GITHUB_REPO = process.env.GITHUB_REPO || 'kazama007/prompt-images';
const GITHUB_BRANCH = process.env.GITHUB_BRANCH || 'main';
const GITHUB_TOKEN = process.env.GITHUB_TOKEN || '';

/**
 * Upload a file buffer to GitHub repository using GitHub Contents API.
 * Returns raw.githubusercontent.com public download URL.
 */
async function uploadToGitHub(fileBuffer, originalName = 'image.png', mimeType = 'image/png') {
  if (!GITHUB_TOKEN) {
    console.error('[GitHub Storage] GITHUB_TOKEN is not configured');
    return null;
  }

  try {
    const ext = path.extname(originalName) || '.png';
    const cleanBase = path.basename(originalName, ext).replace(/[^a-zA-Z0-9_-]/g, '_').slice(0, 30);
    const uniqueName = `upload_${Date.now()}_${Math.round(Math.random() * 1e6)}_${cleanBase}${ext}`;
    const base64Content = fileBuffer.toString('base64');

    const res = await fetch(`https://api.github.com/repos/${GITHUB_REPO}/contents/${uniqueName}`, {
      method: 'PUT',
      headers: {
        'Authorization': `token ${GITHUB_TOKEN}`,
        'Content-Type': 'application/json',
        'User-Agent': 'ViralPrompt-App'
      },
      body: JSON.stringify({
        message: `Upload ${uniqueName}`,
        content: base64Content,
        branch: GITHUB_BRANCH
      })
    });

    const data = await res.json();
    if (res.ok && data.content && data.content.download_url) {
      console.log(`[GitHub Storage Upload Success]: ${data.content.download_url}`);
      return data.content.download_url;
    }

    // Fallback if download_url is structured directly
    if (res.ok && data.content && data.content.path) {
      const directRawUrl = `https://raw.githubusercontent.com/${GITHUB_REPO}/${GITHUB_BRANCH}/${data.content.path}`;
      return directRawUrl;
    }

    console.error('[GitHub Storage Upload Error]:', data.message || data);
    return null;
  } catch (err) {
    console.error('[GitHub Storage Exception]:', err.message);
    return null;
  }
}

module.exports = {
  uploadToGitHub,
  GITHUB_REPO,
  GITHUB_BRANCH
};
