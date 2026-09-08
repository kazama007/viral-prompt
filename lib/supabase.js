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

let sharp = null;
try {
  sharp = require('sharp');
} catch (e) {}

async function uploadToSupabase(fileBuffer, originalName, mimeType, bucket = DEFAULT_BUCKET) {
  if (!supabase) {
    return null;
  }
  try {
    let finalBuffer = fileBuffer;
    let ext = path.extname(originalName) || '.png';
    let finalMime = mimeType || 'image/png';
    const isImage = (mimeType && mimeType.startsWith('image/')) || /\.(png|jpe?g|webp)$/i.test(originalName);

    if (sharp && isImage) {
      try {
        const isThumb = originalName.toLowerCase().includes('thumb');
        const maxW = isThumb ? 800 : 1280;
        finalBuffer = await sharp(fileBuffer)
          .resize({ width: maxW, withoutEnlargement: true })
          .webp({ quality: 82, effort: 4 })
          .toBuffer();
        ext = '.webp';
        finalMime = 'image/webp';
      } catch (sharpErr) {
        console.warn('[Supabase Storage] Sharp compression fallback:', sharpErr.message);
        finalBuffer = fileBuffer;
      }
    }

    const uniqueName = `upload-${Date.now()}-${Math.round(Math.random() * 1e9)}${ext}`;

    const { data, error } = await supabase.storage
      .from(bucket)
      .upload(uniqueName, finalBuffer, {
        contentType: finalMime,
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
