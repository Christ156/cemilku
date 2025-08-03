<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;

class Snack extends Model
{
    // 3
    use SoftDeletes;
    // artinya name, price, stock bisa diisi secara massal
    protected $fillable = ['name', 'description', 'price', 'stock', 'image'];

    // relasi ke OrderDetail (satu snack bisa ada di banyak order detail)
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_snack')->withPivot('quantity');
    }

    public function snackByLayer()
    {
        return $this->belongsTo(LayerSnack::class, 'id', 'id_snack');
    }

    protected static function booted()
    {
        // When a snack is created
        static::created(function (Snack $snack) {
            $causer = Auth::user(); // Get the currently authenticated user

            // Customizing the description
            $description = "Snack {$snack->name} with ID {$snack->id} has been created by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($snack) // The snack that was just created
                ->causedBy($causer)   // The user who created it
                ->withProperties([
                    'snack_id' => $snack->id,
                    'snack_name' => $snack->name,
                    'snack_price' => $snack->price,
                    'snack_stock' => $snack->stock,
                    'snack_description' => $snack->description,
                    'snack_image' => $snack->image,
                ])
                ->event('created') // Explicitly set event to 'created'
                ->log($description); // Custom description
        });

        // When a snack is updated
        static::updated(function (Snack $snack) {
            $causer = Auth::user();

            $properties = [
                'snack_id' => $snack->id,
                'snack_name' => $snack->name,
                'snack_price' => $snack->price,
                'snack_stock' => $snack->stock,
                'snack_description' => $snack->description,
                'snack_image' => $snack->image
            ];

            $changes = $snack->getChanges();
            $original = $snack->getOriginal();

            $changeMessages = [];
            foreach ($changes as $attribute => $newValue) {
                if ($attribute === 'updated_at') {
                    continue;
                }
                $oldValue = $original[$attribute] ?? 'null';
                // Handle potential sensitive data or complex types for display
                if (is_array($oldValue) || is_object($oldValue)) {
                    $oldValue = json_encode($oldValue);
                }
                if (is_array($newValue) || is_object($newValue)) {
                    $newValue = json_encode($newValue);
                }
                $changeMessages[] = "{$attribute} from '{$oldValue}' to '{$newValue}'";
            }

            $changeDetails = implode(', ', $changeMessages);

            // Customizing the description for update
            $description = "Snack {$snack->name} with ID {$snack->id} has been updated by {$causer->name} with ID {$causer->id}. Changes = {$changeDetails}.";

            // Add old and new values to properties for more detailed auditing
            if (!empty($changes)) {
                $properties['old'] = $original;
                $properties['new'] = $changes;
            }

            activity()
                ->performedOn($snack)
                ->causedBy($causer)
                ->withProperties($properties)
                ->event('updated') // Explicitly set event to 'updated'
                ->log($description); // Custom description
        });

        // When a snack is soft deleted
        static::deleted(function (Snack $snack) {
            $causer = Auth::user();

            // Customizing the description for soft delete
            $description = "Snack {$snack->name} with ID {$snack->id} has been soft deleted at {$snack->deleted_at->toDateTimeString()} by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($snack)
                ->causedBy($causer)
                ->withProperties([
                    'snack_id' => $snack->id,
                    'snack_name' => $snack->name,
                    'snack_price' => $snack->price,
                    'snack_stock' => $snack->stock,
                    'snack_description' => $snack->description,
                    'snack_image' => $snack->image,
                    'deleted_at' => $snack->deleted_at->toDateTimeString(), // Tanggal soft delete
                ])
                ->event('soft_deleted') // Explicitly set event to 'soft_deleted'
                ->log($description); // Custom description
        });

        // When a snack is permanently deleted (force deleted)
        static::forceDeleted(function (Snack $snack) {
            $causer = Auth::user();

            // Customizing the description for permanent delete
            $description = "Snack {$snack->name} with ID {$snack->id} has been permanent deleted by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($snack)
                ->causedBy($causer)
                ->withProperties([
                    'snack_id' => $snack->id,
                    'snack_name' => $snack->name,
                    'snack_price' => $snack->price,
                    'snack_stock' => $snack->stock,
                    'snack_description' => $snack->description,
                    'snack_image' => $snack->image,
                ])
                ->event('permanently_deleted') // Explicitly set event to 'permanently_deleted'
                ->log($description); // Custom description
        });

        // When a snack is restored
        static::restored(function (Snack $snack) {
            $causer = Auth::user();

            // Customizing the description for restore
            $description = "Snack {$snack->name} with ID {$snack->id} has been restored by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($snack)
                ->causedBy($causer)
                ->withProperties([
                    'snack_id' => $snack->id,
                    'snack_name' => $snack->name,
                    'snack_price' => $snack->price,
                    'snack_stock' => $snack->stock,
                    'snack_description' => $snack->description,
                    'snack_image' => $snack->image,
                ])
                ->event('restored') // Explicitly set event to 'restored'
                ->log($description); // Custom description
        });
    }
}
