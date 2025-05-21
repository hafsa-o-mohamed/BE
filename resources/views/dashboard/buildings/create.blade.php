@extends('dashboard.layout')

@section('content')
<div class="container mx-auto mt-4 px-4">
    <h1 class="text-2xl font-bold mb-4">إضافة مبنى جديد</h1>

    <div class="bg-white shadow-md rounded-lg">
        <div class="p-6">
            <form action="{{ route('buildings.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="project_id" class="block text-sm font-medium text-gray-700">اختر المشروع</label>
                    <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="project_id" name="project_id" required>
                        <option value="">اختر مشروع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="building_name" class="block text-sm font-medium text-gray-700">اسم المبنى</label>
                    <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-right" id="building_name" name="building_name" required>
                </div>
                <div class="mb-4">
                    <label for="number_of_floors" class="block text-sm font-medium text-gray-700">عدد الطوابق</label>
                    <input type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-right" id="number_of_floors" name="number_of_floors" required min="1">
                </div>
                <div class="mb-4">
                    <label for="number_of_apartments" class="block text-sm font-medium text-gray-700">عدد الشقق</label>
                    <input type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-right" id="number_of_apartments" name="number_of_apartments" required min="1">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">إنشاء مبنى</button>
            </form>
        </div>
    </div>
</div>
@endsection