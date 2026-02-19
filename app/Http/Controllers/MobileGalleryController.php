<?php

namespace App\Http\Controllers;

use App\Models\Mobilegallery;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Request;
class MobileGalleryController extends Controller
{
    public function index()
    {
        $galleries = Mobilegallery::latest('id')
            ->select('id', 'tittle', 'image', 'category_id', 'vdo_url', 'gallery_type', 'status', 'created_at', 'updated_at')
            ->paginate(config('coustom.pagination_size'));
        return view('backend/mobilegallery/index', compact('galleries'));
    }

    # Function to Add Gallery - 5/09/2024
    public function add()
    {
        return view('backend/mobilegallery/add');
    }

    # Function to Store Gallery - 5/09/2024
    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'gallery_type' => 'required|in:1,2',
            'tittle'       => 'required',

            'image'        => 'required_if:gallery_type,1|image|mimes:jpg,jpeg,png|max:2048',

            'vdo_url'      => 'required_if:gallery_type,2|nullable|url',
        ], [
            'image.required_if'   => 'Image is required when gallery type is Image.',
            'vdo_url.required_if' => 'Video URL is required when gallery type is Video.',
        ]);
        if ($validator->fails()) {
              return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            if (Request::hasFile('image')) {
                $extension_img = Request::file('image')->getClientOriginalExtension();
                if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
                    $orginal_img_name = Request::file('image')->getClientOriginalName();
                    $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
                    $fullPath_img = '/public/uploads/gallery' . $imgName;
                    Request::file('image')->move(base_path() . '/public/uploads/gallery', $imgName);
                    $gallery = new Mobilegallery;
                    $gallery->tittle = request()->tittle;
                    $gallery->category_id = request()->categery;
                    $gallery->vdo_url = request()->vdo_url;
                    $gallery->gallery_type = request()->gallery_type;
                    $gallery->image = $imgName;
                    $gallery->status = 1;
                    $gallery->save();
                    return Redirect::route('mobilegallery.admin.index')->with('success', 'Gallery Added Successfully');
                } else {
                    return Redirect::back()->with('error', 'Please Upload Image');
                }
            } else {
                $gallery = new Mobilegallery;
                $gallery->tittle = request()->tittle;
                $gallery->category_id = request()->categery;
                $gallery->vdo_url = request()->vdo_url;
                $gallery->gallery_type = request()->gallery_type;
                $gallery->status = 1;
                $gallery->save();
                return Redirect::route('mobilegallery.admin.index')->with('success', 'Gallery Added Successfully');
            }
        }
    }

    # Function to Delete Gallery - 5/09/2024
    public function delete($id)
    {
        $gallery = Mobilegallery::select('id', 'tittle', 'image', 'category_id', 'vdo_url', 'gallery_type', 'status')
            ->where('id', $id)->first();
        if ($gallery) {
            $path_gallery = 'public/uploads/mobilegallery/' . $gallery->image;
            @unlink($path_gallery);
            Mobilegallery::destroy($id);
            return Redirect::route('mobilegallery.admin.index')->with('success', 'Gallery Deletd Successfully');
        } else {
            return Redirect::back()->with('error', 'Invalid Request');
        }
    }

    # Function to Edit Gallery - 5/09/2024
    public function edit($id)
    {
        $gallery = Mobilegallery::findOrFail($id);
        if ($gallery) {
            return view('backend/mobilegallery/edit', compact('gallery'));
        } else {
            return Redirect::back()->with('error', 'Invalid Request');
        }
    }

    # Function to Update Gallery - 5/09/2024
    public function update(Request $request)
    {
        $id = request()->id;

        $validator = Validator::make(request()->all(), [
            'gallery_type' => 'required|in:1,2',
            'tittle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mobilegallery', 'tittle')->ignore(request()->id),
            ],
            'image'        => 'nullable|image|mimes:jpg,jpeg,png',
            'vdo_url'      => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gallery = Mobilegallery::findOrFail($id);

        /* ===============================
        TYPE 1 → IMAGE
        =============================== */
        if (request()->gallery_type == 1) {
            $gallery->vdo_url = null;
            if (Request::hasFile('image')) {

                // delete old image
                if ($gallery->image) {
                    $oldPath = public_path('uploads/gallery/' . $gallery->image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $extension = request()->file('image')->getClientOriginalExtension();
                $fileName  = uniqid('gallery_') . '.' . $extension;

                request()->file('image')->move(
                    public_path('uploads/gallery'),
                    $fileName
                );

                $gallery->image = $fileName;
            }
        }

        /* ===============================
        TYPE 2 → VIDEO
        =============================== */
        if (request()->gallery_type == 2) {

            // image must be null
            if ($gallery->image) {
                $oldPath = public_path('uploads/gallery/' . $gallery->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $gallery->image   = null;
            $gallery->vdo_url = request()->vdo_url;
        }

        /* ---------- Common Fields ---------- */
        $gallery->tittle       = request()->tittle;
        $gallery->category_id  = request()->category;
        $gallery->gallery_type = request()->gallery_type;

        $gallery->save();

        return redirect()
            ->route('mobilegallery.admin.index')
            ->with('success', 'Gallery Updated Successfully');
    }


    

    # Function to Search Gallery - 5/09/2024
    // public function search()
    // {
    //     $key = Request::get('key');
    //     // dd($key);
    //     if ($key) {
    //         $galleries = Mobilegallery::latest('id')
    //             ->where(function ($query) use ($key) {
    //                 $query->where('galleries.id', 'like', '%' . $key . '%')
    //                     ->orWhere('galleries.tittle', 'like', '%' . $key . '%')
    //                     ->orWhere('galleries.image', 'like', '%' . $key . '%')
    //                     ->orWhere('galleries.category_id', 'like', '%' . $key . '%')
    //                     ->orWhere('galleries.vdo_url', 'like', '%' . $key . '%')
    //                     ->orWhere('galleries.gallery_type', 'like', '%' . $key . '%');
    //             })->paginate(15);
    //         return view('backend/mobilegallery/index', compact('galleries'));
    //     } else {
    //         return Redirect::route('mobilegallery.admin.index')->with('error', 'Please enter a Keyword for Searching');
    //     }
    // }

     public function search()
    {
        $key         = request('key');
        $gallery_type = request('gallery_type');
        $length      = request('length', 15); // default 15
        $galleries = Mobilegallery::latest('id')
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    $q->where('id', 'like', "%{$key}%")
                    ->orWhere('tittle', 'like', "%{$key}%")
                    ->orWhere('image', 'like', "%{$key}%")
                    ->orWhere('vdo_url', 'like', "%{$key}%")
                    ->orWhere('gallery_type', 'like', "%{$key}%");
                });
            })
            ->when($gallery_type, function ($query) use ($gallery_type) {
                $query->where('gallery_type', $gallery_type);
            })
            ->paginate($length)
            ->appends(request()->query()); // keep filters on pagination

        return view('backend.gallery.index', compact('galleries'));
         
    }
}