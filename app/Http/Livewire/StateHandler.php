<?php

namespace App\Http\Livewire;

use App\Models\Country;
use App\Models\State;
use Exception;
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

    protected $rules = [
        'state.name' => 'required|string',
        'state.country_id' => 'required',
    ];
    protected $messages = [
        'state.name.required' => 'This field is required',
        'state.country_id.required' => 'This field is required',
    ];

    public function mount($stateId)
    {
        if ($stateId) {
            $state = State::find($stateId);
            if ($state) {
                $this->state = $state->toArray();
                $this->state['is_active'] = $this->state['is_active'] === 1;
            } else {
                return redirect()->back()->with('warning', 'state  not found');
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

        $stateExists = State::where('name', $this->state['name'])->where('country_id', $this->state['country_id'])->exists();
        if ($stateExists) {
            $this->addError('state.name', 'State with the same name and country already exists');
            return;
        }
        try {
            $state = State::create($this->state);
            if ($state) {
                session()->flash('success', 'State created successfully');
                return redirect()->route('state');
            } else {
                session()->flash('error', 'Failed to create state');
                return redirect()->route('state');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('state');
        }
    }

    public function update()
    {
        $this->validate();
        $stateExists = State::where('name', $this->state['name'])
            ->where('id', '!=', $this->stateId)->exists();
        if ($stateExists) {
            session()->flash('error', 'state already exists');
            return;
        }

        try {
            $state = State::find($this->stateId);
            if ($state) {
                $state->update($this->state);
                session()->flash('success', 'state updated successfully');
                return redirect()->route('state');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('state');
        }
    }

    public function render()
    {
        $countries = Country::orderBy('name')->get();
        return view('livewire.state-handler', [
            'countries' => $countries,
        ])->layout('layouts.admin');
    }
}
