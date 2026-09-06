const { createClient } = require('@supabase/supabase-js');
require('dotenv').config();

const SUPABASE_URL = process.env.SUPABASE_URL || '';
const SUPABASE_KEY = process.env.SUPABASE_KEY || '';

let supabase = null;
if (SUPABASE_URL && SUPABASE_KEY) {
  supabase = createClient(SUPABASE_URL, SUPABASE_KEY);
}

function formatDateDayMonthYear(d) {
  if (!d) return 'N/A';
  const date = new Date(d);
  if (isNaN(date.getTime())) return 'N/A';
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

function evaluateSubscription(sub) {
  if (!sub || sub.status !== 'active') {
    const isExp = Boolean(sub && sub.status === 'expired');
    return {
      status: isExp ? 'expired' : 'inactive',
      plan: sub?.plan || 'free',
      grantedAt: sub?.grantedAt || null,
      expiresAt: sub?.expiresAt || null,
      durationDays: sub?.durationDays || 0,
      isActive: false,
      isExpired: isExp,
      daysLeft: 0,
      daysElapsed: (sub && sub.grantedAt)
        ? Math.max(0, Math.floor((Date.now() - new Date(sub.grantedAt).getTime()) / (1000 * 60 * 60 * 24)))
        : 0,
      expiresAtFormatted: (sub && sub.expiresAt) ? formatDateDayMonthYear(sub.expiresAt) : 'N/A',
      grantedAtFormatted: (sub && sub.grantedAt) ? formatDateDayMonthYear(sub.grantedAt) : 'N/A'
    };
  }

  const now = Date.now();
  const expiresTime = new Date(sub.expiresAt).getTime();
  const grantedTime = new Date(sub.grantedAt || now).getTime();

  if (now > expiresTime) {
    return {
      status: 'expired',
      plan: sub.plan || 'vip',
      grantedAt: sub.grantedAt,
      expiresAt: sub.expiresAt,
      durationDays: sub.durationDays || 0,
      isActive: false,
      isExpired: true,
      daysLeft: 0,
      daysElapsed: Math.max(0, Math.floor((now - grantedTime) / (1000 * 60 * 60 * 24))),
      expiresAtFormatted: formatDateDayMonthYear(expiresTime),
      grantedAtFormatted: formatDateDayMonthYear(grantedTime)
    };
  }

  const msLeft = expiresTime - now;
  const daysLeft = Math.ceil(msLeft / (1000 * 60 * 60 * 24));
  const daysElapsed = Math.max(0, Math.floor((now - grantedTime) / (1000 * 60 * 60 * 24)));

  return {
    status: 'active',
    plan: sub.plan || 'vip',
    grantedAt: sub.grantedAt,
    expiresAt: sub.expiresAt,
    durationDays: sub.durationDays || 0,
    isActive: true,
    isExpired: false,
    daysLeft,
    daysElapsed,
    expiresAtFormatted: formatDateDayMonthYear(expiresTime),
    grantedAtFormatted: formatDateDayMonthYear(grantedTime)
  };
}

function formatUser(u) {
  if (!u) return null;
  const meta = u.user_metadata || {};
  const rawSub = meta.subscription || { status: 'inactive', plan: 'free' };
  const evaluatedSub = evaluateSubscription(rawSub);

  return {
    id: u.id,
    name: meta.name || u.email?.split('@')[0] || 'Member',
    email: u.email,
    createdAt: u.created_at,
    createdAtFormatted: formatDateDayMonthYear(u.created_at),
    subscription: evaluatedSub
  };
}

/**
 * Register a new user in Supabase Cloud Auth
 */
async function registerCloudUser(name, email, password) {
  if (!supabase) throw new Error('Supabase Cloud is not configured');

  const cleanEmail = email.trim().toLowerCase();
  const defaultSub = {
    status: 'inactive',
    plan: 'free',
    grantedAt: null,
    expiresAt: null,
    durationDays: 0
  };

  // 1. Create confirmed user directly via Admin API
  const { data, error } = await supabase.auth.admin.createUser({
    email: cleanEmail,
    password: password.trim(),
    email_confirm: true,
    user_metadata: {
      name: name.trim(),
      subscription: defaultSub
    }
  });

  if (error) {
    throw error;
  }

  // 2. Sign in to get active session token
  const signInRes = await supabase.auth.signInWithPassword({
    email: cleanEmail,
    password: password.trim()
  });

  const token = signInRes.data?.session?.access_token || Buffer.from(`${data.user.id}:::${Date.now()}`).toString('base64');

  return {
    user: formatUser(data.user),
    token
  };
}

/**
 * Login user via Supabase Cloud Auth
 */
async function loginCloudUser(email, password) {
  if (!supabase) throw new Error('Supabase Cloud is not configured');

  const cleanEmail = email.trim().toLowerCase();
  const { data, error } = await supabase.auth.signInWithPassword({
    email: cleanEmail,
    password: password.trim()
  });

  if (error || !data.user) {
    throw error || new Error('Invalid email or password');
  }

  const formatted = formatUser(data.user);
  return {
    user: formatted,
    token: data.session.access_token
  };
}

/**
 * Get profile and evaluate subscription from token or user ID
 */
async function getCloudUserProfile(token) {
  if (!supabase || !token) return null;

  try {
    // Attempt 1: Standard Supabase JWT access token
    const { data: { user }, error } = await supabase.auth.getUser(token);
    if (user && !error) {
      return formatUser(user);
    }

    // Attempt 2: Fallback for decoded base64 token "userId:::timestamp" or direct ID
    let candidateId = token;
    try {
      const decoded = Buffer.from(token, 'base64').toString('utf8');
      if (decoded.includes(':::')) {
        candidateId = decoded.split(':::')[0];
      }
    } catch (e) {}

    if (/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(candidateId)) {
      const adminUserRes = await supabase.auth.admin.getUserById(candidateId);
      if (adminUserRes.data && adminUserRes.data.user) {
        return formatUser(adminUserRes.data.user);
      }
    }
  } catch (err) {
    console.error('[Supabase Auth getProfile error]:', err.message);
  }

  return null;
}

/**
 * List all users from Supabase Cloud (For Admin Panel)
 */
async function listCloudUsers() {
  if (!supabase) return [];

  try {
    const { data, error } = await supabase.auth.admin.listUsers({ perPage: 1000 });
    if (error || !data?.users) {
      console.error('[Supabase listUsers error]:', error?.message);
      return [];
    }

    return data.users.map(formatUser);
  } catch (err) {
    console.error('[Supabase listUsers exception]:', err.message);
    return [];
  }
}

/**
 * Update user subscription in Supabase Cloud
 */
async function updateCloudUserSubscription(userId, subscriptionData) {
  if (!supabase) return null;

  try {
    const userRes = await supabase.auth.admin.getUserById(userId);
    if (!userRes.data?.user) {
      throw new Error('User not found');
    }

    const currentMeta = userRes.data.user.user_metadata || {};
    const updatedMeta = {
      ...currentMeta,
      subscription: subscriptionData
    };

    const { data, error } = await supabase.auth.admin.updateUserById(userId, {
      user_metadata: updatedMeta
    });

    if (error) throw error;
    return formatUser(data.user);
  } catch (err) {
    console.error('[Supabase updateSubscription exception]:', err.message);
    throw err;
  }
}

/**
 * Delete a user from Supabase Cloud
 */
async function deleteCloudUser(userId) {
  if (!supabase) return false;
  try {
    const { error } = await supabase.auth.admin.deleteUser(userId);
    return !error;
  } catch (e) {
    return false;
  }
}

module.exports = {
  supabase,
  registerCloudUser,
  loginCloudUser,
  getCloudUserProfile,
  listCloudUsers,
  updateCloudUserSubscription,
  deleteCloudUser,
  formatUser,
  evaluateSubscription
};
