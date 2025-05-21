<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ServiceRequest;

class ServiceRequestStatus extends Component
{
    public $serviceRequest;
    public $showModal = false;
    public $status;
    public $due_price;
    public function mount(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
        $this->status = $serviceRequest->status;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function updateStatus()
    {
        // Validate required fields
        if (empty($this->status)) {
            $this->addError('status', 'Please select a status');
            return;
        }

        // Build update array with only non-null values
        $updateData = ['status' => $this->status];
        
        if (!is_null($this->due_price)) {
            $updateData['due_price'] = $this->due_price;
        }

        $this->serviceRequest->update($updateData);
        $this->showModal = false;
        $this->dispatch('status-updated');
    }

    public function render()
    {
        return view('livewire.service-request-status');
    }
}