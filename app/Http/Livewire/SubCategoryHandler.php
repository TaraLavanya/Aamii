<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\SubCategory;
use Exception;
use Livewire\Component;

class SubCategoryHandler extends Component
{
    public $subCategory = [
        'name' => '',
        'categor_id' => '',
        'is_active' => true,

    ];
    public $subCategoryId;
    public $perPage = 10;

    protected $rules = [
        'subCategory.name' => 'required',
        'subCategory.category_id' => 'required',
    ];

    protected $messages = [
        'subCategory.name.required' => 'This Field is Required',
        'subCategory.category_id.required' => 'This Field is Required',
    ];

    public function mount($subCategoryId)
    {
        if ($subCategoryId) {
            $subCategory = SubCategory::find($subCategoryId);
            if ($subCategory) {
                $this->subCategory = $subCategory->toArray();
                $this->subCategory['is_active'] = $this->subCategory['is_active'] === 1;
            } else {
                return redirect()->back()->with('warning', 'subCategory  not found');
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
        $subCategoryExists = SubCategory::where('name', $this->subCategory['name'])
            ->exists();
        if ($subCategoryExists) {
            $this->addError('subCategory.name', 'Name already exists');
            return;
        }
        try {
            $subCategory = SubCategory::create($this->subCategory);
            if ($subCategory) {
                session()->flash('success', 'Sub Category created successfully');
                return redirect()->route('sub-category');
            } else {
                session()->flash('error', 'Failed to create Sub Category');
                return redirect()->route('sub-category');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('sub-category');
        }
    }

    public function update()
    {
        $this->validate();
        $subCategoryExists = SubCategory::where('id', '!=', $this->subCategoryId)
            ->where('name', $this->subCategory['name'])->exists();
        if ($subCategoryExists) {
            $this->addError('subCategory.name', 'Name already exists');
            return;
        }
        try {
            $subCategory = SubCategory::find($this->subCategoryId);
            if ($subCategory) {
                $subCategory->update($this->subCategory);
                session()->flash('success', 'subCategory updated successfully');
                return redirect()->route('sub-category');
            } else {
                session()->flash('error', 'subCategory not found');
                return redirect()->route('sub-category');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('sub-category');
        }
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();
        return view('livewire.sub-category-handler', [
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}
