<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Offer;


class NotificationController extends Controller
{

    public function sendNotification()
    {
        $url = "https://uniq-rho.vercel.app/send-notification";

        // Fetch the latest offer
        $latestOffer = Offer::latest()->first();

        if ($latestOffer) {
            // Prepare the offer data for the notification body
            $offerData = [
                'id' => $latestOffer->id,
                'title' => $latestOffer->category_name,  // Adjust fields based on your `Offer` model
                'description' => $latestOffer->details,
            ];

            // Convert the offer data to a suitable format for the payload
            $payload = [
                "title" => "New Offer Available",
                "body" => $latestOffer->category_name,
                "data" =>[
                    "key1"=> "offers"
                ], // You can customize this field
            ];

            // Send the notification via HTTP POST request
            try {
                $response = Http::post($url, $payload);

                if ($response->successful()) {
                    return response()->json([
                        "message" => "Notification Sent",
                        "response" => $response->json(),
                    ]);
                } else {
                    return response()->json([
                        "message" => "Error Sending Notification",
                        "error" => $response->body(),
                    ], $response->status());
                }
            } catch (\Exception $e) {
                return response()->json([
                    "message" => "Error Sending Notification",
                    "error" => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                "message" => "No Offers Found",
            ], 404);
        }
    }


    
}
