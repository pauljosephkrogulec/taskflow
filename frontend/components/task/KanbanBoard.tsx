'use client';

import { useState, useCallback } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Task, TaskStatus } from '@/types';
import { apiTransitionTask } from '@/lib/api';
import TaskCard from './TaskCard';
import TaskDetailModal from './TaskDetailModal';

const COLUMNS: { status: TaskStatus; label: string; color: string }[] = [
  { status: 'todo',        label: 'To Do',       color: 'bg-gray-100 text-gray-600' },
  { status: 'in_progress', label: 'In Progress',  color: 'bg-blue-100 text-blue-600' },
  { status: 'review',      label: 'Review',       color: 'bg-yellow-100 text-yellow-700' },
  { status: 'done',        label: 'Done',         color: 'bg-green-100 text-green-700' },
];

interface Props {
  projectId: string;
  initialTasks: Task[];
  token: string;
}

export default function KanbanBoard({ projectId, initialTasks, token }: Props) {
  const queryClient   = useQueryClient();
  const [tasks, setTasks]           = useState(initialTasks);
  const [selectedTask, setSelectedTask] = useState<Task | null>(null);
  const [dragTaskId, setDragTaskId] = useState<string | null>(null);

  const transitionMutation = useMutation({
    mutationFn: ({ id, status }: { id: string; status: string }) =>
      apiTransitionTask(id, status, token),
    onSuccess: (updated) => {
      setTasks((prev) =>
        prev.map((t) => (t.id === updated.id ? updated : t)),
      );
      queryClient.invalidateQueries({ queryKey: ['tasks', projectId] });
    },
  });

  const handleDragStart = (e: React.DragEvent, taskId: string) => {
    setDragTaskId(taskId);
    e.dataTransfer.effectAllowed = 'move';
  };

  const handleDrop = (e: React.DragEvent, targetStatus: TaskStatus) => {
    e.preventDefault();
    if (!dragTaskId) return;
    const task = tasks.find((t) => t.id === dragTaskId);
    if (!task || task.status === targetStatus) return;
    transitionMutation.mutate({ id: dragTaskId, status: targetStatus });
    setDragTaskId(null);
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
  };

  const addTaskOptimistically = useCallback((task: Task) => {
    setTasks((prev) => [task, ...prev]);
  }, []);

  const updateTaskOptimistically = useCallback((updated: Task) => {
    setTasks((prev) => prev.map((t) => (t.id === updated.id ? updated : t)));
    if (selectedTask?.id === updated.id) setSelectedTask(updated);
  }, [selectedTask]);

  return (
    <>
      <div className="grid grid-cols-4 gap-4 h-full">
        {COLUMNS.map(({ status, label, color }) => {
          const columnTasks = tasks.filter((t) => t.status === status);

          return (
            <div
              key={status}
              className="flex flex-col min-h-[60vh]"
              onDrop={(e) => handleDrop(e, status)}
              onDragOver={handleDragOver}
            >
              <div className="flex items-center justify-between mb-3">
                <div className="flex items-center gap-2">
                  <span className={`text-xs font-medium px-2 py-0.5 rounded-full ${color}`}>
                    {label}
                  </span>
                  <span className="text-xs text-gray-400">{columnTasks.length}</span>
                </div>
              </div>

              <div className="flex-1 space-y-2 rounded-xl min-h-[8rem] p-2 bg-gray-50 border-2 border-dashed border-transparent hover:border-gray-200 transition-colors">
                {columnTasks.map((task) => (
                  <div
                    key={task.id}
                    draggable
                    onDragStart={(e) => handleDragStart(e, task.id)}
                    className="cursor-grab active:cursor-grabbing"
                  >
                    <TaskCard task={task} onClick={setSelectedTask} />
                  </div>
                ))}
              </div>
            </div>
          );
        })}
      </div>

      {selectedTask && (
        <TaskDetailModal
          task={selectedTask}
          token={token}
          onClose={() => setSelectedTask(null)}
          onUpdate={updateTaskOptimistically}
        />
      )}
    </>
  );
}
