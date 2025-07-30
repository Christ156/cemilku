<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class Snack extends Model
{
    use SoftDeletes;
    use HasFactory;
    // artinya name, price, stock bisa diisi secara massal
    protected $fillable = ['name', 'description' ,'price', 'stock', 'image'];

    // relasi ke OrderDetail (satu snack bisa ada di banyak order detail)
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_snack')->withPivot('quantity');
    }

    public function snackByLayer(){
        return $this->belongsTo(LayerSnack::class, 'id', 'id_snack');
    }
}
