<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;

class SSEController extends Controller
{
    public function streamData()
    {
        $response = new StreamedResponse(function() {
//            while (true) {
                // Example: Sending a JSON object with the server time and a message.

            // Attempt to disable PHP's output buffering
//            while (ob_get_level() > 0) {
//                ob_end_clean();
//            }
                $users = \DB::table('users')
                    ->select('id', 'name', 'avatar_id', 'posx', 'posy')
                    ->where('land_id', \request('land_id'))
                    ->where('active_at', '>=', now()->subMinutes(15))
                    ->get();

                $usersArray = [];
                foreach ($users as $user) {
                    $usersArray[$user->id] = [
                        'name' => $user->name,
                        'avatar_id' => $user->avatar_id,
                        'posx' => $user->posx,
                        'posy' => $user->posy,
                    ];
                }




                $data = [
                    'time' => now()->toDateTimeString(),
                    'serverTime' => strtotime(now()),
                    'usersArray' => $usersArray,
                ];

                // Encode the data array to JSON
                echo "data: " . json_encode($data) . "\n\n";

                // Make sure the data gets sent immediately
                ob_flush();
                flush();

                // Sleep for a second to throttle the updates
//                sleep(2);
//            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
