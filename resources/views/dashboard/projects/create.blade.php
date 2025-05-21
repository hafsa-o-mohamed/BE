@extends('dashboard.layout')

@section('content')
<div class="container mx-auto mt-4 px-4">
    <h1 class="text-2xl font-bold mb-4">إضافة مشروع جديد</h1>

    <div class="bg-white shadow-md rounded-lg">
        <div class="p-6">
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="project_name" class="block text-sm font-medium text-gray-700">اسم المشروع</label>
                    <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="project_name" name="project_name" required>
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">العنوان</label>
                    <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="address" name="address">
                </div>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">إضافة المشروع</button>
            </form>
        </div>
    </div>
</div>
@endsection