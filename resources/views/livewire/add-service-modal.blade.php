<div>
    @if($isOpen)
        <div class="fixed z-10 modal inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
    
                <!-- Modal panel -->
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Add Service to Contract</h2>
                        
                        <form wire:submit.prevent="addService">
                            <div class="mb-4">
                                <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
                                <select wire:model="selectedService" id="service" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Select a service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->service_name }} </option>
                                    @endforeach
                                </select>
                                @error('selectedService') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="mb-4">
                                <label for="frequency" class="block text-sm font-medium text-gray-700">التكرار</label>
                                <select wire:model="frequency" id="frequency" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">اختر التكرار</option>
                                    <option value="monthly">مرة في الشهر</option>
                                    <option value="yearly">مرة في السنة</option>
                                    <option value="quarterly">كل 3 شهور</option>
                                    <option value="daily">يومياً</option>
                                    <option value="biannually">مرتين في السنة</option>
                                </select>
                                @error('frequency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" wire:click="close" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Service
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
