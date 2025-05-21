@extends('dashboard.layout')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-2xl font-bold mb-2">تفاصيل الاقتراح / الشكوى</h2>
        <div class="mb-4">
            <strong>العنوان:</strong> {{ $suggestion->title ?? '-' }}<br>
            <strong>النوع:</strong> {{ $suggestion->type }}<br>
            <strong>المستخدم:</strong> {{ $suggestion->user->name ?? '-' }}<br>
            <strong>التاريخ:</strong> {{ $suggestion->created_at->format('Y-m-d H:i') }}
        </div>
        <div class="mb-4">
            <strong>النص:</strong>
            <div class="bg-gray-100 rounded p-3 mt-2">
                {{ $suggestion->content }}
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow p-6">
        <h3 class="text-xl font-bold mb-4">الردود</h3>
        @forelse($replies as $reply)
            <div class="mb-4 border-b pb-2">
                <div class="text-gray-700 mb-1">{{ $reply->reply }}</div>
                <div class="text-xs text-gray-500">
                    بواسطة: {{ $reply->user->name ?? 'إدارة النظام' }} | {{ $reply->created_at->format('Y-m-d H:i') }}
                </div>
            </div>
        @empty
            <div class="text-gray-500">لا توجد ردود بعد.</div>
        @endforelse
    </div>

    @if(auth()->user() && auth()->user()->role === 'admin')
        <div class="bg-white rounded shadow p-6 mt-6">
            <h3 class="text-xl font-bold mb-4">إضافة رد</h3>
            <form method="POST" action="{{ route('suggestions.reply', $suggestion->id) }}">
                @csrf
                <div class="mb-4">
                    <textarea name="reply" rows="3" class="w-full border rounded p-2" placeholder="اكتب الرد هنا..." required>{{ old('reply') }}</textarea>
                    @error('reply')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">إرسال الرد</button>
            </form>
        </div>
    @endif
</div>
@endsection
