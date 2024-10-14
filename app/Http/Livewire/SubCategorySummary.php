<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class SubCategorySummary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $subCategoryId;

    protected $listeners = [
        'deleteSubCategory' => 'deleteSubCategoryById',
    ];

    public function changePageValue($perPageValue)
    {
        $this->perPage = $perPageValue;
        $this->resetPage(pageName: 'p');
    }

    public function deleteSubCategoryById($subCategoryId)
    {
        if ($subCategoryId) {
            try {
                SubCategory::find($subCategoryId)->delete();
                $subCategoryId = null;
                session()->flash('success', 'sub category deleted successfully.');
                return redirect()->route('sub-category');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
                return redirect()->route('sub-category');
            }
        }
    }

    public function mount(Request $request)
    {
        $this->subCategoryId = $request->subCategoryId;
    }
    public function render()
    {
        $subCategories = SubCategory::orderBy('name')->paginate($this->perPage);
        return view('livewire.sub-category-summary', [
            'subCategories' => $subCategories,
        ])->layout('layouts.admin');
    }
}
