@extends('dashboard.layout')

@section('content')
<div class="container mx-auto mt-4 px-4">
    <h1 class="text-2xl font-bold mb-4">إضافة شقة جديدة</h1>

    <div class="bg-white shadow-md rounded-lg">
        <div class="p-6">
            <form action="{{ route('apartments.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="building_id" class="block text-sm font-medium text-gray-700">اختر المبنى</label>
                    <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="building_id" name="building_id" required>
                        <option value="">اختر مبنى</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->building_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="floor_number" class="block text-sm font-medium text-gray-700">رقم الطابق</label>
                    <input type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-right" id="floor_number" name="floor_number" required >
                </div>
                <div class="mb-4">
                    <label for="apartment_number" class="block text-sm font-medium text-gray-700">رقم الشقة</label>
                    <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-right" id="apartment_number" name="apartment_number" required>
                </div>
                <div class="mb-4">
                    <label for="owner_id" class="block text-sm font-medium text-gray-700">اختر المالك</label>
                    <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="owner_id" name="owner_id">
                        <option value="">لا يوجد مالك</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">إنشاء شقة</button>
            </form>
        </div>
    </div>
</div>
@endsection