@extends('dashboard.layout')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Create New Contract') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">
                    <form method="POST" action="{{ route('contracts.store') }}" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Contract Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Contract Title') }}</label>
                                <input id="title" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" 
                                    type="text" name="title" value="{{ old('title') }}" required autofocus />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contract Type -->
                            <div>
                                <label for="contract_type" class="block text-sm font-medium text-gray-700">{{ __('Contract Type') }}</label>
                                <select id="contract_type" name="contract_type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                    <option value="">Select a type</option>
                                    <option value="maintenance" {{ old('contract_type') == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                                </select>
                            </div>

                            <!-- Building Selection -->
                            <div>
                                <label for="building_id" class="block text-sm font-medium text-gray-700">{{ __('Building') }}</label>
                                <select id="building_id" name="building_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                    <option value="">Select a building</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                            {{ $building->building_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Duration, Start Date, End Date -->
                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700">{{ __('Duration (Years)') }}</label>
                                <input type="number" id="duration" name="duration" min="1" max="99" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" 
                                    value="{{ old('duration') }}" required />
                            </div>
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                                <input type="date" id="start_date" name="start_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" 
                                    value="{{ old('start_date') }}" required />
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                                <input type="date" id="end_date" name="end_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" 
                                    value="{{ old('end_date') }}" required />
                            </div>

                            <!-- Yearly Price -->
                            <div>
                                <label for="yearly_price" class="block text-sm font-medium text-gray-700">{{ __('Yearly Price') }}</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" id="yearly_price" name="yearly_price" step="0.01" 
                                        class="block w-full rounded-md border-gray-300 pl-7 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" 
                                        value="{{ old('yearly_price') }}" required />
                                </div>
                            </div>

                            <!-- Contract Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                <select id="status" name="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-span-full">
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                            <textarea id="description" name="description" rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="window.history.back()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                {{ __('Create Contract') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
