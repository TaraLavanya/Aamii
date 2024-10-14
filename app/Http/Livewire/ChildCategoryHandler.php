<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use Exception;
use Livewire\Component;

class ChildCategoryHandler extends Component
{
    public $childCategory = [
        'name' => '',
        'category_id' => '',
        'subcategory_id' => '',
        'is_active' => true,

    ];
    public $childCategoryId;
    public $perPage = 10;

    protected $rules = [
        'childCategory.name' => 'required',
        'childCategory.category_id' => 'required',
        'childCategory.subcategory_id' => 'required',
    ];

    protected $messages = [
        'childCategory.name.required' => 'This Field is Required',
        'childCategory.category_id.required' => 'This Field is Required',
        'childCategory.subcategory_id.required' => 'This Field is Required',
    ];

    public function mount($childCategoryId)
    {
        if ($childCategoryId) {
            $childCategory = ChildCategory::find($childCategoryId);
            if ($childCategory) {
                $this->childCategory = $childCategory->toArray();
                $this->childCategory['is_active'] = $this->childCategory['is_active'] === 1;
            } else {
                return redirect()->back()->with('warning', 'childCategory  not found');
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
        $childCategoryExists = ChildCategory::where('name', $this->childCategory['name'])
            ->where('category_id', $this->childCategory['category_id'])
            ->where('subcategory_id', $this->childCategory['subcategory_id'])
            ->exists();
        if ($childCategoryExists) {
            $this->addError('childCategory.name', 'Name already exists');
            return;
        }
        try {
            $childCategory = ChildCategory::create($this->childCategory);
            if ($childCategory) {
                session()->flash('success', 'child Category created successfully');
                return redirect()->route('child-category');
            } else {
                session()->flash('error', 'Failed to create child Category');
                return redirect()->route('child-category');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('child-category');
        }
    }

    public function update()
    {
        $this->validate();
        $childCategoryExists = childCategory::where('id', '!=', $this->childCategoryId)
            ->where('name', $this->childCategory['name'])
            ->where('category_id', $this->childCategory['category_id'])
            ->where('subcategory_id', $this->childCategory['subcategory_id'])
            ->exists();
        if ($childCategoryExists) {
            $this->addError('childCategory.name', 'Name already exists');
            return;
        }
        try {
            $childCategory = ChildCategory::find($this->childCategoryId);
            if ($childCategory) {
                $childCategory->update($this->childCategory);
                session()->flash('success', 'childCategory updated successfully');
                return redirect()->route('child-category');
            } else {
                session()->flash('error', 'childCategory not found');
                return redirect()->route('child-category');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('child-category');
        }
    }

    public function render()
    {
        // dump($this->childCategory['category_id']);
        $categories = Category::orderBy('name')->get();
        $subCategories = SubCategory::where('category_id', $this->childCategory['category_id'])->get();
        return view('livewire.child-category-handler', [
            'categories' => $categories,
            'subCategories' => $subCategories,
        ]);
    }
}
