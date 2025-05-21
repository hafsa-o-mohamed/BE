@extends('dashboard.layout')

@section('content')
<div class="container mx-auto px-4 mt-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">المشاريع</h1>
        <a href="{{ route('projects.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">إضافة مشروع جديد</a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم المشروع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإنشاء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($projects as $project)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->project_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->address }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" >{{ $project->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>
@endsection