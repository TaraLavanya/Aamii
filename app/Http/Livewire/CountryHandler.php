<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CountryHandler extends Component
{
    public $country = [
        'name',
        'is_active' => true,
    ];
    public $countryId;

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
        return view('livewire.country-handler')->layout('layouts.admin');
    }
}
