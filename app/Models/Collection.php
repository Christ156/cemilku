<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'price',
        'stock',
        'image',
        'snack_id_1',
        'snack_id_2',
        'snack_id_3',
        'snack_id_4',
        'layer'
    ];

    public function snacks()
    {
        return $this->belongsToMany(Snack::class, 'collection_snack')->withPivot('quantity');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function snack1()
    {
        return $this->belongsTo(Snack::class, 'snack_id_1');
    }
    public function snack2()
    {
        return $this->belongsTo(Snack::class, 'snack_id_2');
    }
    public function snack3()
    {
        return $this->belongsTo(Snack::class, 'snack_id_3');
    }
    public function snack4()
    {
        return $this->belongsTo(Snack::class, 'snack_id_4');
    }
}
