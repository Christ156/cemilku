<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use SoftDeletes;

protected $fillable = [
    'name', 'type', 'category', 'description', 'price', 'stock', 'image', 'layer'
];


    public function snacks()
    {
        return $this->belongsToMany(Snack::class, 'collection_snacks')->withPivot('quantity');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
