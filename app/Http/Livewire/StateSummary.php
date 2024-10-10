<?php

namespace App\Http\Livewire;

use App\Models\State;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class StateSummary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $stateId;

    protected $listeners = [
        'deletestate' => 'deleteStateById',
    ];

    public function mount(Request $request)
    {
        $this->stateId = $request->stateId;
    }

    public function changePageValue($perPageValue)
    {
        $this->perPage = $perPageValue;
        $this->resetPage(pageName: 'p');
    }

    public function deleteStateById($stateId)
    {
        if ($stateId) {
            try {
                State::find($stateId)->delete();
                $stateId = null;
                session()->flash('success', 'State deleted successfully.');
                return redirect()->route('state');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
                return redirect()->route('state');
            }
        }
    }

    public function render()
    {
        $states = State::orderBy('name')->paginate($this->perPage);
        return view('livewire.state-summary', [
            'states' => $states,
        ])->layout('layouts.admin');
    }
}
