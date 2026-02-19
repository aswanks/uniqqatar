<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Request;

class GalleryController extends Controller
{
    # Function to load Media page - 16/06/2021
    public function galleryControllerMedia()
    {
        $galleries = Gallery::latest('id')
            ->select('id', 'tittle', 'image', 'category_id', 'vdo_url', 'gallery_type')
            ->get();

        return view('frondend/pages/media', compact('galleries'));
    }

    # Function to View all Gallery - 16/06/2021
    public function galleryControllerAdminIndex()
    {
        $galleries = Gallery::latest('id')
            ->select('id', 'tittle', 'image', 'category_id', 'vdo_url', 'gallery_type', 'status', 'created_at', 'updated_at')
            ->paginate(config('coustom.pagination_size'));
        return view('backend/gallery/index', compact('galleries'));
    }

    # Function to Add Gallery - 16/06/2021
    public function galleryControllerAdd()
    {
        return view('backend/gallery/add');
    }

    # Function to Store Gallery - 16/06/2021
    public function galleryControllerStore(Request $request)
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
        }

        $gallery = new Gallery;
        $gallery->tittle = request()->tittle;
        $gallery->gallery_type = request()->gallery_type;
        $gallery->vdo_url = request()->vdo_url;
        $gallery->status = 1;

        if (request()->hasFile('image')) {
            $imgName = time().'_'.uniqid().'.'.request()->image->extension();
            request()->image->move(public_path('uploads/gallery'), $imgName);
            $gallery->image = $imgName;
        }

        $gallery->save();

        return redirect()
            ->route('gallery.admin.index')
            ->with('success', 'Gallery Added Successfully');
    }

    // public function galleryControllerStore()
    // {
    //     $validate = Validator::make(request()->all(), [
    //         'gallery_type' => 'required',
    //         'tittle' => 'required',
    //     ]);
    //     if ($validate->fails()) {
    //           return redirect()->back()->withErrors($validate)->withInput();
    //     } else {
    //         if (Request::hasFile('image')) {
    //             $extension_img = Request::file('image')->getClientOriginalExtension();
    //             if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
    //                 $orginal_img_name = Request::file('image')->getClientOriginalName();
    //                 $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
    //                 $fullPath_img = '/public/uploads/gallery' . $imgName;
    //                 Request::file('image')->move(base_path() . '/public/uploads/gallery', $imgName);
    //                 $gallery = new Gallery;
    //                 $gallery->tittle = request()->tittle;
    //                 // $gallery->category_id = request()->categery;
    //                 $gallery->vdo_url = request()->vdo_url;
    //                 $gallery->gallery_type = request()->gallery_type;
    //                 $gallery->image = $imgName;
    //                 $gallery->status = 1;
    //                 $gallery->save();
    //                 return Redirect::route('gallery.admin.index')->with('success', 'Gallery Added Successfully');
    //             } else {
    //                 return Redirect::back()->with('error', 'Please Upload Image');
    //             }
    //         } else {
    //             $gallery = new Gallery;
    //             $gallery->tittle = request()->tittle;
    //             // $gallery->category_id = request()->categery;
    //             $gallery->vdo_url = request()->vdo_url;
    //             $gallery->gallery_type = request()->gallery_type;
    //             $gallery->status = 1;
    //             $gallery->save();
    //             return Redirect::route('gallery.admin.index')->with('success', 'Gallery Added Successfully');
    //         }
    //     }
    // }

    # Function to Delete Gallery - 16/06/2021
    public function galleryControllerDelete($id)
    {
        $gallery = Gallery::select('id', 'tittle', 'image', 'category_id', 'vdo_url', 'gallery_type', 'status')
            ->where('id', $id)->first();
        if ($gallery) {
            $path_gallery = 'public/uploads/gallery/' . $gallery->image;
            @unlink($path_gallery);
            Gallery::destroy($id);
            return Redirect::route('gallery.admin.index')->with('success', 'Gallery Deletd Successfully');
        } else {
            return Redirect::back()->with('error', 'Invalid Request');
        }
    }

    # Function to Edit Gallery - 16/06/2021
    public function galleryControllerEdit($id)
    {
        $gallery = Gallery::findOrFail($id);
        if ($gallery) {
            return view('backend/gallery/edit', compact('gallery'));
        } else {
            return Redirect::back()->with('error', 'Invalid Request');
        }
    }

    # Function to Update Gallery - 16/06/2021
    public function galleryControllerUpdate(Request $request)
    {
       $id = request()->id;

       $validator = Validator::make(request()->all(), [
            'gallery_type' => 'required|in:1,2',
            'tittle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('galleries', 'tittle')->ignore(request()->id),
            ],
            'image'        => 'nullable|image|mimes:jpg,jpeg,png',
            'vdo_url'      => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $gallery = Gallery::findOrFail($id);

        /* ===============================
        TYPE 1 → IMAGE
        =============================== */
        if (request()->gallery_type == 1) {

            // video must be null
            $gallery->vdo_url = null;

            if (request()->hasFile('image')) {

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

            $gallery->image   = NULL;
            $gallery->vdo_url = request()->vdo_url;
        }

        /* ---------- Common Fields ---------- */
        $gallery->tittle       = request()->tittle;
        $gallery->gallery_type = request()->gallery_type;

        $gallery->save();

        return redirect()
            ->route('gallery.admin.index')
            ->with('success', 'Gallery Updated Successfully');
    }

    # Function to Search Gallery - 17/06/2021
    public function galleryControllerSearch()
    {
        $key          = request('key');
        $gallery_type = request('gallery_type');
        $length       = request('length', 15); // default 15

        $galleries = Gallery::latest('id')
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