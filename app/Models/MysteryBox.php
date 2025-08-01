<?php
namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class MysteryBox extends Model
{

    // 3
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'budget',
        'mood',
        'stock',
    ];

    /**
     * The snacks that belong to the MysteryBox.
     * Many-to-many relationship with the Snack model.
     */
    public function snacks()
    {
        return $this->belongsToMany(Snack::class, 'mystery_box_snack', 'mystery_box_id', 'snack_id')
            ->withPivot('quantity');
    }

    protected static function booted()
    {
        // When a mystery box is created
        static::created(function (MysteryBox $mysteryBox) {
            if (Auth::check()) {
                $causer = Auth::user(); // Get the currently authenticated user

                // Customizing the description
                $description = "Mystery Box with ID {$mysteryBox->id} with budget {$mysteryBox->budget} and mood {$mysteryBox->mood} has been created by {$causer->name} with ID {$causer->id}.";

                activity()
                    ->performedOn($mysteryBox) // The mystery box that was just created
                    ->causedBy($causer)        // The user who created it
                    ->withProperties([
                        'mystery_box_id' => $mysteryBox->id,
                        'budget'         => $mysteryBox->budget,
                        'mood'           => $mysteryBox->mood,
                        'stock'          => $mysteryBox->stock,
                    ])
                    ->event('created')   // Explicitly set event to 'created'
                    ->log($description); // Custom description
            }
        });

        // When a mystery box is updated
        static::updated(function (MysteryBox $mysteryBox) {
            if (Auth::check()) {
                $causer = Auth::user();

                $properties = [
                    'mystery_box_id' => $mysteryBox->id,
                    'budget'         => $mysteryBox->budget,
                    'mood'           => $mysteryBox->mood,
                    'stock'          => $mysteryBox->stock,
                ];

                $changes  = $mysteryBox->getChanges();
                $original = $mysteryBox->getOriginal();

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
                $description = "Mystery Box with ID {$mysteryBox->id} has been updated by {$causer->name} with ID {$causer->id}. Changes = {$changeDetails}.";

                // Add old and new values to properties for more detailed auditing
                if (! empty($changes)) {
                    $properties['old'] = $original;
                    $properties['new'] = $changes;
                }

                activity()
                    ->performedOn($mysteryBox)
                    ->causedBy($causer)
                    ->withProperties($properties)
                    ->event('updated')   // Explicitly set event to 'updated'
                    ->log($description); // Custom description
            }
        });

        // When a mystery box is soft deleted
        static::deleted(function (MysteryBox $mysteryBox) {
            if (Auth::check()) {
                $causer = Auth::user();

                // Customizing the description for soft delete
                $description = "Mystery Box with ID {$mysteryBox->id} has been soft deleted at {$mysteryBox->deleted_at->toDateTimeString()} by {$causer->name} with ID {$causer->id}.";

                activity()
                    ->performedOn($mysteryBox)
                    ->causedBy($causer)
                    ->withProperties([
                        'mystery_box_id' => $mysteryBox->id,
                        'budget'         => $mysteryBox->budget,
                        'mood'           => $mysteryBox->mood,
                        'stock'          => $mysteryBox->stock,
                        'deleted_at'     => $mysteryBox->deleted_at->toDateTimeString(), // Tanggal soft delete
                    ])
                    ->event('soft_deleted') // Explicitly set event to 'soft_deleted'
                    ->log($description);    // Custom description
            }
        });

        // When a mystery box is permanently deleted (force deleted)
        static::forceDeleted(function (MysteryBox $mysteryBox) {
            if (Auth::check()) {
                $causer = Auth::user();

                // Customizing the description for permanent delete
                $description = "Mystery Box with ID {$mysteryBox->id} has been permanent deleted by {$causer->name} with ID {$causer->id}.";

                activity()
                    ->performedOn($mysteryBox)
                    ->causedBy($causer)
                    ->withProperties([
                        'mystery_box_id' => $mysteryBox->id,
                        'budget'         => $mysteryBox->budget,
                        'mood'           => $mysteryBox->mood,
                        'stock'          => $mysteryBox->stock,
                    ])
                    ->event('permanently_deleted') // Explicitly set event to 'permanently_deleted'
                    ->log($description);           // Custom description
            }
        });

        // When a mystery box is restored
        static::restored(function (MysteryBox $mysteryBox) {
            if (Auth::check()) {
                $causer = Auth::user();

                // Customizing the description for restore
                $description = "Mystery Box with ID {$mysteryBox->id} has been restored by {$causer->name} with ID {$causer->id}.";

                activity()
                    ->performedOn($mysteryBox)
                    ->causedBy($causer)
                    ->withProperties([
                        'mystery_box_id' => $mysteryBox->id,
                        'budget'         => $mysteryBox->budget,
                        'mood'           => $mysteryBox->mood,
                        'stock'          => $mysteryBox->stock,
                    ])
                    ->event('restored')  // Explicitly set event to 'restored'
                    ->log($description); // Custom description
            }
        });
    }
}
