@extends('dashboard.layout')

@section('content')
<div class="w-full">
    <h1 class="text-2xl font-bold mb-6">لوحة التحكم</h1>
    
    {{-- Top Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-4">
            <p class="text-gray-500 text-sm mb-1">المشاريع السكنية</p>
            <div class="flex items-center">
                <span class="text-2xl font-bold text-gray-800">{{ $projects }}</span>
                <div class="ml-2 p-2 bg-teal-100 rounded-lg"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-4">
            <p class="text-gray-500 text-sm mb-1">العقود النشطة</p>
            <div class="flex items-center">
                <span class="text-2xl font-bold text-gray-800">{{ $contracts }}</span>
                <div class="ml-2 p-2 bg-blue-100 rounded-lg"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-4">
            <p class="text-gray-500 text-sm mb-1">مستحقات المياه</p>
            <div class="flex items-center">
                <span class="text-2xl font-bold text-gray-800">{{ $negativeLatestWaterBills }}</span>
                <div class="ml-2 p-2 bg-cyan-100 rounded-lg"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-4">
            <p class="text-gray-500 text-sm mb-1">مستحقات الكهرباء</p>
            <div class="flex items-center">
                <span class="text-2xl font-bold text-gray-800">{{ $negativeLatestElectricityBills }}</span>
                <div class="ml-2 p-2 bg-yellow-100 rounded-lg"></div>
            </div>
        </div>
    </div>

    {{-- Tables Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Latest Contracts --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="font-semibold text-lg">آخر العقود</h2>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-right py-2 px-3">المبنى</th>
                            <th class="text-right py-2 px-3">المستأجر</th>
                            <th class="text-right py-2 px-3">تاريخ البداية</th>
                            <th class="text-right py-2 px-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestContracts as $contract)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3">{{ $contract->building->building_name }}</td>
                            <td class="py-2 px-3">{{ $contract->tenant_name }}</td>
                            <td class="py-2 px-3">{{ $contract->start_date }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $contract->status === 'Active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $contract->status === 'Active' ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Latest Water Bills --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="font-semibold text-lg">آخر فواتير المياه</h2>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-right py-2 px-3">المبنى</th>
                            <th class="text-right py-2 px-3">المبلغ</th>
                            <th class="text-right py-2 px-3">الرصيد</th>
                            <th class="text-right py-2 px-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestWaterBills as $bill)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3">{{ $bill->building->building_name }}</td>
                            <td class="py-2 px-3">{{ $bill->subtracted_amount }}</td>
                            <td class="py-2 px-3">{{ $bill->current_balance }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $bill->current_balance >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $bill->current_balance >= 0 ? 'غير مطالب بالدفع' : 'مطالب بالدفع' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Latest Service Requests --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="font-semibold text-lg">آخر طلبات الخدمة</h2>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-right py-2 px-3">المبنى</th>
                            <th class="text-right py-2 px-3">الخدمة</th>
                            <th class="text-right py-2 px-3">التاريخ</th>
                            <th class="text-right py-2 px-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestServiceRequests as $request)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3">{{ $request->apartment->building->building_name }}</td>
                            <td class="py-2 px-3">{{ $request->service->name }}</td>
                            <td class="py-2 px-3">{{ $request->created_at->format('Y-m-d') }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $request->status === 'completed' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                                    {{ $request->status === 'completed' ? 'مكتمل' : 'قيد التنفيذ' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Latest Suggestions & Complaints --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="font-semibold text-lg">آخر الاقتراحات والشكاوى</h2>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-right py-2 px-3">المبنى</th>
                            <th class="text-right py-2 px-3">النوع</th>
                            <th class="text-right py-2 px-3">الوصف</th>
                            <th class="text-right py-2 px-3">التاريخ</th>
                            <th class="text-right py-2 px-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestSuggestions as $suggestion)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3">{{ $suggestion->user->name }}</td>
                            <td class="py-2 px-3">{{ $suggestion->type }}</td>
                            <td class="py-2 px-3">{{ Str::limit($suggestion->content, 30) }}</td>
                            <td class="py-2 px-3">{{ $suggestion->created_at->format('Y-m-d') }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $suggestion->status === 'resolved' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                                    {{ $suggestion->status === 'resolved' ? 'مكتمل' : 'قيد التنفيذ' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection