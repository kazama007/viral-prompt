const { createClient } = require('@supabase/supabase-js');
require('dotenv').config();
const path = require('path');

const SUPABASE_URL = process.env.SUPABASE_URL || '';
const SUPABASE_KEY = process.env.SUPABASE_KEY || '';
const DEFAULT_BUCKET = process.env.SUPABASE_BUCKET || 'prompts';

let supabase = null;
if (SUPABASE_URL && SUPABASE_KEY) {
  supabase = createClient(SUPABASE_URL, SUPABASE_KEY);
}

async function uploadToSupabase(fileBuffer, originalName, mimeType, bucket = DEFAULT_BUCKET) {
  if (!supabase) {
    return null;
  }
  try {
    const ext = path.extname(originalName) || '.png';
    const uniqueName = `upload-${Date.now()}-${Math.round(Math.random() * 1e9)}${ext}`;

    const { data, error } = await supabase.storage
      .from(bucket)
      .upload(uniqueName, fileBuffer, {
        contentType: mimeType || 'image/png',
        upsert: true
      });

    if (error) {
      console.error('[Supabase Storage Upload Error]:', error.message);
      return null;
    }

    const { data: { publicUrl } } = supabase.storage.from(bucket).getPublicUrl(uniqueName);
    return publicUrl;
  } catch (err) {
    console.error('[Supabase Storage Exception]:', err.message);
    return null;
  }
}

module.exports = {
  supabase,
  uploadToSupabase,
  SUPABASE_URL,
  DEFAULT_BUCKET
};
