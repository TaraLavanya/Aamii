<?php

namespace App\Http\Livewire;

use App\Models\Country;
use Illuminate\Http\Request;
use Livewire\Component;

class CountrySummary extends Component
{
    public $page = 10;
    public $countryId;

    public function mount(Request $request)
    {
        $this->countryId = $request->countryId;
    }

    public function render()
    {
        $countries = Country::orderBy('name')->paginate($this->page);
        return view('livewire.country-summary', [
            'countries' => $countries,
        ])->layout('layouts.admin');
    }
}
