<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use Livewire\Component;

class ProductHandler extends Component
{
    public $category_id;
    public $sub_category_id;
    public $child_category_id;
    public function render()
    {
        $categories = Category::orderBy('name')->where('is_active', true)->get();

        $sub_categories = SubCategory::where('category_id', $this->category_id)
            ->orderBy('name')
            ->where('is_active', true)->get();

        $child_categories = ChildCategory::where('category_id', $this->category_id)
            ->where('subcategory_id', $this->sub_category_id)
            ->orderBy('name')
            ->where('is_active', true)
            ->get();

        return view('livewire.product-handler', [
            'categories' => $categories,
            'sub_categories' => $sub_categories,
            'child_categories' => $child_categories,
        ])->layout('layouts.admin');
    }
}
