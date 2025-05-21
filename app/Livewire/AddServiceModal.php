<?php

namespace App\Livewire;

use App\Models\Service;
use App\Models\Contract;
use App\Models\ContractService;
use Livewire\Component;

class AddServiceModal extends Component
{
    public $isOpen = false;
    public $contractId;
    public $selectedService;
    public $frequency = '';
    public $services;

    protected $rules = [
        'selectedService' => 'required',
        'frequency' => 'required|in:monthly,yearly,quarterly,daily,biannually',
    ];

    protected $listeners = ['openModal'];

    public function mount()
    {
        $this->services = Service::all();
    }

    public function openModal($contractId)
    {
        $this->contractId = $contractId;
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function addService()
    {
        $this->validate();
        
        ContractService::create([
            'contract_id' => $this->contractId,
            'service_id' => $this->selectedService,
            'frequency' => $this->frequency
        ]);

        $this->close();
        $this->dispatch('serviceAdded');
    }

    public function render()
    {
        return view('livewire.add-service-modal', [
            'services' => Service::all(),
        ]);
    }
} 