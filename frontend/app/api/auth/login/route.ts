import { NextRequest, NextResponse } from 'next/server';

const API = process.env.API_INTERNAL_URL ?? 'http://nginx';

export async function POST(req: NextRequest) {
  const body = await req.json();

  const upstream = await fetch(`${API}/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body),
  });

  const data = await upstream.json();

  if (!upstream.ok) {
    return NextResponse.json(data, { status: upstream.status });
  }

  const res = NextResponse.json({ ok: true });

  // Store JWT in httpOnly cookie — not accessible from JavaScript
  res.cookies.set('tf_token', data.token, {
    httpOnly: true,
    sameSite: 'lax',
    maxAge: 60 * 60,        // 1 hour
    path: '/',
  });
  res.cookies.set('tf_refresh', data.refresh_token, {
    httpOnly: true,
    sameSite: 'lax',
    maxAge: 60 * 60 * 24 * 30, // 30 days
    path: '/',
  });

  return res;
}
