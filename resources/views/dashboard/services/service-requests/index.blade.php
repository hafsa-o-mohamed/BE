@extends('dashboard.layout')

@section('content')
<div class="container mx-auto mt-4 px-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">الخدمات المطلوبة</h2>
    </div>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success bg-green-500 text-white p-2 rounded mb-4">{{ session('success') }}</div>
            @endif
            
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4 text-right">رقم الطلب</th>
                        <th class="py-2 px-4 text-right">الخدمة</th>
                        <th class="py-2 px-4 text-right">المستأجر</th>
                        <th class="py-2 px-4 text-right">الشقة</th>
                        <th class="py-2 px-4 text-right">تاريخ الطلب</th>
                        <th class="py-2 px-4 text-right">الحالة</th>
                        <th class="py-2 px-4 text-right">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serviceRequests as $request)
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-2 px-4">#{{ $request->request_id }}</td>
                            <td class="py-2 px-4">{{ $request->service->service_name }}</td>
                            <td class="py-2 px-4">{{ $request->owner->name }}</td>
                            <td class="py-2 px-4">{{ $request->apartment->apartment_number }}</td>
                            <td class="py-2 px-4">{{ $request->request_date->format('Y-m-d') }}</td>
                            <td class="py-2 px-4">
                                <span class="px-2 py-1 rounded text-sm
                                    @if($request->status === 'Pending') bg-yellow-100 text-yellow-800
                                    @elseif($request->status === 'In Progress') bg-blue-100 text-blue-800
                                    @elseif($request->status === 'Completed') bg-green-100 text-green-800
                                    @endif">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td class="py-2 px-4">
                                <form action="{{ route('service-requests.update', $request->request_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" 
                                            class="rounded border-gray-300 text-sm">
                                        <option value="Pending" {{ $request->status === 'Pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="In Progress" {{ $request->status === 'In Progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="Completed" {{ $request->status === 'Completed' ? 'selected' : '' }}>مكتمل</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="mt-4">
                {{ $serviceRequests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection