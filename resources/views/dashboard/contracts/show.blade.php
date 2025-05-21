@extends('dashboard.layout')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6" wire:poll.visible>
                @livewire('add-service-modal')
                <!-- Contract Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900" dir="rtl">
                        عقد رقم {{ $contract->id }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($contract->status === 'active') 
                            bg-green-100 text-green-800
                        @elseif($contract->status === 'pending')
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-red-100 text-red-800
                        @endif" dir="rtl">
                        {{ $contract->status === 'Active' ? 'نشط' : ($contract->status === 'pending' ? 'قيد الانتظار' : 'منتهي') }}
                    </span>
                </div>

                <!-- Contract Details -->
                <div class="grid grid-cols-2 gap-4 mb-6" dir="rtl">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">المبنى</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $contract->building->building_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">تاريخ البدء</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $contract->start_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">تاريخ الانتهاء</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $contract->end_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">القيمة الإجمالية</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ number_format($contract->yearly_price, 2) }} ريال</p>
                    </div>
                </div>

                <!-- Contract Services Section -->
                <div class="mt-8" 
                    wire:poll.30s
                    x-data
                    x-on:contract-updated.window="$wire.$refresh()"
                >
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900" dir="rtl">خدمات العقد</h2>
                        <button onclick="Livewire.dispatch('openModal', [{{ $contract->id }}])" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            إضافة خدمة
                        </button>
                    </div>

                    <!-- Services Table -->
                    <table class="min-w-full divide-y divide-gray-200" dir="rtl">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الخدمة
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    التكرار
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">الإجراءات</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($contract->services as $contractService)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $contractService->service->service_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $contractService->frequency_text }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <button onclick="Livewire.emit('openModal', 'edit-service-modal', {{ json_encode(['contractId' => $contract->id, 'serviceId' => $contractService->id]) }})" 
                                                class="text-indigo-600 hover:text-indigo-900">تعديل</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" colspan="3">
                                        لا توجد خدمات متاحة
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

