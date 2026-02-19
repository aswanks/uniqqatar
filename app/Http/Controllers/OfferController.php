<?php

namespace App\Http\Controllers;

use Request;
use App\Models\Offer;
use App\Models\Offercategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Str;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;


class OfferController extends Controller
{
    # Function to Offer List page - 24/11/2023
    public function offersControllerIndex()
    {
        $Offers = Offer::latest('id')
            ->select('id', 'category_name','slug', 'image', 'details', 'status','start_date','end_date','created_at', 'updated_at')
            ->paginate(15);
        $categories = Offercategory::where('status',1)->orderBy('id','ASC')->get();
        return view('backend/offers/index',compact('Offers','categories'));
    }
    # Function to Offer create page - 24/11/2023
    public function offersControllerCreate()
    {
        $categories = Offercategory::where('status',1)->orderBy('id','ASC')->get();
        return view('backend.offers.add',compact('categories'));
    }
    # Function to Offer Store - 24/11/2023
    public function offersControllerStore(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'category_name' => 'required',
            'image'         => 'required|image|mimes:png,jpeg,jpg|max:1024',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'details'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $category = Offercategory::where('category_name', request()->category_name)->first();

        if (!$category) {
            return Redirect::back()->with('error', 'Category not found.');
        }

        if (request()->hasFile('image')) {

            $image      = request()->file('image');
            $extension  = $image->getClientOriginalExtension();
            $original   = $image->getClientOriginalName();

            $imgName = setNewNameForPhoto($original) . '_'
                . generateRandomString(2, 15) . '.'
                . $extension;

            $image->move(
                public_path('assets/themes/frondend/images/offers'),
                $imgName
            );

            $offer = new Offer();
            $offer->category_id   = $category->id;
            $offer->category_name = $category->category_name;
            $offer->slug          = Str::slug(request()->category_name);
            $offer->image         = $imgName;
            $offer->start_date    = request()->start_date;
            $offer->end_date      = request()->end_date;
            $offer->details       = request()->details;
            $offer->status        = 1;
            $offer->save();

            $this->sendNotification($offer);

            return Redirect::route('offer.index')
                ->with('success', 'Offer Added Successfully');
        }

        return Redirect::back()->with('error', 'Please upload an image.');
    }
    # Function to SendNotificationMobile - 24/11/2023
    private function sendNotification($offer)
    {
        $url = "https://uniq-rho.vercel.app/send-notification";

        // Fetch the latest offer

        if ($offer) {
            // Prepare the offer data for the notification body
            $offerData = [
                'id' => $offer->id,
                'title' => $offer->category_name,  // Adjust fields based on your `Offer` model
                'description' => $offer->details,
            ];

            // Convert the offer data to a suitable format for the payload
            $payload = [
                "title" => "New Offer Available",
                "body" => $offer->category_name,
                "data" =>[
                    "key1"=> "offers",
                    "category"=>$offer->category_name,
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
    # Function to Edit Cpd Events - 24/11/2023
    public function offersControllerEdit($id)
    {
        $offer = Offer::findOrFail($id);
        $categories = Offercategory::where('status',1)->orderBy('id','ASC')->get();
        if ($offer) {
            return view('backend/offers/edit', compact('offer','categories'));
        } else {
            return Redirect::route('offers.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Offer Delete - 24/11/2023
    public function offersControllerDelete($id)
    {
        $offer = Offer::find($id);
        if ($offer) {
            $path_offer = '/public/assets/themes/frondend/images/offers/' . $offer->image;
            @unlink($path_offer);
            Offer::destroy($id);
            return Redirect::route('offer.index')->with('success', 'Offer Deleted Successfully');
        } else {
            return Redirect::route('offer.index')->with('error', 'Invalid Reqeust');
        }
    }
    # Function to Update Event - 16/06/2021
    public function offersControllerUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'id'            => 'required|exists:offers,id',
            'category_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('offers', 'category_name')->ignore(request()->id),
            ],
            'image'         => 'nullable|image|mimes:png,jpg,jpeg,gif|max:1024',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
            'details'       => 'nullable|string',
            'status'        => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $offer = Offer::find(request()->id);

        $data = [
            'category_name' => request()->category_name,
            'slug'          => Str::slug(request()->category_name),
            'details'       => request()->details,
            'start_date'    => request()->start_date,
            'end_date'      => request()->end_date,
            'status'        => (int) request()->status,
        ];

        if (Request::hasFile('image')) {

            $image      = request()->file('image');
            $extension  = $image->getClientOriginalExtension();
            $imageName  = setNewNameForPhoto(
                                pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
                            ) . '_' . generateRandomString(2, 15) . '.' . $extension;

            $image->move(
                public_path('assets/themes/frondend/images/offers'),
                $imageName
            );

            $data['image'] = $imageName;
        }

        $offer->update($data);

        return Redirect::route('offer.index')
            ->with('success', 'Offer Updated Successfully!');
    }
    # Function to Offer display page - 16/06/2021
    public function offerdisplay()
    {
        $Offers = Offer::latest('id')
                ->select('id', 'category_id','category_name','slug', 'image', 'details', 'status','created_at', 'updated_at')
                ->paginate(15);
                return view('frondend/pages/offerpage',compact('Offers'));

    }
    # Function to Search News - 18/06/2021
    public function offersControllerSearch(Request $request)
    {
        $category_id = request()->category_id;
        $start_date  = request()->start_date;
        $end_date    = request()->end_date;

        $Offers = Offer::with('category') 
            ->latest('id')
            ->when($category_id, function ($query) use ($category_id) {
                $query->where('category_id', $category_id);
            })
            ->when($start_date, function ($query) use ($start_date) {
                $query->whereDate('start_date', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('end_date', '<=', $end_date);
            })
            ->paginate(15);

        if ($Offers->count() == 0) {
            return redirect()
                ->route('offer.index')
                ->with('error', 'No records found');
        }
        $categories = Offercategory::where('status',1)->orderBy('id','ASC')->get();
        return view('backend/offers/index', compact('Offers','categories'));
    }

}