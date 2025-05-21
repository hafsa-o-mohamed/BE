@extends('dashboard.layout')

@section('content')
    <div class="flex justify-between items-center m-8 mb-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            تفاصيل المبنى: {{ $building->building_name }}
        </h2>
      
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Building Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">اسم المبنى</label>
                            <p class="text-gray-600">{{ $building->building_name }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">عدد الطوابق</label>
                            <p class="text-gray-600">{{ $building->number_of_floors }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">عدد الشقق</label>
                            <p class="text-gray-600">{{ $building->number_of_apartments }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">تاريخ الإنشاء</label>
                            <p class="text-gray-600">{{ $building->created_at->format('Y-m-d') }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">تاريخ آخر تحديث</label>
                            <p class="text-gray-600">{{ $building->updated_at->format('Y-m-d') }}</p>
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
                        
                       
                    </nav>
                </div>

                <!-- Tab Contents -->
                <div class="p-6">
                    <div x-show="activeTab === 'water'" x-transition>
                        @include('dashboard.buildings._water_bills', ['bills' => $building->waterBills])
                    </div>
                    
                    <div x-show="activeTab === 'electricity'" x-transition>
                        @include('dashboard.buildings._electricity_bills', ['bills' => $building->electricityBills])
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex space-x-4 justify-end">
                <a href="{{ route('dashboard.buildings.edit', $building) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    تعديل
                </a>
                <form class="inline" method="POST" action="{{ route('buildings.destroy', $building) }}" 
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المبنى؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                        حذف
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection