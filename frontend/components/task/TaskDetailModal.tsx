'use client';

import { useState, useEffect, useRef } from 'react';
import { useMutation } from '@tanstack/react-query';
import { Task } from '@/types';
import { apiUpdateTask, apiAddComment } from '@/lib/api';

interface Props {
  task: Task;
  token: string;
  onClose: () => void;
  onUpdate: (updated: Task) => void;
}

export default function TaskDetailModal({ task, token, onClose, onUpdate }: Props) {
  const [title, setTitle]             = useState(task.title);
  const [description, setDescription] = useState(task.description ?? '');
  const [dueDate, setDueDate]         = useState(task.dueDate ?? '');
  const [commentText, setCommentText] = useState('');
  const [comments, setComments]       = useState(task.comments);
  const [editMode, setEditMode]       = useState(false);
  const overlayRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handler = (e: KeyboardEvent) => {
      if (e.key === 'Escape') onClose();
    };
    window.addEventListener('keydown', handler);
    return () => window.removeEventListener('keydown', handler);
  }, [onClose]);

  const updateMutation = useMutation({
    mutationFn: () =>
      apiUpdateTask(
        task.id,
        {
          title,
          description: description || null,
          dueDate: dueDate || null,
        },
        token,
      ),
    onSuccess: (updated) => {
      onUpdate(updated);
      setEditMode(false);
    },
  });

  const commentMutation = useMutation({
    mutationFn: () => apiAddComment(task.id, commentText, token),
    onSuccess: (comment) => {
      setComments((prev) => [
        ...prev,
        {
          id:        comment.id,
          authorId:  comment.authorId,
          content:   comment.content,
          createdAt: comment.createdAt,
        },
      ]);
      setCommentText('');
    },
  });

  return (
    <div
      ref={overlayRef}
      className="fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4"
      onClick={(e) => e.target === overlayRef.current && onClose()}
    >
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div className="p-6 border-b border-gray-100 flex items-start justify-between">
          {editMode ? (
            <input
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              className="flex-1 text-xl font-semibold border-b-2 border-blue-500 outline-none mr-4"
              autoFocus
            />
          ) : (
            <h2 className="text-xl font-semibold text-gray-900 flex-1 mr-4">{task.title}</h2>
          )}
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600 text-xl font-light"
          >
            ✕
          </button>
        </div>

        <div className="p-6 space-y-6">
          {/* Status badge */}
          <div className="flex items-center gap-2 text-sm">
            <span className="text-gray-500">Status:</span>
            <span className="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium capitalize">
              {task.status.replace('_', ' ')}
            </span>
          </div>

          {/* Description */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            {editMode ? (
              <textarea
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                rows={4}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                placeholder="Add a description…"
              />
            ) : (
              <p className="text-sm text-gray-600 whitespace-pre-wrap">
                {task.description ?? <span className="text-gray-400 italic">No description</span>}
              </p>
            )}
          </div>

          {/* Due date */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Due date</label>
            {editMode ? (
              <input
                type="date"
                value={dueDate}
                onChange={(e) => setDueDate(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              />
            ) : (
              <p className="text-sm text-gray-600">
                {task.dueDate
                  ? new Date(task.dueDate).toLocaleDateString()
                  : <span className="text-gray-400 italic">No due date</span>
                }
              </p>
            )}
          </div>

          {/* Edit actions */}
          <div className="flex gap-2">
            {editMode ? (
              <>
                <button
                  onClick={() => updateMutation.mutate()}
                  disabled={updateMutation.isPending}
                  className="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
                >
                  {updateMutation.isPending ? 'Saving…' : 'Save'}
                </button>
                <button
                  onClick={() => setEditMode(false)}
                  className="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors"
                >
                  Cancel
                </button>
              </>
            ) : (
              <button
                onClick={() => setEditMode(true)}
                className="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors"
              >
                Edit
              </button>
            )}
          </div>

          {/* Comments */}
          <div>
            <h3 className="text-sm font-semibold text-gray-700 mb-3">
              Comments ({comments.length})
            </h3>

            <div className="space-y-3 mb-4">
              {comments.map((c) => (
                <div key={c.id} className="bg-gray-50 rounded-xl p-3">
                  <div className="flex items-center gap-2 mb-1">
                    <div className="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center">
                      <span className="text-xs text-blue-600 font-medium">
                        {c.authorId.slice(0, 1).toUpperCase()}
                      </span>
                    </div>
                    <span className="text-xs text-gray-400">
                      {new Date(c.createdAt).toLocaleString()}
                    </span>
                  </div>
                  <p className="text-sm text-gray-700 ml-8">{c.content}</p>
                </div>
              ))}
            </div>

            <div className="flex gap-2">
              <input
                value={commentText}
                onChange={(e) => setCommentText(e.target.value)}
                onKeyDown={(e) => e.key === 'Enter' && !e.shiftKey && commentMutation.mutate()}
                placeholder="Add a comment…"
                className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              />
              <button
                onClick={() => commentMutation.mutate()}
                disabled={!commentText.trim() || commentMutation.isPending}
                className="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
              >
                Post
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
