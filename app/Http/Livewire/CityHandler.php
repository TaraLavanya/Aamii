<?php

namespace App\Http\Livewire;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Exception;
use Livewire\Component;

class CityHandler extends Component
{
    public $city = [
        'name' => '',
        'is_active' => true,
        'country_id' => '',
        'state_id' => '',
    ];
    public $cityId;
    public $perPage = 10;

    protected $rules = [
        'city.name' => 'required',
        'city.country_id' => 'required',
        'city.state_id' => 'required',
    ];

    protected $messages = [
        'city.name.required' => 'This Field is Required',
        'city.country_id.required' => 'This Field is Required',
        'city.state_id.required' => 'This Field is Required',
    ];

    public function mount($cityId)
    {
        if ($cityId) {
            $city = City::find($cityId);
            if ($city) {
                $this->city = $city->toArray();
                $this->city['is_active'] = $this->city['is_active'] === 1;
            } else {
                return redirect()->back()->with('warning', 'city  not found');
            }
        }
    }

    public function resetFields()
    {
        $this->reset();
    }

    public function create()
    {
        $this->validate();
        $cityExists = City::where('name', $this->city['name'])
            ->where('state_id', $this->city['state_id'])
            ->where('country_id', $this->city['country_id'])->exists();
        if ($cityExists) {
            session()->flash('error', 'City already exists');
            return;
        }
        try {
            $city = City::create($this->city);
            if ($city) {
                session()->flash('success', 'City created successfully');
                return redirect()->route('city');
            } else {
                session()->flash('error', 'Failed to create city');
                return redirect()->route('city');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('city');
        }
    }

    public function update()
    {
        $this->validate();
        $cityExists = City::where('id', '!=', $this->cityId)
            ->where('name', $this->city['name'])
            ->where('state_id', $this->city['state_id'])
            ->where('country_id', $this->city['country_id'])->exists();;
        if ($cityExists) {
            session()->flash('error', 'City already exists');
            return;
        }
        try {
            $city = City::find($this->cityId);
            if ($city) {
                $city->update($this->city);
                session()->flash('success', 'City updated successfully');
                return redirect()->route('city');
            } else {
                session()->flash('error', 'City not found');
                return redirect()->route('city');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('city');
        }
    }
    public function render()
    {
        $countries = Country::orderBy('name')->get();
        $states = State::where('country_id', $this->city['country_id'])->orderBy('name')->get();
        // dump($this->city['country_id']);
        return view('livewire.city-handler', [
            'countries' => $countries,
            'states' => $states,
        ])->layout('layouts.admin');
    }
}
