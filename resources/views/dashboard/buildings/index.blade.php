@extends('dashboard.layout')

@section('content')
    <div class="flex justify-between items-center m-8 mb-4">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            المباني
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('buildings.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                إضافة مبنى جديد
            </a>
            <form action="{{ route('bills.create-contract') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 border  rounded-md font-semibold text-xs text-grey-200 uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    إضافة فواتير العقود
                </button>
            </form>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($buildings->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الرقم
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        اسم المبنى
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        عدد الطوابق
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        عدد الشقق
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تاريخ الإنشاء
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($buildings as $building)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $building->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $building->building_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $building->number_of_floors }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $building->number_of_apartments }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $building->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('buildings.show', $building) }}" 
                                               class="text-blue-600 hover:text-blue-900 ml-3">
                                                عرض
                                            </a>
                                            <a href="{{ route('dashboard.buildings.edit', $building) }}" 
                                               class="text-blue-600 hover:text-blue-900 mx-3">
                                                تعديل
                                            </a>
                                            <form class="inline" method="POST" action="{{ route('buildings.destroy', $building) }}" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المبنى؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $buildings->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            {{ __('No buildings found.') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
