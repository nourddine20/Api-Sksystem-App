<?php 

namespace App\Listeners;

use Illuminate\Auth\Events\TokenExpired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;

class InvalidateToken implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  TokenExpired  $event
     * @return void
     */
    public function handle(TokenExpired $event)
    {
        // Get the user associated with the expired token
        $user = User::where('id', $event->tokenId)->first();

        // Update the user's access_api column to 0
        if ($user) {
            $user->update(['customers_api' => 0,'customers_api_key' => NULL]);
        }
    }
}