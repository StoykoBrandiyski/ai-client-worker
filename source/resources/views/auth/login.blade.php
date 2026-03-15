@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Welcome Back</h2>
    
    <form action="{{ route('authenticate') }}" method="POST" class="space-y-4">
        @csrf {{-- Don't forget this for security! --}}
        
        <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" required 
                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" required 
                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center text-sm text-gray-600">
                <input type="checkbox" class="mr-2"> Remember me
            </label>
            <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
        </div>

        <button type="submit" 
            class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
            Sign In
        </button>
    </form>
    
    <p class="mt-6 text-center text-sm text-gray-600">
        Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Sign up</a>
    </p>
</div>
@endsection