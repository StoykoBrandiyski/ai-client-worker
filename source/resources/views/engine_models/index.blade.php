@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Engine Models</h1>
        <a href="{{ url('/engine/models/create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Model</a>
    </div>

    <form method="GET" class="mb-4 flex gap-4">
        <input type="text" name="name" placeholder="Filter by name..." value="{{ request('name') }}" class="border p-2 rounded w-full">
        <button class="bg-gray-800 text-white px-6 py-2 rounded">Search</button>
    </form>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
            <tr>
                <th class="p-4"><a href="?sort=name&direction={{ request('direction') == 'asc' ? 'desc' : 'asc' }}">Name ↕</a></th>
                <th class="p-4">Identifier</th>
                <th class="p-4">Engine</th>
                <th class="p-4 text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($models as $m)
                <tr class="border-t">
                    <td class="p-4">{{ $m->name }}</td>
                    <td class="p-4 font-mono text-sm">{{ $m->identifier }}</td>
                    <td class="p-4">{{ $m->engine->name }}</td>
                    <td class="p-4 text-right space-x-2">
                        <a href="{{ url('/engine/models/'. $m->identifier) }}" class="text-blue-600">View</a>
                        <form action="/engine/models" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <input type="hidden" name="id" value="{{ $m->identifier }}">
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $models->links() }}</div>
</div>
@endsection
