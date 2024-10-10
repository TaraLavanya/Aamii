<?php

namespace App\Http\Livewire;

use App\Models\Category;
use Exception;
use Livewire\Component;

class CategoryHandler extends Component
{
    public $category = [
        'name' => '',
        'is_active' => true,

    ];
    public $categoryId;
    public $perPage = 10;

    protected $rules = [
        'category.name' => 'required',
    ];

    protected $messages = [
        'category.name.required' => 'This Field is Required',
    ];

    public function mount($categoryId)
    {
        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $this->category = $category->toArray();
                $this->category['is_active'] = $this->category['is_active'] === 1;
            } else {
                return redirect()->back()->with('warning', 'category  not found');
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
        $categoryExists = Category::where('name', $this->category['name'])
            ->exists();
        if ($categoryExists) {
            session()->flash('error', 'category already exists');
            return;
        }
        try {
            $category = Category::create($this->category);
            if ($category) {
                session()->flash('success', 'category created successfully');
                return redirect()->route('category');
            } else {
                session()->flash('error', 'Failed to create category');
                return redirect()->route('category');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('category');
        }
    }

    public function update()
    {
        $this->validate();
        $categoryExists = Category::where('id', '!=', $this->categoryId)
            ->where('name', $this->category['name'])->exists();
        if ($categoryExists) {
            session()->flash('error', 'category already exists');
            return;
        }
        try {
            $category = Category::find($this->categoryId);
            if ($category) {
                $category->update($this->category);
                session()->flash('success', 'category updated successfully');
                return redirect()->route('category');
            } else {
                session()->flash('error', 'category not found');
                return redirect()->route('category');
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('category');
        }
    }

    public function render()
    {
        return view('livewire.category-handler')->layout('layouts.admin');
    }
}
