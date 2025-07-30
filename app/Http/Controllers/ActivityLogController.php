<?php

namespace App\Http\Controllers;

use App\Models\User; // Assuming you have a User model
use Spatie\Activitylog\Models\Activity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function showUserLogs($userId)
    {
        // Find the user
        $user = User::findOrFail($userId);

        // Get activity logs for the specific user
        // You might need to adjust the 'causer_id' or 'subject_id' depending on how you're logging
        // For user-initiated actions, 'causer_id' is typically the user's ID.
        // If the user is the 'subject' of an action (e.g., user profile updated), 'subject_id' might be the user's ID.
        $userActivities = Activity::where('causer_id', $userId)
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(20); // Paginate the results

        return view('admin.user.index', compact('user', 'userActivities'));
    }
}
