<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SuspendUserController extends Controller
{
    /**
     * Suspend a user account with a specified duration.
     */
    public function store(Request $request, User $user)
    {
        $admin = auth()->user();
        if (!$admin || $admin->user_type !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'suspension_duration' => ['required', 'integer', 'min:1', 'max:365'],
            'suspension_unit' => ['required', 'in:days,weeks,months'],
            'suspension_reason' => ['nullable', 'string', 'max:500'],
        ]);

        // Calculate expiry date
        $expiryDate = Carbon::now('Asia/Manila');
        $unit = $validated['suspension_unit'];
        $duration = $validated['suspension_duration'];

        if ($unit === 'days') {
            $expiryDate->addDays($duration);
        } elseif ($unit === 'weeks') {
            $expiryDate->addWeeks($duration);
        } elseif ($unit === 'months') {
            $expiryDate->addMonths($duration);
        }

        // Prevent suspending the last active admin
        if (
            $user->user_type === 'admin'
            && User::where('user_type', 'admin')
                ->where('account_status', 'active')
                ->whereKeyNot($user->id)
                ->count() === 0
        ) {
            $message = 'Cannot suspend the last active admin account.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->withErrors(['suspension' => $message]);
        }

        // Update user
        $user->update([
            'account_status' => 'suspended',
            'suspension_expires_at' => $expiryDate,
            'suspension_reason' => $validated['suspension_reason'] ?? null,
        ]);

        $message = "Account suspended until {$expiryDate->format('F d, Y H:i A')} (Asia/Manila).";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'user' => [
                    'id' => $user->id,
                    'account_status' => $user->account_status,
                    'suspension_expires_at' => $expiryDate->toIso8601String(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
