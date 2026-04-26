@extends('layouts.app')

@section('content')
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">AI Worker Dashboard</h1>
                    <p class="text-sm text-gray-500">Real-time status of your AI processing pipeline.</p>
                </div>
                <div class="flex items-center space-x-2 bg-white px-3 py-1 rounded-full border shadow-sm">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-medium text-gray-600 uppercase tracking-wider">System Online</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-stat-card title="Total Tasks" :value="$stats['total_tasks']" color="indigo" />
                <x-stat-card title="Pending" :value="$stats['pending_tasks']" color="orange" />
                <x-stat-card title="Success Rate" :value="$stats['success_rate'] . '%'" color="green" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">
                    <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">Recent Process Executions</h3>
                            <a href="{{ route('process-logs.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View All Logs →</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase">
                                <tr>
                                    <th class="px-6 py-3">Process</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Tasks</th>
                                    <th class="px-6 py-3 text-right">Time</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                @foreach($latestLogs as $log)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $log->process->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->engine->name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $log->status === 'ready' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($log->task_ids)
                                                <div class="flex -space-x-1">
                                                    @foreach(array_slice($log->task_ids, 0, 3) as $id)
                                                        <a href="/tasks/{{ $id }}" class="h-6 w-6 rounded-full bg-white border border-gray-200 flex items-center justify-center text-[10px] font-mono text-indigo-600 hover:bg-indigo-50">#{{ $id }}</a>
                                                    @endforeach
                                                    @if(count($log->task_ids) > 3)
                                                        <span class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] text-gray-500">+{{ count($log->task_ids) - 3 }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-xs text-gray-400 font-mono">
                                            {{ $log->started_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="font-bold text-gray-800 mb-4">Latest Tasks</h3>
                        <div class="space-y-4">
                            @foreach($tasks as $task)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs font-bold">
                                            {{ substr($task->engine->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 truncate w-32">{{ $task->name }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $task->status }}</p>
                                        </div>
                                    </div>
                                    <a href="/tasks/{{ $task->id }}" class="p-1 hover:bg-gray-100 rounded">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="bg-indigo-900 rounded-xl shadow-lg p-6 text-white overflow-hidden relative">
                        <div class="relative z-10">
                            <h3 class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-4">Internal Node</h3>
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-indigo-800 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-lg font-mono leading-none">Ollama:11434</p>
                                    <p class="text-[10px] text-indigo-300 mt-1">Status: Listening for JSON</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 text-indigo-800 opacity-20 transform -rotate-12">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>
@endsection
