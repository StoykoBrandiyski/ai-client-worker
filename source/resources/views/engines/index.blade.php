@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Engine Management</h1>
            <a href="/engines/create" class="bg-blue-600 text-white px-4 py-2 rounded">Add Engine</a>
        </div>

        <form method="GET" action="/engines" class="mb-6 flex gap-2">
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search by name..." class="border border-gray-300 rounded px-4 py-2">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Search</button>
        </form>

        <div class="bg-white rounded shadow">
            <table class="w-full text-left">
                <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-4"><a href="?sort=name&direction={{ request('direction') == 'asc' ? 'desc' : 'asc' }}">Name ↕</a></th>
                    <th class="p-4">Base URL</th>
                    <th class="p-4">Max Tasks</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($engines ?? [] as $engine)
                    <tr class="border-b">
                        <td class="p-4">{{ $engine->name }}</td>
                        <td class="p-4">{{ $engine->base_url }}</td>
                        <td class="p-4">{{ $engine->max_tasks_count }}</td>
                        <td class="p-4 text-right flex justify-end gap-2">
                            <a href="/engines/{{ $engine->id }}" class="text-blue-600">View</a>
                            <form action="/engines" method="POST" onsubmit="return confirm('Delete this engine?');">
                                @csrf @method('DELETE')
                                <input type="hidden" name="id" value="{{ $engine->id }}">
                                <button type="submit" class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center">No engines found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $engines->links() }}</div>
    </div>
@endsection
