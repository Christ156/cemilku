<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'collection_id', 'customize_id', 'mysterybox_id', 'quantity', 'price', 'total_price'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function customize()
    {
        return $this->belongsTo(Customize::class);
    }

    public function mysteryBox()
    {
        return $this->belongsTo(MysteryBox::class, 'mysterybox_id');
    }

}
