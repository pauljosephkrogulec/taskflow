'use client';

import { createContext, useContext, useState, useCallback, ReactNode } from 'react';
import { useRouter } from 'next/navigation';

interface AuthContextValue {
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue>({ logout: async () => {} });

export function AuthProvider({ children }: { children: ReactNode }) {
  const router = useRouter();

  const logout = useCallback(async () => {
    await fetch('/api/auth/logout', { method: 'POST' });
    router.push('/login');
  }, [router]);

  return <AuthContext.Provider value={{ logout }}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  return useContext(AuthContext);
}
