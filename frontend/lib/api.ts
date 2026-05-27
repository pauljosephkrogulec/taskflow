import { AuthTokens, HydraCollection, Project, Task } from '@/types';

const API_URL =
  typeof window === 'undefined'
    ? (process.env.API_INTERNAL_URL ?? 'http://nginx')
    : (process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8080');

class ApiError extends Error {
  constructor(
    public readonly status: number,
    message: string,
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

async function apiFetch<T>(
  path: string,
  init: RequestInit & { token?: string } = {},
): Promise<T> {
  const { token, ...rest } = init;
  const headers: Record<string, string> = {
    'Content-Type': 'application/ld+json',
    Accept: 'application/ld+json',
    ...(init.headers as Record<string, string>),
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const res = await fetch(`${API_URL}${path}`, { ...rest, headers });

  if (!res.ok) {
    const body = await res.text();
    throw new ApiError(res.status, body);
  }

  if (res.status === 204) return undefined as unknown as T;
  return res.json();
}

// ── Auth ──────────────────────────────────────────────────────────────────────

export async function apiLogin(email: string, password: string): Promise<AuthTokens> {
  return apiFetch<AuthTokens>('/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ email, password }),
  });
}

export async function apiRegister(
  email: string,
  password: string,
  name: string,
): Promise<{ id: string; email: string; name: string }> {
  return apiFetch('/auth/register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ email, password, name }),
  });
}

// ── Projects ──────────────────────────────────────────────────────────────────

export async function apiGetProjects(token: string): Promise<Project[]> {
  const data = await apiFetch<HydraCollection<Project>>('/api/projects', { token });
  return data.member;
}

export async function apiGetProject(id: string, token: string): Promise<Project> {
  return apiFetch<Project>(`/api/projects/${id}`, { token });
}

export async function apiCreateProject(name: string, token: string): Promise<Project> {
  return apiFetch<Project>('/api/projects', {
    method: 'POST',
    body: JSON.stringify({ name }),
    token,
  });
}

export async function apiUpdateProject(
  id: string,
  name: string,
  token: string,
): Promise<Project> {
  return apiFetch<Project>(`/api/projects/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/merge-patch+json', Accept: 'application/ld+json' },
    body: JSON.stringify({ name }),
    token,
  });
}

// ── Tasks ─────────────────────────────────────────────────────────────────────

export async function apiGetTasks(projectId: string, token: string): Promise<Task[]> {
  const data = await apiFetch<HydraCollection<Task>>(
    `/api/projects/${projectId}/tasks`,
    { token },
  );
  return data.member;
}

export async function apiGetTask(id: string, token: string): Promise<Task> {
  return apiFetch<Task>(`/api/tasks/${id}`, { token });
}

export async function apiCreateTask(
  projectId: string,
  payload: { title: string; description?: string; assigneeId?: string; dueDate?: string },
  token: string,
): Promise<Task> {
  return apiFetch<Task>(`/api/projects/${projectId}/tasks`, {
    method: 'POST',
    body: JSON.stringify(payload),
    token,
  });
}

export async function apiUpdateTask(
  id: string,
  payload: { title: string; description?: string | null; assigneeId?: string | null; dueDate?: string | null },
  token: string,
): Promise<Task> {
  return apiFetch<Task>(`/api/tasks/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/merge-patch+json', Accept: 'application/ld+json' },
    body: JSON.stringify(payload),
    token,
  });
}

export async function apiTransitionTask(
  id: string,
  status: string,
  token: string,
): Promise<Task> {
  return apiFetch<Task>(`/api/tasks/${id}/transition`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/merge-patch+json', Accept: 'application/ld+json' },
    body: JSON.stringify({ status }),
    token,
  });
}

// ── Comments ──────────────────────────────────────────────────────────────────

export async function apiAddComment(
  taskId: string,
  content: string,
  token: string,
): Promise<{ id: string; content: string; authorId: string; createdAt: string }> {
  return apiFetch(`/api/tasks/${taskId}/comments`, {
    method: 'POST',
    body: JSON.stringify({ content }),
    token,
  });
}

export { ApiError };
