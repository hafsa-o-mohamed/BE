@extends('dashboard.layout')

@section('content')
<div class="container px-4 py-8" dir="rtl">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">مالك الشقة: {{ $owner->name }}</h1>
        <a href="{{ route('owners.edit', $owner->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
            تعديل المالك
        </a>
    </div>

    <!-- Owner Info Card -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="flex space-x-6">
            <div>
                <span class="text-sm text-gray-500">البريد الإلكتروني:</span>
                <span class="text-sm mr-1">{{ $owner->email ?? 'غير متوفر' }}</span>
            </div>
            <div>
                <span class="text-sm text-gray-500">الهاتف:</span>
                <span class="text-sm mr-1">{{ $owner->phone ?? 'غير متوفر' }}</span>
            </div>
            <div>
                <span class="text-sm text-gray-500">حساب المستخدم:</span>
                <span class="text-sm mr-1">{{ $owner->user ? 'نعم' : 'لا' }}</span>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <!-- Apartment Details Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">تفاصيل الشقة</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">رقم الشقة</p>
                    <p class="text-xl font-bold text-gray-800">{{ $owner->apartments->first()->apartment_number }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">الطابق</p>
                    <p class="text-xl font-bold text-gray-800">{{ $owner->apartments->first()->floor_number }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">المبنى</p>
                    <p class="text-xl font-bold text-gray-800">{{ $owner->apartments->first()->building->name }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">الحالة</p>
                    <p class="text-xl font-bold text-gray-800">{{ ucfirst($owner->apartments->first()->status) }}</p>
                </div>
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">ملخص مالي</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">فواتير المياه</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($waterTotal, 2) }} ريال</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">فواتير الكهرباء</p>
                    <p class="text-xl font-bold text-yellow-600">{{ number_format($electricityTotal, 2) }} ريال</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">طلبات الخدمة</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($serviceTotal, 2) }} ريال</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">إجمالي المستحق</p>
                    <p class="text-xl font-bold text-red-600">{{ number_format($waterTotal + $electricityTotal + $serviceTotal, 2) }} ريال</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div x-data="{ activeTab: 'water' }" class="bg-white rounded-lg shadow">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex">
                <button 
                    @click="activeTab = 'water'" 
                    :class="{ 
                        'border-indigo-500 text-indigo-600': activeTab === 'water',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'water'
                    }"
                    class="flex-1 text-center py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                >
                    فواتير المياه
                </button>
                
                <button 
                    @click="activeTab = 'electricity'" 
                    :class="{ 
                        'border-indigo-500 text-indigo-600': activeTab === 'electricity',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'electricity'
                    }"
                    class="flex-1 text-center py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                >
                    فواتير الكهرباء
                </button>
                
                <button 
                    @click="activeTab = 'services'" 
                    :class="{ 
                        'border-indigo-500 text-indigo-600': activeTab === 'services',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'services'
                    }"
                    class="flex-1 text-center py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                >
                    طلبات الخدمة
                </button>
            </nav>
        </div>

        <!-- Tab Contents -->
        <div class="p-6">
            <div x-show="activeTab === 'water'" x-transition>
                @include('dashboard.owners._water_bills', ['bills' => $owner->bills->where('bill_type', 'water')])
            </div>
            
            <div x-show="activeTab === 'electricity'" x-transition>
                @include('dashboard.owners._electricity_bills', ['bills' => $owner->bills->where('bill_type', 'electricity')])
            </div>
            
            <div x-show="activeTab === 'services'" x-transition>
                @include('dashboard.owners._service_requests', ['serviceRequests' => $owner->serviceRequests])
            </div>
        </div>
    </div>
</div>
@endsection 