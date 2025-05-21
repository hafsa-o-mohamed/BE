@extends('dashboard.layout')

@section('content')
<div class="container mx-auto mt-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="text-xl font-bold mb-4">Create New Service</div>
            <form method="POST" action="{{ route('services.store') }}">
                @csrf
                <div class="form-group mb-4">
                    <label class="block text-sm font-medium text-gray-700">Service Name</label>
                    <input type="text" name="service_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('service_name') border-red-500 @enderror" value="{{ old('service_name') }}">
                    @error('service_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-between mt-6">
                    <button type="submit" class="bg-blue-500 text-white rounded-lg px-4 py-2 hover:bg-blue-600">Create Service</button>
                    <a href="{{ route('services.index') }}" class="bg-gray-300 text-gray-700 rounded-lg px-4 py-2 hover:bg-gray-400">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection