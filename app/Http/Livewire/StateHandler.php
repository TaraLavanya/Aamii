<?php

namespace App\Http\Livewire;

use App\Models\Country;
use Livewire\Component;

class StateHandler extends Component
{
    public $state = [
        'name',
        'country_id',
        'is_active' => true,
    ];
    public $stateId;
    public $perPage = 10;

    public function resetFields()
    {
        $this->reset();
    }

    public function create()
    {
        dd($this->state);
    }

    public function update($id)
    {
        dd($this->state);
    }

    public function render()
    {
        $countries = Country::orderBy('name')->get();
        return view('livewire.state-handler', [
            'countries' => $countries,
        ])->layout('layouts.admin');
    }
}
