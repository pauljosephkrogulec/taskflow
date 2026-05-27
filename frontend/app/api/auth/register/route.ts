import { NextRequest, NextResponse } from 'next/server';

const API = process.env.API_INTERNAL_URL ?? 'http://nginx';

export async function POST(req: NextRequest) {
  const body = await req.json();

  const upstream = await fetch(`${API}/auth/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body),
  });

  const data = await upstream.json();
  return NextResponse.json(data, { status: upstream.status });
}
