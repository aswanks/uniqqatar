<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;


class InformationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $informations = Information::all();
       return view('backend.information.index',compact('informations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend/information/add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'message' => 'required',
            'document' => 'nullable|file|mimes:png,jpeg,jpg,pdf|max:2048',
        ]);
        if ($validator->fails()) {
              return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $information = new Information;
            $information->title = request()->title;
            $information->message = request()->message;
            if (request()->hasFile('document')) {
                $image_extension = request()->file('document')->getClientOriginalExtension();
                $image_name = request()->file('document')->getClientOriginalName();
                $image_rename = setNewNameForPhoto($image_name) . '.' . generateRandomString(2, 15) . '.' . $image_extension;
                $upload_path = '/public/uploads/documents' . $image_rename;
                request()->file('document')->move(base_path() . '/public/uploads/documents', $image_rename);
                $information->document = $image_rename;

            }
            $information->status = 1;
            $information->save();
            $this->sendNotification($information);

            return Redirect()->route('information.index')->with('success', 'Information Added Successfully');
        }


    }
    
    private function sendNotification(Information $information)
    {
        $url = "https://uniq-rho.vercel.app/send-notification";
        
        $documentUrl = $information->document
        ? asset('uploads/documents/' . $information->document)
        : null;


        // Fetch the latest offer
        $latestInfo = Information::latest()->first();

        if ($latestInfo) {
            // Prepare the offer data for the notification body
            $infoData = [
                'title' => $latestInfo->title,
                'Message' => $latestInfo->message,  // Adjust fields based on your `Offer` model
            ];

            // Convert the offer data to a suitable format for the payload
            $payload = [
                "title" => "New Message Available",
                "body" => " $latestInfo->title",
                "data" =>[
                    "key1"=> "messages",
                    //"document"  => $documentUrl,
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
                "message" => "No Message Found",
            ], 404);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Information  $information
     * @return \Illuminate\Http\Response
     */
    public function show(Information $information)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Information  $information
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $information = Information::findOrFail($id);
        if ($information) {
            return view('backend/information/edit', compact('information'));
        } else {
            return Redirect::route('information.index')->with('error', 'Invalid Request');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Information  $information
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('informations', 'title')->ignore(request()->id),
            ],
            'document' => 'nullable|file|mimes:png,jpeg,jpg,pdf|max:2048',
        ]);
        if ($validator->fails()) {
             return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
                    $id = $request->input('id');
                    $information = Information::findOrFail($id);
                    $information->title = request()->title;
                    $information->message = request()->message;
                    if (request()->hasFile('document')) {
                        $image_extension = request()->file('document')->getClientOriginalExtension();
                        $image_name = request()->file('document')->getClientOriginalName();
                        $image_rename = setNewNameForPhoto($image_name) . '.' . generateRandomString(2, 15) . '.' . $image_extension;
                        $upload_path = '/public/uploads/documents' . $image_rename;
                        request()->file('document')->move(base_path() . '/public/uploads/documents', $image_rename);
                        $information->document = $image_rename;

                    }
                    $information->status = 1;
                    $information->save();
                    return Redirect()->route('information.index')->with('success', 'Information Updated Successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Information  $information
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $information = Information::findOrFail($id);
        if ($information) {

            Information::destroy($id);
            return Redirect::route('information.index')->with('success', 'Information Deleted Successfully');
        } else {
            return Redirect::route('information.index')->with('error', 'Invalid Reqeust');
        }
    }
}