<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image_path',
        'stock_qty',
        'item_qty',
        'price',
        'category_id',
        'sub_category_id',
        'child_category_id',
        'short_description',
        'more_info',
    ];

    protected $cast = [
        'more_info' => 'json',
        'image_path' => 'json',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class, 'child_category_id');
    }
}
