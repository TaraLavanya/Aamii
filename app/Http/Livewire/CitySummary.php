<?php

namespace App\Http\Livewire;

use App\Models\City;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class CitySummary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $cityId;

    protected $listeners = [
        'deletecity' => 'deleteCityById',
    ];

    public function changePageValue($perPageValue)
    {
        $this->perPage = $perPageValue;
        $this->resetPage(pageName: 'p');
    }

    public function deleteCityById($cityId)
    {
        if ($cityId) {
            try {
                City::find($cityId)->delete();
                $cityId = null;
                session()->flash('success', 'City deleted successfully.');
                return redirect()->route('city');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
                return redirect()->route('city');
            }
        }
    }

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
