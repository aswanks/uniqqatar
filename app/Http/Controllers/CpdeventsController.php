<?php

namespace App\Http\Controllers;


use App\Models\Cpd;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Http;

use Request;
use Str;

class CpdeventsController extends Controller
{
    # Function to List all CPD Events - 23/11/2023
    public function cpdeventsControllerIndex()
    {
        $Cpd = Cpd::latest('id')
            ->select('id', 'title', 'slug','eventlink', 'image', 'status', 'created_at', 'updated_at')
            ->paginate(15);
        return view('backend/cpdevents/index',compact('Cpd'));
    }
    # Function to CPD Events Search - 23/11/2023
    public function cpdeventsControllerSearch()
    {
        $key       = request()->get('search');

        if (!$key) {
            return redirect()
                ->route('cpdevents.index')
                ->with('error', 'Please enter a Keyword or Date');
        }

        $Cpd = Cpd::latest('id')
            ->where(function ($query) use ($key) {

                if ($key) {
                    $query->where('cpd_events.id', 'like', "%{$key}%")
                        ->orWhere('cpd_events.title', 'like', "%{$key}%");
                }
            })
            ->paginate(15)
            ->appends(request()->query());

        return view('backend.cpdevents.index', compact('Cpd'));

    }
    # Function to create page view - 23/11/2023
    public function cpdeventsControllerCreate()
    {
        return view('backend.cpdevents.add');
    }
    # Function to Store Cpd event - 23/11/2023
    public function cpdeventsControllerStore()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'image' => 'required|image|mimes:png,jpeg,jpg',
            'eventlink' => 'required',
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        } else {
        if (Request::hasFile('image')) {
            $extension_img = Request::file('image')->getClientOriginalExtension();
            if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
                $orginal_img_name = Request::file('image')->getClientOriginalName();
                $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
                //$fullPath_img = '/public/uploads/images/events' . $imgName;
                Request::file('image')->move(base_path() . '/public/assets/themes/frondend/images/expo2023', $imgName);


                $cpd = new Cpd;
                    $cpd->title = request()->title;
                    $cpd->slug =Str::slug(request()->title);
                    $cpd->eventlink = request()->eventlink;
                    $cpd->image = $imgName;
                    $cpd->status=1;
                    $cpd->save();
                    $this->sendNotification($cpd);
                    return Redirect::route('cpdevents.index')->with('success', 'CPD Events Added Successfully');

            }
         }else {
            return Redirect::back()->with('error', 'Please Upload an Image');
        }
        }
    }
    # Function to Send Notification - 23/11/2023
    private function sendNotification()
    {
        $url = "https://uniq-rho.vercel.app/send-notification";

        // Fetch the latest offer
        $cpd = Cpd::latest()->first();

        if ($cpd) {
            // Prepare the offer data for the notification body
            $infoData = [
                'title' => $cpd->title,
                'link' => $cpd->eventlink,  // Adjust fields based on your `Offer` model
            ];

            // Convert the offer data to a suitable format for the payload
            $payload = [
                "title" => "New CPD Events Available",
                "body" => $cpd->title,
                "data" =>[
                    "key1"=> "cpd",
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
    # Function to Edit Cpd Events - 24/11/2023
    public function cpdeventsControllerEdit($id)
    {
         $cpd = Cpd::findOrFail($id);
         if ($cpd) {
             return view('backend/cpdevents/edit', compact('cpd'));
         } else {
             return Redirect::route('cpdevents.index')->with('error', 'Invalid Request');
         }
    }
    # Function to Edit Cpd Event Delete - 24/11/2023
    public function cpdeventsControllerDelete($id)
    {
        $cpd = Cpd::findOrFail($id);
        if ($cpd) {
            $path_cpd = '/public/assets/themes/frondend/images/expo2023/' . $cpd->image;
            @unlink($path_cpd);
            Cpd::destroy($id);
            return Redirect::route('cpdevents.index')->with('success', 'Event Deleted Successfully');
        } else {
            return Redirect::route('cpdevents.index')->with('error', 'Invalid Reqeust');
        }
    }
    # Function to Update Event - 16/06/2021
    // public function cpdeventsControllerUpdate()
    // {
    //     $validate = Validator::make(request()->all(), [
    //         'title' => 'required',
    //         'image' => 'image|mimes:png,jpg,jpeg',
    //     ]);

    //     if ($validate->fails()) {
    //         return Redirect::back()->with('error', $validate->errors());
    //     } else {
    //         if (Request::hasFile('image')) {
    //             $extension_img = Request::file('image')->getClientOriginalExtension();
    //             if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
    //                 $orginal_img_name = Request::file('image')->getClientOriginalName();
    //                 $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
    //                 //$fullPath_img = '/public/uploads/images/events' . $imgName;
    //                 Request::file('image')->move(base_path() . '/public/assets/themes/frondend/images/expo2023', $imgName);

    //                 $id = request()->id;
    //                 $title = request()->title;
    //                 $slug =Str::slug(request()->title);

    //                 $eventlink = request()->eventlink;

    //                 $image = $imgName;


    //                 $status = 1;
    //                 Cpd::where('id', $id)->update([
    //                     'title' => $title,
    //                     'slug'  =>$slug,
    //                     'image' => $image,
    //                     'eventlink' => $eventlink,
    //                     ]);
    //                 return Redirect::route('cpdevents.index')->with('success', 'Event Updated Successfully');

    //             } else {
    //                 return Redirect::back()->with('error', 'Please Upload Image');
    //             }
    //         }else {

    //             $id = request()->id;
    //             $title = request()->title;
    //             $slug =Str::slug(request()->title);
    //             $eventlink = request()->eventlink;
    //             $image = request()->image;
    //             dd($image);
    //             $update = Cpd::where('id', $id)->update([
    //                 'title' => $title,
    //                 'slug'  =>$slug,
    //                 'eventlink' => $eventlink,
    //                 'image' => $image,

    //             ]);
    //             return Redirect::route('cpdevents.index')->with('success', 'Event Updated Successfully!');

    //         }

        public function cpdeventsControllerUpdate(Request $request)
        {
            $validate = Validator::make(request()->all(), [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('cpd_events', 'title')->ignore(request()->id),
                ],
                'image' => 'image|mimes:png,jpg,jpeg',
            ]);

            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate)->withInput();
            }

            $id        = request()->id;
            $title     = request()->title;
            $slug      = Str::slug($title);
            $eventlink = request()->eventlink;

            // Get existing record
            $cpd = Cpd::findOrFail($id);

            $data = [
                'title'     => $title,
                'slug'      => $slug,
                'eventlink' => $eventlink,
            ];

            // If image uploaded
            if (Request::hasFile('image')) {
                $extension = request()->file('image')->getClientOriginalExtension();
                $original  = request()->file('image')->getClientOriginalName();

                $imgName = setNewNameForPhoto($original)
                    . '.' . generateRandomString(2, 15)
                    . '.' . $extension;

                request()->file('image')->move(
                    public_path('assets/themes/frondend/images/expo2023'),
                    $imgName
                );

                $data['image'] = $imgName;
            }

            Cpd::where('id', $id)->update($data);

            return redirect()
                ->route('cpdevents.index')
                ->with('success', 'Event Updated Successfully!');
        }

    }
    