<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;

class Customize extends Model
{
    use SoftDeletes;


    protected $fillable = ['name', 'type', 'price', 'image', 'base_image_path', 'layer'];

    public function snacks() {
        return $this->belongsToMany(Snack::class, 'customize_snacks')->withPivot('quantity');
    }

    public function decorations() {
        return $this->belongsToMany(Decoration::class, 'customize_decorations');
    }

    protected static function booted()
    {
        // When a customize item is created
        static::created(function (Customize $customize) {
            $causer = Auth::user(); // Get the currently authenticated user

            // Customizing the description
            $description = "Customize item {$customize->name} with ID {$customize->id} has been created.";

            activity()
                ->performedOn($customize) // The customize item that was just created
                ->causedBy($causer)       // The user who created it
                ->withProperties([
                    'customize_id' => $customize->id,
                    'customize_name' => $customize->name,
                    'customize_type' => $customize->type,
                    'customize_price' => $customize->price,
                    'customize_image' => $customize->image,
                    'customize_layer' => $customize->layer,
                ])
                ->event('created') // Explicitly set event to 'created'
                ->log($description); // Custom description
        });

        // When a customize item is soft deleted
        static::deleted(function (Customize $customize) {
            $causer = Auth::user();

            // Customizing the description for soft delete
            $description = "Customize item {$customize->name} with ID {$customize->id} has been deleted.";

            activity()
                ->performedOn($customize)
                ->causedBy($causer)
                ->withProperties([
                    'customize_id' => $customize->id,
                    'customize_name' => $customize->name,
                    'customize_type' => $customize->type,
                    'customize_price' => $customize->price,
                    'customize_image' => $customize->image,
                    'customize_layer' => $customize->layer,
                ])
                ->event('deleted') // Explicitly set event to 'soft_deleted'
                ->log($description); // Custom description
        });
    }

}
