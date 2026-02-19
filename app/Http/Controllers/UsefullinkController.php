<?php

namespace App\Http\Controllers;

use Request;
use App\Models\Usefullink;
use App\Models\Usefullinkfile;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Str;
use Illuminate\Support\Facades\Redirect;
use File;

class UsefullinkController extends Controller
{
    public function usefullinkControllerIndex()
    {
        $Ufls = Usefullink::latest('id')
            ->select('id', 'title', 'image', 'description','link','location', 'status')
            ->paginate(15);
            foreach ($Ufls as $Ufl) {
                $query = Usefullinkfile::where('link_id', $Ufl->id)->get();
                $Ufl->morepdfdownloads = $query; // Attach morepdfdownloads to each Ufl
            }


      // dd($Ufls);
        return view('backend/usefullink/index',compact('Ufls'));
    }
    public function usefullinkControllerCreate()
    {
        return view('backend.usefullink.add');

    }
    public function usefullinkControllerStore()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'link' => 'required',
            'location' => 'required',
            'link_title' => 'required',
            'location_name' => 'required',
            'image' => 'required|file|mimes:pdf|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $Ufl = new Usefullink;
        $Ufl->title = request()->title;
        $Ufl->description = request()->description;
        $Ufl->location = request()->location;
        $Ufl->link = request()->link;
        $Ufl->link_title = request()->link_title;
        $Ufl->location_name = request()->location_name;
        $Ufl->status = 1;

        if (Request::hasFile('image')) {
            $extension_img = Request::file('image')->getClientOriginalExtension();
            if (in_array(strtolower($extension_img), ['png', 'jpeg', 'jpg'])) {
                $orginal_img_name = Request::file('image')->getClientOriginalName();
                $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
                Request::file('image')->move(base_path() . '/public/assets/themes/frondend/images/useful_links', $imgName);
                $Ufl->image = $imgName;
            } else {
                return Redirect::back()->with('error', 'Please Upload Image');
            }
        }

    $Ufl->save();


    $morepdf = is_array(request()->file('downloads')) ? request()->file('downloads') : [];
    //dd($morepdf);
                     foreach ($morepdf as $pdf) {
                         if (!empty($pdf)) {
                            $originalFilename = $pdf->getClientOriginalName();
                            $uploadDir = config('custom.usefullink_pdf_path');
                            $fileName = setNewNameForPhoto(public_path($uploadDir), $originalFilename);
                            $pdf->move(public_path($uploadDir), $fileName);
                            $usefullinkfile = new Usefullinkfile;
                            $usefullinkfile->link_id = $Ufl->id;
                            $usefullinkfile->title = request()->filetitle;
                            $usefullinkfile->pdf = 'public/'.$uploadDir . $fileName;
                            $usefullinkfile ->save();
                         }}
               return Redirect::route('usefullink.index')->with('success', 'Useful Link Added Successfully');


    }


     # Function to Edit  Useful Link - 11/03/2024
     public function usefullinkControllerEdit($id)
     {
         $ufl = Usefullink::findOrFail($id);
         $files = Usefullinkfile::where('link_id',$ufl->id)->get();
        // dd($files);
         if ($ufl) {
             return view('backend/usefullink/edit', compact('ufl','files'));
         } else {
             return Redirect::route('usefullink.index')->with('error', 'Invalid Request');
         }
     }
     public function usefullinkControllerDelete($id)
    {
        $ufl = Usefullink::findOrFail($id);
        $files = Usefullinkfile::where('link_id',$ufl->id)->get();
        if ($ufl) {
            $path_ufl = '/public/assets/themes/frondend/images/useful_links/' . $ufl->image;
            @unlink($path_ufl);
            Usefullink::destroy($id);
            Usefullinkfile::destroy($files);
            return Redirect::route('usefullink.index')->with('success', 'Useful Link Deleted Successfully');
        } else {
            return Redirect::route('usefullink.index')->with('error', 'Invalid Reqeust');
        }
    }
    # Function to Update  Useful Link - 11/03/2024
    public function usefullinkControllerUpdate($id)
    {

        $validator = Validator::make(request()->all(), [
            'title' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('usefullinks', 'title')->ignore(request()->id),
            ],
            'title' => 'required',
            'link' => 'required',
            'location' => 'required',
            'link_title' => 'required',
            'location_name' => 'required',
            'image' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        } else {
            $usefullink = Usefullink::find($id);

            $title = request()->title;
            $description = request()->description;
            $location = request()->location;
            $link = request()->link;
            $link_title = request()->link_title;
            $location_name = request()->location_name;
            $status=1;
            $image = NULL;
            if (Request::hasFile('image')) {
                $extension_img = Request::file('image')->getClientOriginalExtension();
                if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
                    $orginal_img_name = Request::file('image')->getClientOriginalName();
                    $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;

                    Request::file('image')->move(base_path() . 'public/assets/themes/frondend/images/useful_links/', $imgName);
                    $image = $imgName;
                }
            }
                Usefullink::where('id', $id)->update([
                    'title' => $title,
                    'description'=>$description,
                    'image' => $image,
                    'link' => $link,
                    'location' => $location,
                    'link_title' =>$link_title,
                    'location_name'=>$location_name,
                    ]);
            


                   if($usefullink != NULL)
                   {
                    $morepdf = is_array(request()->file('downloads')) ? request()->file('downloads') : [];
                    foreach ($morepdf as $pdf) {
                        if (!empty($pdf)) {
                           $originalFilename = $pdf->getClientOriginalName();
                           $uploadDir = config('custom.usefullink_pdf_path');
                           $fileName = setNewNameForPhoto(public_path($uploadDir), $originalFilename);
                           $usefullinkfile = new Usefullinkfile;
                           $usefullinkfile->link_id = $usefullink->id;
                           $usefullinkfile->title = request()->input('filetitle');
                           $usefullinkfile->pdf = 'public/' . $uploadDir . $fileName;;
                           $usefullinkfile ->save();
                        }}
                    }
                        return Redirect::route('usefullink.index')->with('success', 'Usefullink Updated Successfully!');

        }

    }

    public function deletepdf($id){

        $pdf =Usefullinkfile::findOrfail($id);
        $pdf->delete();
        return back()
        ->withSuccess('Pdf deleted successfully');
    }

}