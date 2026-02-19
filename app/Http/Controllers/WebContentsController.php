<?php

namespace App\Http\Controllers;

use Request;
use App\Models\About;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Str;
use Illuminate\Support\Facades\Redirect;

class WebContentsController extends Controller
{
    public function index()
    {
        $Abouts = About::latest('id')
            ->select('id', 'title','brief','details','patronname','patronimage')
            ->get();
       return view('backend.about.index',compact('Abouts'));
    }
    public function aboutadd()
    {
        return view('backend.about.add');
    }
    public function aboutControllerStore()
    {
    $validate = Validator::make(request()->all(), [
        'title' => 'required',
        'brief' => 'required',
        'details' => 'required',
        'patronimage' => 'image|mimes:png,jpg,jpeg',
        ]);
        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {

                $abt = new About;
                    $abt->title = request()->title;
                    $abt->brief = request()->brief;
                    $abt->details = request()->details;
                    $abt->patronname = request()->patronname;
                    $abt->patronimage = request()->patronimage;
                    $abt->status=1;
                    $abt->save();
                    return Redirect::route('admin.about')->with('success', 'About Added Successfully');

            }


    }
    public function aboutControllerEdit($id)
    {
        $abt = About::findOrFail($id);
        if ($abt) {
            return view('backend/about/edit', compact('abt'));
        } else {
            return Redirect::route('admin.about')->with('error', 'Invalid Request');
        }
    }
    public function aboutControllerDelete($id)
    {
        $abt = About::findOrFail($id);
        if ($abt) {
            About::destroy($id);
            return Redirect::route('admin.about')->with('success', 'About Deleted Successfully');
        } else {
            return Redirect::route('admin.about')->with('error', 'Invalid Reqeust');
        }
    }
    public function aboutControllerUpdate()
    {
        // dd(request()->all());
        $validate = Validator::make(request()->all(), [
            'title' => 'required',
            'brief' => 'required',
            'details' => 'required',
            'patronimage' => 'image|mimes:png,jpg,jpeg',


        ]);

        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {
            if (Request::hasFile('patronimage')) {
                $extension_img = Request::file('patronimage')->getClientOriginalExtension();
                if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
                    $orginal_img_name = Request::file('patronimage')->getClientOriginalName();
                    $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
                    //$fullPath_img = '/public/uploads/images/events' . $imgName;
                    Request::file('patronimage')->move(base_path() . '/public/assets/themes/frondend/images/patron', $imgName);


                $id = request()->id;
                    $title = request()->title;
                    $brief = request()->brief;
                    $details = request()->details;
                    $patronname = request()->patronname;
                    $patronimage = $imgName;
                    $status = 1;
                    About::where('id', $id)->update([
                        'title' => $title,
                        'brief'  =>$brief,
                        'details' => $details,
                        'patronname'=>$patronname,
                        'patronimage'=> $patronimage,
                        ]);
                    return Redirect::route('admin.about')->with('success', 'About Updated Successfully');

                }else {
                    return Redirect::back()->with('error', 'Please Upload Image');
                }
            }else {
                $id = request()->id;
                $title = request()->title;
                $brief = request()->brief;
                $details = request()->details;
                $patronname = request()->patronname;
                $patronimage = request()->$patronimage;
                $status = 1;
                About::where('id', $id)->update([
                    'title' => $title,
                    'brief'  =>$brief,
                    'details' => $details,
                    'patronname'=>$patronname,
                    'patronimage'=> $patronimage,
                    ]);
                    return Redirect::route('admin.about')->with('success', 'About Updated Successfully');

     }
    }}


     

}
