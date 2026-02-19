<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Blog;
use App\Models\Like;
use App\Models\Blogcategory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;


class BlogsController extends Controller
{
    public function index()
    {
        $Blogs = Blog::latest('id')
        ->select('id','category_id', 'image', 'person_image', 'uniq_id','pname', 'description', 'status', 'created_at', 'updated_at')
        ->paginate(15);
    return view('backend/blog/index', compact('Blogs'));
    }

    public function create()
    {
        $categories = Blogcategory::orderBy('category_name','ASC')->get();
        return view('backend.blog.add',compact('categories'));
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'category_id' =>'required',
            'image' => 'required|image|mimes:png,jpeg,jpg',
            'uniq_id' => 'required|numeric',
            'description' => 'required',
            'pname'       => 'required|string|max:255|unique:blogcategories,category_name',
            'person_image' => 'required|image|mimes:png,jpeg,jpg'
        ]);

       
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            if (request()->hasFile('image')) {
                $image_extension = request()->file('image')->getClientOriginalExtension();
                $image_name = request()->file('image')->getClientOriginalName();
                $image_rename = setNewNameForPhoto($image_name) . '.' . generateRandomString(2, 15) . '.' . $image_extension;
                $upload_path = '/public/uploads/blogs' . $image_rename;
                request()->file('image')->move(base_path() . '/public/uploads/blogs', $image_rename);
            }

            if (request()->hasFile('person_image')) {
                $image_extension = request()->file('person_image')->getClientOriginalExtension();
                $image_name = request()->file('person_image')->getClientOriginalName();
                $image_pname = setNewNameForPhoto($image_name) . '.' . generateRandomString(2, 15) . '.' . $image_extension;
                $upload_path = '/public/uploads/blogs/persons' . $image_pname;
                request()->file('person_image')->move(base_path() . '/public/uploads/blogs/persons', $image_pname);
            }

            $blog = new Blog;
            $blog->category_id = request()->category_id;
            $blog->blog_category = NULL;
            $blog->description = request()->description;
            $blog->pname = request()->pname;
            $blog->uniq_id = request()->uniq_id;
            $blog->image = $image_rename;
            $blog->person_image = $image_pname;
            $blog->status = 1;
            $blog->save();
            

            $this->sendNotification($blog);
            $like= New Like;
            $like->blog_id= $blog->id;
            $like->save();


            return Redirect::route('blog.index')->with('success', 'Blog Added Successfully');
        }

    }
    
            private function sendNotification()
    {
        $url = "https://uniq-rho.vercel.app/send-notification";

        // Fetch the latest offer
        $latestBlog = Blog::latest()->first();

        if ($latestBlog) {
            // Prepare the offer data for the notification body
            $infoData = [
                'title' => $latestBlog->blog_category,
                'Message' => $latestBlog->description,  // Adjust fields based on your `Offer` model
            ];

            // Convert the offer data to a suitable format for the payload
            $payload = [
                "title" => "New Blog Available",
                "body" => $latestBlog->blog_category,
                "data" =>[
                    "key1"=> "blog",
                    "category"=>$latestBlog->blog_category,
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


    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = Blogcategory::all();

        if ($blog) {
            return view('backend/blog/edit', compact('blog','categories'));
        } else {
            return Redirect::route('blog.index')->with('error', 'Invalid Request');
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'category_id' => 'required',
            'image' => 'image|mimes:png,jpeg,jpg',
            'description' => 'required',
            'uniq_id' => 'required|numeric',
            'person_image' => 'image|mimes:png,jpeg,jpg',
        ]);

        if ($validator->fails()) {
              return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        } else {
                    $id = $request->input('id');
                    $blog = Blog::findOrFail($id);
                    
                    $image_rename = $blog->image;
                    $image_pname  = $blog->person_image;

                    if (request()->hasFile('image')) {
                        $image_extension = request()->file('image')->getClientOriginalExtension();
                        $image_name = request()->file('image')->getClientOriginalName();
                        $image_rename = setNewNameForPhoto($image_name) . '.' . generateRandomString(2, 15) . '.' . $image_extension;
                        request()->file('image')->move(base_path() . '/public/uploads/blogs', $image_rename);
                    }

                    if (request()->hasFile('person_image')) {
                        $image_extension = request()->file('person_image')->getClientOriginalExtension();
                        $image_name = request()->file('person_image')->getClientOriginalName();
                        $image_pname = setNewNameForPhoto($image_name) . '.' . generateRandomString(2, 15) . '.' . $image_extension;
                        request()->file('person_image')->move(base_path() . '/public/uploads/blogs/persons', $image_pname);
                    }

                    $blog->category_id = request()->category_id;
                    $blog->blog_category = NULL;
                    $blog->description = request()->description;
                    $blog->pname = request()->pname;
                    $blog->uniq_id = request()->uniq_id;
                    $blog->image = $image_rename;
                    $blog->person_image = $image_pname;
                    $blog->status = 1;
                    $blog->save();
                    return Redirect()->route('blog.index')->with('success', 'Blog Updated Successfully');
        }
    }


    public function destroy($id)
    {
        $blog = Blog::find($id);
        if ($blog) {

            Blog::destroy($id);
            return Redirect::route('blog.index')->with('success', 'Blog Deleted Successfully');
        } else {
            return Redirect::route('blog.index')->with('error', 'Invalid Reqeust');
        }
    }
}