@extends('dashboard.layout')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-center">
        <div class="w-full max-w-lg">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="text-lg font-bold mb-4">Edit Service</div>
                <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Service Name</label>
                        <input type="text" name="service_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('service_name') border-red-500 @enderror" value="{{ old('service_name', $service->service_name) }}" placeholder="Enter service name">
                        @error('service_name')
                            <span class="text-red-500 text-xs italic">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror" rows="4" placeholder="Enter service description">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-xs italic">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Service Image</label>
                        <input type="file" name="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('image') border-red-500 @enderror" accept="image/*">
                        @error('image')
                            <span class="text-red-500 text-xs italic">{{ $message }}</span>
                        @enderror
                        @if($service->img_url)
                            <div class="mt-2">
                                <img src="{{ asset($service->img_url) }}" alt="Current service image" class="w-32 h-32 object-cover">
                                <p class="text-sm text-gray-600">Current image</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Service</button>
                        <a href="{{ route('services.index') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection