<?php

namespace App\Http\Controllers;

use Request;
use App\Models\Wing;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Str;
use Illuminate\Support\Facades\Redirect;

class WingsController extends Controller
{
    public function index()
    {
        $Wings = Wing::all();
       return view('backend.wings.index',compact('Wings'));
    }
    public function wingsadd()
    {
        return view('backend.wings.add');
    }
    public function wingsControllerStore()
    {
    $validate = Validator::make(request()->all(), [
        'image' => 'image|mimes:png,jpg,jpeg',
        'name'  => 'required|string|max:255',
        'designation' => 'required|string|max:255',
        'panel' => 'required|string|max:255',
        
        ]);
        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {
            if (Request::hasFile('image')) {
                $extension_img = Request::file('image')->getClientOriginalExtension();
                if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
                    $orginal_img_name = Request::file('image')->getClientOriginalName();
                    $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
                    //$fullPath_img = '/public/uploads/images/events' . $imgName;
                    Request::file('image')->move(base_path() . '/public/assets/themes/frondend/images/uniq_wings', $imgName);


                $wing = new Wing;
                    $wing->name = request()->name;
                    $wing->designation = request()->designation;
                    $wing->image = $imgName;
                    $wing->panel = request()->panel;
                    $wing->status=1;
                    $wing->save();
                    return Redirect::route('admin.wings')->with('success', 'Wings Added Successfully');

            }else {
                return Redirect::back()->with('error', 'Please Upload an Image');
            }
            }
        }


    }
    public function wingsControllerEdit($id)
    {
        $wing = Wing::findOrFail($id);
        if ($wing) {
            return view('backend/wings/edit', compact('wing'));
        } else {
            return Redirect::route('admin.wings')->with('error', 'Invalid Request');
        }
    }
    public function wingsControllerDelete($id)
    {
        $wing = Wing::findOrFail($id);
        if ($wing) {
            Wing::destroy($id);
            return Redirect::route('admin.wings')->with('success', 'Wings Deleted Successfully');
        } else {
            return Redirect::route('admin.wings')->with('error', 'Invalid Reqeust');
        }
    }
    public function wingsControllerUpdate()
    {
        // dd(request()->all());
        $validate = Validator::make(request()->all(), [
            'image' => 'image|mimes:png,jpg,jpeg',
            'name'  => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'panel' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {
            if (Request::hasFile('image')) {
                $extension_img = Request::file('image')->getClientOriginalExtension();
                if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
                    $orginal_img_name = Request::file('image')->getClientOriginalName();
                    $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
                    //$fullPath_img = '/public/uploads/images/events' . $imgName;
                    Request::file('image')->move(base_path() . '/public/assets/themes/frondend/images/uniq_wings', $imgName);

                    $id = request()->id;
                    $name = request()->name;
                    $designation = request()->designation;
                    $image = $imgName;
                    $panel = request()->panel;
                    $status = 1;
                    Wing::where('id', $id)->update([
                        'name' =>$name,
                        'designation' =>$designation,
                        'panel' =>$panel,
                        'image'=> $image,
                        ]);
                    return Redirect::route('admin.wings')->with('success', 'Wings Updated Successfully');

                }else {
                    return Redirect::back()->with('error', 'Please Upload Image');
                }
            }else {
                $id = request()->id;
                    $name = request()->name;
                    $designation = request()->designation;
                    $panel = request()->panel;
                    $image = request()->image;               
                    $status = 1;
                Wing::where('id', $id)->update([
                        'name' =>$name,
                        'designation' =>$designation,
                        'panel' =>$panel,
                        'image'=> $image,                    ]);
                    return Redirect::route('admin.wings')->with('success', 'Wings Updated Successfully');

                }
            }

    }
}