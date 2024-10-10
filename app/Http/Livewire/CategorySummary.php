<?php

namespace App\Http\Livewire;

use App\Models\Category;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class CategorySummary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;
    public $categoryId;

    protected $listeners = [
        'deletecategory' => 'deleteCategoryById',
    ];

    public function changePageValue($perPageValue)
    {
        $this->perPage = $perPageValue;
        $this->resetPage(pageName: 'p');
    }

    public function deleteCategoryById($categoryId)
    {
        if ($categoryId) {
            try {
                Category::find($categoryId)->delete();
                $categoryId = null;
                session()->flash('success', 'category deleted successfully.');
                return redirect()->route('category');
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
                return redirect()->route('category');
            }
        }
    }

    public function mount(Request $request)
    {
        $this->categoryId = $request->categoryId;
    }

    public function render()
    {
        return view('livewire.category-summary');
    }
}
