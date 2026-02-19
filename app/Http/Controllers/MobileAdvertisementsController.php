<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Validation\Rule;
use App\Models\Mobileadvertisement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Str;
use Illuminate\Support\Facades\Redirect;

class MobileAdvertisementsController extends Controller
{   
    # Function to MobileAdvertisement list - 16/06/2021
    public function index()
    {
        $Adv = Mobileadvertisement::latest('id')
        ->select('id', 'title', 'image','advlink', 'is_approve', 'start_date','end_date', 'created_at','updated_at')
        ->paginate(15);
          return view('backend/mobileadvertisment/index', compact('Adv'));
    }
    # Function to Advertisment add - 16/06/2021
    public function advertismentAdd()
    {
        return view('backend.mobileadvertisment.add');
    }
    # Function to Advertisment store - 16/06/2021
    public function advertismentStore()
    {
        $validate = Validator::make(request()->all(), [
            'title' => 'required',
            'image' => 'required|image|mimes:png,jpeg,jpg,gif|max:1024',
            'advlink'=>'required',

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
                    Request::file('image')->move(base_path() . '/public/assets/themes/frondend/images/advertisment', $imgName);


                    //$now = Carbon::now();
                    $adv = new Mobileadvertisement;
                        $adv->title = request()->title;
                        $adv->image = $imgName;
                        $adv->start_date = request()->start_date;
                        $adv->end_date = request()->end_date;
                        $adv->advlink = request()->advlink;
                        $adv->is_approve = 1;
                        $adv->save();
                        return Redirect::route('mobileadvertisement.index')->with('success', 'Advertisment Added Successfully');
                }
            }
            else {
                return Redirect::back()->with('error', 'Please Upload an Image');
            }
        }
    }
    # Function to Advertisment edit - 16/06/2021
    public function advertismentEdit($id)
    {
        $adv = Mobileadvertisement::findOrFail($id);
        if ($adv) {
            return view('backend/mobileadvertisment/edit', compact('adv'));
        } else {
            return Redirect::route('mobileadvertisement.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Advertisment delete - 16/06/2021
    public function advertismentDelete($id)
    {
        $adv = Mobileadvertisement::find($id);
        if ($adv) {
            $path_offer = '/public/assets/themes/frondend/images/advetisment/' . $adv->image;
            @unlink($path_offer);
            Mobileadvertisement::destroy($id);
            return Redirect::route('mobileadvertisement.index')->with('success', 'Advertisment Deleted Successfully');
        } else {
            return Redirect::route('mobileadvertisement.index')->with('error', 'Invalid Reqeust');
        }
    }
    # Function to Advertisment update - 16/06/2021
    public function advertismentUpdate()
    {
        $validate = Validator::make(request()->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mobileadvertisements', 'title')->ignore(request()->id),
            ],
            'image' => 'required|image|mimes:png,jpeg,jpg,gif|max:2048',
            'advlink'=>'required',

        ]);

        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {
            if (Request::hasFile('image')) {
                $extension_img = Request::file('image')->getClientOriginalExtension();
                if (($extension_img == 'png') || ($extension_img == 'jpeg') || ($extension_img == 'jpg') || ($extension_img == 'PNG') || ($extension_img == 'JPEG') || ($extension_img == 'JPG')) {
                    $orginal_img_name = Request::file('image')->getClientOriginalName();
                    $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
                    Request::file('image')->move(base_path() . '/public/assets/themes/frondend/images/advertisment', $imgName);

                    $id = request()->id;
                    $title = request()->title;
                    $image = $imgName;
                    $advlink = request()->advlink;
                    $start_date = request()->start_date;
                    $end_date= request()->end_date;
                    $is_approve = 1;
                    Mobileadvertisement::where('id', $id)->update([
                        'title' => $title,
                        'image' => $image,
                        'advlink' =>$advlink,
                        'start_date' => $start_date,
                        'end_date' =>$end_date,
                        ]);
                    return Redirect::route('mobileadvertisement.index')->with('success', 'Advertisment Updated Successfully');

                } else {
                    return Redirect::back()->with('error', 'Please Upload Image');
                }
            }else {

                $id = request()->id;
                $title = request()->title;
                $image = request()->image;
                $advlink = request()->advlink;
                $start_date = request()->start_date;
                $end_date= request()->end_date;
                Mobileadvertisement::where('id', $id)->update([
                        'title' => $title,
                        'image' => $image,
                        'advlink' =>$advlink,
                        'start_date' => $start_date,
                        'end_date' =>$end_date,

                        ]);


                return Redirect::route('mobileadvertisement.index')->with('success', 'Event Updated Successfully!');

            }
        }
    }

}