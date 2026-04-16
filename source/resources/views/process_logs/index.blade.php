@extends('layouts.app')

@section('content')
<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Process Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Engine</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Engine Model</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timeline</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tasks</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Error</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        @foreach($logs as $log)
            <tr>
                <td class="px-6 py-4 font-bold text-gray-900">{{ $log->process->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->engine->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $log->engineModel->name }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 text-xs rounded-full {{ $log->status == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($log->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">
                    <div class="font-semibold text-gray-700">Duration: {{ $log->started_at->diffInSeconds($log->finished_at) }}s</div>
                    <div>Start: {{ $log->started_at->format('H:i:s') }}</div>
                </td>
                <td class="px-6 py-4">
                    @if($log->task_id)
                        <div class="flex flex-wrap gap-1">
                            <a href="/tasks/{{ $log->task_id }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-mono bg-indigo-50 px-1 rounded border border-indigo-200">
                                #{{ $log->task_id }}
                            </a>
                        </div>
                    @else
                        <span class="text-gray-400 text-xs">None</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-xs text-red-500 italic max-w-xs truncate">
                    {{ $log->process_message ?? '—' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $logs->links() }}
</div>

@endsection
