export interface User {
  id: string;
  email: string;
  name: string;
}

export interface ProjectMember {
  userId: string;
  role: 'owner' | 'member' | 'viewer';
  joinedAt: string;
}

export interface Project {
  id: string;
  name: string;
  ownerId: string;
  archived: boolean;
  createdAt: string;
  members: ProjectMember[];
}

export interface Comment {
  id: string;
  authorId: string;
  content: string;
  createdAt: string;
}

export type TaskStatus = 'todo' | 'in_progress' | 'review' | 'done';

export interface Task {
  id: string;
  projectId: string;
  title: string;
  description: string | null;
  status: TaskStatus;
  assigneeId: string | null;
  reporterId: string | null;
  dueDate: string | null;
  createdAt: string;
  updatedAt: string;
  comments: Comment[];
}

export interface HydraCollection<T> {
  '@context': string;
  '@id': string;
  '@type': string;
  totalItems: number;
  member: T[];
}

export interface AuthTokens {
  token: string;
  refresh_token: string;
}
