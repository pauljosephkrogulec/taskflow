import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import Link from 'next/link';
import { apiGetProjects } from '@/lib/api';
import { Project } from '@/types';
import NewProjectButton from '@/components/project/NewProjectButton';

function ProjectCard({ project }: { project: Project }) {
  const memberCount = project.members.length;
  return (
    <Link
      href={`/projects/${project.id}`}
      className="block bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-md hover:border-blue-200 transition-all group"
    >
      <div className="flex items-start justify-between mb-3">
        <h3 className="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
          {project.name}
        </h3>
        {project.archived && (
          <span className="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
            Archived
          </span>
        )}
      </div>
      <p className="text-sm text-gray-500">
        {memberCount} {memberCount === 1 ? 'member' : 'members'}
      </p>
      <p className="text-xs text-gray-400 mt-2">
        Created {new Date(project.createdAt).toLocaleDateString()}
      </p>
    </Link>
  );
}

export default async function DashboardPage() {
  const cookieStore = await cookies();
  const token = cookieStore.get('tf_token')?.value;

  if (!token) redirect('/login');

  let projects: Project[] = [];
  try {
    projects = await apiGetProjects(token);
  } catch {
    redirect('/login');
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">TaskFlow</h1>
        <form action="/api/auth/logout" method="POST">
          <button
            type="submit"
            className="text-sm text-gray-500 hover:text-gray-900 transition-colors"
          >
            Sign out
          </button>
        </form>
      </header>

      <main className="max-w-5xl mx-auto px-6 py-8">
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-2xl font-bold text-gray-900">Projects</h2>
          <NewProjectButton />
        </div>

        {projects.length === 0 ? (
          <div className="text-center py-20">
            <p className="text-gray-400 text-lg mb-4">No projects yet</p>
            <p className="text-gray-400 text-sm">Create your first project to get started</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {projects.map((p) => (
              <ProjectCard key={p.id} project={p} />
            ))}
          </div>
        )}
      </main>
    </div>
  );
}
