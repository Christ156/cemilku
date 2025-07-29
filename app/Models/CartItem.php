<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CartItem extends Model
{
    use HasFactory, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions   ::defaults()
            ->logFillable() // Atau logOnly(['name', 'email'])
            ->dontSubmitEmptyLogs(); // Hindari log kosong jika tidak ada perubahan
    }
}
