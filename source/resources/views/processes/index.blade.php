@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Process Management</h1>
            <a href="/processes/create" class="bg-blue-600 text-white px-4 py-2 rounded">Create Process</a>
        </div>

        <form method="GET" action="/processes" class="mb-6 flex gap-2">
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name..." class="border rounded px-4 py-2">
            <select name="status" class="border rounded px-4 py-2 bg-white">
                <option value="">All Statuses</option>
                <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Search</button>
        </form>

        <div class="bg-white rounded shadow">
            <table class="w-full text-left">
                <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-4"><a href="?sort=name&direction={{ request('direction') == 'asc' ? 'desc' : 'asc' }}">Name ↕</a></th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Schedule</th>
                    <th class="p-4">Enabled</th>
                    <th class="p-4">Next Schedule</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($processes ?? [] as $process)
                    <tr class="border-b">
                        <td class="p-4">{{ $process->name }}</td>
                        <td class="p-4">{{ $process->status }}</td>
                        <td class="p-4 font-mono text-sm">{{ $process->schedule }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs text-white {{ $process->is_enabled ? 'bg-green-500' : 'bg-red-500' }}">
                                {{ $process->is_enabled ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="text-[10px] text-blue-500 uppercase font-bold">
                                Next: {{ \Cron\CronExpression::factory((string) $process->schedule)->getNextRunDate()->format('Y-m-d H:i') }}
                            </div>
                        </td>
                        <td class="p-4 text-right flex justify-end gap-2">
                            <a href="/processes/{{ $process->id }}" class="text-blue-600">View</a>
                            <a href="/processes/edit/{{ $process->id }}" class="text-orange-500">Edit</a>
                            <form action="/processes" method="POST" onsubmit="return confirm('Delete?');">
                                @csrf @method('DELETE')
                                <input type="hidden" name="id" value="{{ $process->id }}">
                                <button type="submit" class="text-red-600">Del</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-center">No processes found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $processes->links() }}</div>
    </div>
@endsection
