@extends('dashboard.layout')

@section('content')
@php
use Illuminate\Support\Str;
@endphp
<div class="container mx-auto mt-4 px-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">الخدمات</h2>
        <a href="{{ route('services.create') }}" class="btn btn-primary bg-blue-500 text-white rounded-lg px-4 py-2">إضافة خدمة جديدة</a>
    </div>
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الصورة
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        اسم الخدمة
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الوصف
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الإجراءات
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($services as $service)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($service->image_url)
                                <img src="{{ asset('/' . $service->image_url) }}" 
                                     alt="{{ $service->service_name }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-400 text-xs">لا توجد صورة</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $service->service_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ Str::limit($service->description, 100) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <a href="{{ route('services.edit', $service) }}" 
                               class="btn btn-sm btn-primary bg-blue-500 text-white rounded px-2 py-1 ml-2">تعديل</a>
                            <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-danger bg-red-500 text-white rounded px-2 py-1" 
                                        onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">
            {{ $services->links() }}
        </div>
    </div>
</div>
@endsection