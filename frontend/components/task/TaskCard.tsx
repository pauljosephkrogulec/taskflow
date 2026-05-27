'use client';

import { Task } from '@/types';

interface Props {
  task: Task;
  onClick: (task: Task) => void;
}

export default function TaskCard({ task, onClick }: Props) {
  return (
    <button
      onClick={() => onClick(task)}
      className="w-full text-left bg-white rounded-xl border border-gray-200 p-3 hover:shadow-sm hover:border-blue-200 transition-all group"
    >
      <p className="text-sm font-medium text-gray-900 group-hover:text-blue-600 line-clamp-2">
        {task.title}
      </p>
      {task.dueDate && (
        <p className="text-xs text-gray-400 mt-2">
          Due {new Date(task.dueDate).toLocaleDateString()}
        </p>
      )}
      {task.assigneeId && (
        <div className="mt-2 flex items-center gap-1">
          <div className="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center">
            <span className="text-xs text-blue-600 font-medium">
              {task.assigneeId.slice(0, 1).toUpperCase()}
            </span>
          </div>
        </div>
      )}
    </button>
  );
}
