<?php

namespace App\Http\Livewire;

use App\Models\Country;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class CountrySummary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $countryId;
    protected $listeners = [
        'deletecountry' => 'deletecountryById',
    ];

    public function mount(Request $request)
    {
        $this->countryId = $request->countryId;
    }

    public function changePageValue($perPageValue)
    {
        $this->perPage = $perPageValue;
        $this->resetPage(pageName: 'p');
    }

    public function deletecountryById($countryId)
    {
        if ($countryId) {

            try {
                Country::find($countryId)->delete();
                $countryId = null;
                session()->flash('success', 'Country deleted successfully.');
                return redirect()->route('country');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
                return;
            }
        }
    }


    public function render()
    {
        $countries = Country::orderBy('name')->paginate($this->perPage);
        return view('livewire.country-summary', [
            'countries' => $countries,
        ])->layout('layouts.admin');
    }
}
