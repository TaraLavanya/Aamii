<?php

namespace App\Http\Livewire;

use App\Models\Country;
use Exception;
use Illuminate\Http\Client\Request;
use Livewire\Component;

class CountryHandler extends Component
{
    public $country = [
        'name' => '',
        'is_active' => true,
    ];
    public $countryId;

    public $rules = [
        'country.name' => 'required',
    ];

    public $messages = [
        'country.name' => 'This field is required',
    ];


    public function resetFields()
    {
        $this->reset();
    }

    public function create()
    {
        $this->validate();

        $countryExists = Country::where('name', $this->country['name'])->exists();
        if ($countryExists) {
            session()->flash('error', 'Country already exists');
            return;
        }

        try {
            $country = Country::create($this->country);
            if ($country) {
                session()->flash('success', 'Country created successfully');
                return redirect()->route('country');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('country');
        }
    }

    public function update()
    {
        $this->validate();
        $countryExists = Country::where('name', $this->country['name'])
            ->where('id', '!=', $this->countryId)->exists();
        if ($countryExists) {
            session()->flash('error', 'Country already exists');
            return;
        }

        try {
            $country = Country::find($this->countryId);
            if ($country) {
                $country->update($this->country);
                session()->flash('success', 'Country updated successfully');
                return redirect()->route('country');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('country');
        }
    }

    public function mount($countryId)
    {
        if ($countryId) {
            $country = Country::find($countryId);
            if ($country) {
                $this->country = $country->toArray();
                $this->country['is_active'] = $this->country['is_active'] === 1;
            } else {
                return redirect()->back()->with('warning', 'Country  not found');
            }
        }
    }

    public function render()
    {
        return view('livewire.country-handler')->layout('layouts.admin');
    }
}
