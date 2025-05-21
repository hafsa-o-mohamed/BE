@extends('dashboard.layout')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">الاقتراحات والشكاوي</h2>
        </div>

        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <form action="{{ route('suggestions.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">النوع</label>
                    <select name="type" id="type" class="w-full rounded-md border-gray-300">
                        <option value="">الكل</option>
                        <option value="suggestion" {{ request('type') === 'suggestion' ? 'selected' : '' }}>اقتراح</option>
                        <option value="complaint" {{ request('type') === 'complaint' ? 'selected' : '' }}>شكوى</option>
                    </select>
                </div>
                
                <div class="flex-1 min-w-[200px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>تمت المراجعة</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>تم الحل</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300">
                </div>

                <div class="flex items-end w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        تطبيق الفلتر
                    </button>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 border-b border-gray-200 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            المستخدم
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            النوع
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            المحتوى
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            التاريخ
                        </th>
                        <th class="px-6 py-3 border-b border-gray-200 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($suggestions as $suggestion)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                {{ $suggestion->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $suggestion->type === 'suggestion' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $suggestion->type === 'suggestion' ? 'اقتراح' : 'شكوى' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs overflow-hidden">
                                    {{ Str::limit($suggestion->content, 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <form action="{{ route('suggestions.update', $suggestion->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300">
                                        <option value="pending" {{ $suggestion->status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="reviewed" {{ $suggestion->status === 'reviewed' ? 'selected' : '' }}>تمت المراجعة</option>
                                        <option value="resolved" {{ $suggestion->status === 'resolved' ? 'selected' : '' }}>تم الحل</option>
                                        <option value="rejected" {{ $suggestion->status === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm text-gray-500">
                                {{ $suggestion->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-sm font-medium">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <a href="{{ route('suggestions.show', $suggestion->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class='bx bx-show'></i>
                                    </a>
                                    <form action="{{ route('suggestions.destroy', $suggestion->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                لا توجد اقتراحات أو شكاوي حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $suggestions->links() }}
        </div>
    </div>
</div>
@endsection