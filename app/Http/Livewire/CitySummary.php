<?php

namespace App\Http\Livewire;

use App\Models\City;
use Illuminate\Http\Request;
use Livewire\Component;

class CitySummary extends Component
{
    public $perPage = 10;
    public $cityId;

    public function mount(Request $request)
    {
        $this->cityId = $request->cityId;
    }

    public function render()
    {
        $cities = City::orderBy('name')->paginate($this->perPage);
        return view('livewire.city-summary', [
            'cities' => $cities,
        ])->layout('layouts.admin');
    }
}
