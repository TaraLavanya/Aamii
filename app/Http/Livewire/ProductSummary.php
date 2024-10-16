<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ProductSummary extends Component
{
    public function rsender()
    {
        return view('livewire.product-summary')->layout('layouts.admin');
    }
}
