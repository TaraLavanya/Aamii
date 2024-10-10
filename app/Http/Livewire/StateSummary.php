<?php

namespace App\Http\Livewire;

use App\Models\State;
use Illuminate\Http\Request;
use Livewire\Component;

class StateSummary extends Component
{
    public $page = 10;
    public $stateId;

    public function mount(Request $request)
    {
        $this->stateId = $request->stateId;
    }

    public function render()
    {
        $states = State::orderBy('name')->paginate($this->page);
        return view('livewire.state-summary', [
            'states' => $states,
        ])->layout('layouts.admin');
    }
}
