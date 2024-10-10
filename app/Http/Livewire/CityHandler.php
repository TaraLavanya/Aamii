<?php

namespace App\Http\Livewire;

use App\Models\Country;
use App\Models\State;
use Livewire\Component;

class CityHandler extends Component
{
    public $city = [
        'name',
        'is_active' => true,
        'country_id',
        'state_id',
    ];
    public $cityId;
    public $perPage = 10;

    public function resetFields()
    {
        $this->reset();
    }

    public function create()
    {
        dd($this->country);
    }

    public function update($id)
    {
        dd($this->country);
    }
    public function render()
    {
        $countries = Country::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        return view('livewire.city-handler', [
            'countries' => $countries,
            'states' => $states,
        ])->layout('layouts.admin');
    }
}
