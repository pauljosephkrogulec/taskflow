import { cookies } from 'next/headers';
import { redirect, notFound } from 'next/navigation';
import Link from 'next/link';
import { apiGetProject, apiGetTasks } from '@/lib/api';
import KanbanBoard from '@/components/task/KanbanBoard';
import ReactQueryProvider from '@/components/ui/ReactQueryProvider';

interface Props {
  params: Promise<{ id: string }>;
}

export default async function ProjectPage({ params }: Props) {
  const { id } = await params;
  const cookieStore = await cookies();
  const token = cookieStore.get('tf_token')?.value;

  if (!token) redirect('/login');

  try {
    const [project, tasks] = await Promise.all([
      apiGetProject(id, token),
      apiGetTasks(id, token),
    ]);

    return (
      <div className="min-h-screen bg-gray-50">
        <header className="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4">
          <Link href="/dashboard" className="text-gray-400 hover:text-gray-600 text-sm transition-colors">
            ← Projects
          </Link>
          <h1 className="text-lg font-semibold text-gray-900">{project.name}</h1>
          <span className="text-sm text-gray-400">{project.members.length} members</span>
        </header>

        <main className="px-6 py-6">
          <ReactQueryProvider>
            <KanbanBoard projectId={id} initialTasks={tasks} token={token} />
          </ReactQueryProvider>
        </main>
      </div>
    );
  } catch {
    notFound();
  }
}
