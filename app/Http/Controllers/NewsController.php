<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsImage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Request;
use Illuminate\Support\Facades\Http;


class NewsController extends Controller
{
    # Function to List all News - 17/06/2021
    public function newsControllerIndex()
    {
        $News = News::latest('id')
            ->select('id', 'tittle', 'place', 'date', 'image', 'description', 'status', 'created_at', 'updated_at')
            ->paginate(15);
        return view('backend/news/index', compact('News'));
    }
    # Function to Create News - 18/06/2021
    public function newsControllerCreate()
    {
        return view('backend.news.add');
    }
    # Function to Store News - 18/06/2021
    public function newsControllerStore(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'tittle'       => 'required|string|max:255|unique:news,tittle',
            'image_news.*' => 'image|mimes:png,jpg,jpeg|max:2048',
            'image'        => 'image|mimes:png,jpg,jpeg|max:2048',
            'place'        => 'required|string|max:255',
            'date'         => 'required|date',
            'image'        => 'required|image|mimes:png,jpeg,jpg',
            'description'  => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Main Image Upload
        if (Request::hasFile('image')) {

            $image        = request()->file('image');
            $extension    = $image->getClientOriginalExtension();
            $originalName = $image->getClientOriginalName();

            $imageName = setNewNameForPhoto($originalName)
                . '.' . generateRandomString(2, 15)
                . '.' . $extension;

            $image->move(public_path('uploads/news'), $imageName);

            // Save News
            $new = new News();
            $new->tittle      = request()->tittle;
            $new->description = request()->description;
            $new->date        = request()->date;
            $new->place       = request()->place;
            $new->image       = $imageName;
            $new->status      = 1;
            $new->save();
            
            $this->sendNotification($new);
            
            // Multiple Images Upload
            if (Request::hasFile('image_news')) {
                foreach (request()->file('image_news') as $file) {

                    $originalName = $file->getClientOriginalName();
                    $extension    = $file->getClientOriginalExtension();

                    $fileName = setNewNameForPhoto($originalName)
                        . '.' . generateRandomString(2, 15)
                        . '.' . $extension;

                    $file->move(public_path('uploads/news/other_images'), $fileName);

                    NewsImage::create([
                        'news_id'    => $new->id,
                        'image_name' => $fileName,
                    ]);
                }
            }

            return redirect()
                ->route('news.index')
                ->with('success', 'News Added Successfully');
        }

        return redirect()
            ->back()
            ->with('error', 'Please upload an image');
    }
    # Function to SendNotification Mobile 18/06/2021
    private function sendNotification()
    {
        $url = "https://uniq-rho.vercel.app/send-notification";

        // Fetch the latest offer
        $latestNews = News::latest()->first();

        if ($latestNews) {
            // Prepare the offer data for the notification body
            $offerData = [
                'id' => $latestNews->id,
                'title' => $latestNews->tittle,  // Adjust fields based on your `Offer` model
                'description' => $latestNews->description,
            ];

            // Convert the offer data to a suitable format for the payload
            $payload = [
                "title" => "New News Added",
                "body" => " ",
                "data" =>[
                    "key1"=> "news"
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
    # Function to Edit News - 18/06/2021
    public function newsControllerEdit($id)
    {
        $news = News::findOrFail($id);
        if ($news) {
            return view('backend/news/edit', compact('news'));
        } else {
            return Redirect::route('news.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Delete News - 18/06/2021
    public function deleteotherimages($id)
    {
        $othernewsimages = NewsImage::findOrFail($id);
        if ($othernewsimages) {
            $image_others = $othernewsimages->image_name;
            $image_others_path = 'public/uploads/news/other_images/' . $image_others;
            @unlink($image_others_path);
            NewsImage::destroy($id);
            return Redirect::route('news.index')->with('success', 'News image Deleted Successfully');
        } else {
            return Redirect::route('news.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Update News - 18/06/2021
    public function newsControllerUpdate(Request $request)
    {
        $id = request()->id;

        $validator = Validator::make(request()->all(), [
            'tittle'        => 'required|string|max:255|unique:news,tittle,' . $id,
            'image'         => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'image_news.*'  => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'place'         => 'required|string|max:255',
            'date'          => 'required|date',
            'description'   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Fetch news
        $news = News::findOrFail($id);

        // Update basic fields
        $news->tittle      = request()->tittle;
        $news->description = request()->description;
        $news->date        = request()->date;
        $news->place       = request()->place;

        // Main image upload
        if (Request::hasFile('image')) {

            // Delete old image safely
            if ($news->image && file_exists(public_path('uploads/news/' . $news->image))) {
                unlink(public_path('uploads/news/' . $news->image));
            }

            $image = request()->file('image');
            $extension = $image->getClientOriginalExtension();
            $originalName = $image->getClientOriginalName();

            $imageName = setNewNameForPhoto($originalName)
                . '.' . generateRandomString(2, 15)
                . '.' . $extension;

            $image->move(public_path('uploads/news'), $imageName);

            $news->image = $imageName;
        }

        $news->save();

        // Multiple images upload
        if (Request::hasFile('image_news')) {
            foreach (request()->file('image_news') as $file) {

                $originalName = $file->getClientOriginalName();
                $extension    = $file->getClientOriginalExtension();

                $fileName = setNewNameForPhoto($originalName)
                    . '.' . generateRandomString(2, 15)
                    . '.' . $extension;

                $file->move(public_path('uploads/news/other_images'), $fileName);

                NewsImage::create([
                    'news_id'    => $news->id,
                    'image_name' => $fileName,
                ]);
            }
        }

        return redirect()
            ->route('news.index')
            ->with('success', 'News Updated Successfully');
    }
    # Function to Delete News - 18/06/2021
    public function newsControllerDelete($id)
    {
        $news = News::findOrFail($id);
        if ($news) {
            $news_image = $news->image;
            $news_image_path = 'public/uploads/news/' . $news_image;
            @unlink($news_image_path);
            $newsImages=NewsImage::where('news_id','=',$id)->get();
            foreach ($newsImages as $ne) {
                $image_others = $ne->image_name;
                $image_others_path = 'public/uploads/news/other_images/' . $image_others;
                @unlink($image_others_path);
                NewsImage::destroy($ne->id);
            }
            News::destroy($id);
            return Redirect::route('news.index')->with('success', 'News Deleted Successfully');
        } else {
            return Redirect::route('news.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Search News - 18/06/2021
    public function newsControllerSearch(Request $request)
    {
        $key  = request()->key;
        $date = request()->date;

        $News = News::latest('id')
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    $q->where('tittle', 'like', '%' . $key . '%')
                    ->orWhere('place', 'like', '%' . $key . '%')
                    ->orWhere('description', 'like', '%' . $key . '%');
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->paginate(15);

        if ($News->count() == 0) {
            return redirect()
                ->route('news.index')
                ->with('error', 'No records found');
        }

        return view('backend/news.index', compact('News'));
    }

}