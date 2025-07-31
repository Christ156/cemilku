<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;

class Decoration extends Model
{
    // 3

    use SoftDeletes;

    protected $fillable = ['name', 'price', 'stock', 'image'];

    protected static function booted()
    {
        // When a decoration is created
        static::created(function (Decoration $decoration) {
            $causer = Auth::user(); // Get the currently authenticated user

            // Customizing the description
            $description = "Decoration {$decoration->name} with ID {$decoration->id} has been created by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($decoration) // The decoration that was just created
                ->causedBy($causer)        // The user who created it
                ->withProperties([
                    'decoration_id' => $decoration->id,
                    'decoration_name' => $decoration->name,
                    'decoration_price' => $decoration->price,
                    'decoration_stock' => $decoration->stock,
                    'decoration_image' => $decoration->image,
                ])
                ->event('created') // Explicitly set event to 'created'
                ->log($description); // Custom description
        });

        // When a decoration is updated
        static::updated(function (Decoration $decoration) {
            $causer = Auth::user();

            $properties = [
                'decoration_id' => $decoration->id,
                'decoration_name' => $decoration->name,
                'decoration_price' => $decoration->price,
                'decoration_stock' => $decoration->stock,
                'decoration_image' => $decoration->image
            ];

            $changes = $decoration->getChanges();
            $original = $decoration->getOriginal();

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
            $description = "Decoration {$decoration->name} with ID {$decoration->id} has been updated by {$causer->name} with ID {$causer->id}. Changes = {$changeDetails}.";

            // Add old and new values to properties for more detailed auditing
            if (!empty($changes)) {
                $properties['old'] = $original;
                $properties['new'] = $changes;
            }

            activity()
                ->performedOn($decoration)
                ->causedBy($causer)
                ->withProperties($properties)
                ->event('updated') // Explicitly set event to 'updated'
                ->log($description); // Custom description
        });

        // When a decoration is soft deleted
        static::deleted(function (Decoration $decoration) {
            $causer = Auth::user();

            // Customizing the description for soft delete
            $description = "Decoration {$decoration->name} with ID {$decoration->id} has been soft deleted at {$decoration->deleted_at->toDateTimeString()} by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($decoration)
                ->causedBy($causer)
                ->withProperties([
                    'decoration_id' => $decoration->id,
                    'decoration_name' => $decoration->name,
                    'decoration_price' => $decoration->price,
                    'decoration_stock' => $decoration->stock,
                    'decoration_image' => $decoration->image,
                    'deleted_at' => $decoration->deleted_at->toDateTimeString(), // Tanggal soft delete
                ])
                ->event('soft_deleted') // Explicitly set event to 'soft_deleted'
                ->log($description); // Custom description
        });

        // When a decoration is permanently deleted (force deleted)
        static::forceDeleted(function (Decoration $decoration) {
            $causer = Auth::user();

            // Customizing the description for permanent delete
            $description = "Decoration {$decoration->name} with ID {$decoration->id} has been permanent deleted by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($decoration)
                ->causedBy($causer)
                ->withProperties([
                    'decoration_id' => $decoration->id,
                    'decoration_name' => $decoration->name,
                    'decoration_price' => $decoration->price,
                    'decoration_stock' => $decoration->stock,
                    'decoration_image' => $decoration->image,
                ])
                ->event('permanently_deleted') // Explicitly set event to 'permanently_deleted'
                ->log($description); // Custom description
        });

        // When a decoration is restored
        static::restored(function (Decoration $decoration) {
            $causer = Auth::user();

            // Customizing the description for restore
            $description = "Decoration {$decoration->name} with ID {$decoration->id} has been restored by {$causer->name} with ID {$causer->id}.";

            activity()
                ->performedOn($decoration)
                ->causedBy($causer)
                ->withProperties([
                    'decoration_id' => $decoration->id,
                    'decoration_name' => $decoration->name,
                    'decoration_price' => $decoration->price,
                    'decoration_stock' => $decoration->stock,
                    'decoration_image' => $decoration->image,
                ])
                ->event('restored') // Explicitly set event to 'restored'
                ->log($description); // Custom description
        });
    }
}
