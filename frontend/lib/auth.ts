'use client';

import Cookies from 'js-cookie';

const TOKEN_KEY   = 'tf_token';
const REFRESH_KEY = 'tf_refresh';

export function getToken(): string | undefined {
  return Cookies.get(TOKEN_KEY);
}

export function setTokens(token: string, refreshToken: string): void {
  Cookies.set(TOKEN_KEY, token, { expires: 1, sameSite: 'lax' });
  Cookies.set(REFRESH_KEY, refreshToken, { expires: 30, sameSite: 'lax' });
}

export function clearTokens(): void {
  Cookies.remove(TOKEN_KEY);
  Cookies.remove(REFRESH_KEY);
}
