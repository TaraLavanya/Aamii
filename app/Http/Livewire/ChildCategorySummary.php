<?php

namespace App\Http\Livewire;

use App\Models\ChildCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class ChildCategorySummary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $childCategoryId;

    protected $listeners = [
        'deleteChildCategory' => 'deleteChildCategoryById',
    ];

    public function changePageValue($perPageValue)
    {
        $this->perPage = $perPageValue;
        $this->resetPage(pageName: 'p');
    }

    public function deleteChildCategoryById($childCategoryId)
    {
        if ($childCategoryId) {
            try {
                ChildCategory::find($childCategoryId)->delete();
                $childCategoryId = null;
                session()->flash('success', 'child category deleted successfully.');
                return redirect()->route('child-category');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
                return redirect()->route('child-category');
            }
        }
    }

    public function mount(Request $request)
    {
        $this->childCategoryId = $request->childCategoryId;
    }
    public function render()
    {
        $childCategories =  ChildCategory::orderBy('name')->paginate($this->perPage);
        return view('livewire.child-category-summary', [
            "childCategories" => $childCategories
        ])->layout('layouts.admin');
    }
}
