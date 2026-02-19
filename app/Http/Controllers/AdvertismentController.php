<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Validation\Rule;
use App\Models\Advertisment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Str;
use Illuminate\Support\Facades\Redirect;



class AdvertismentController extends Controller
{   
    # Function to Advertisment index - 28/12/2025
    public function index()
    {
        $Adv = Advertisment::latest('id')
        ->select('id', 'title', 'image','advlink', 'is_approve', 'start_time', 'updated_time','created_at','updated_at')
        ->paginate(15);
          return view('backend/advertisment/index', compact('Adv'));
    }
    # Function to Advertisment Search - 28/12/2025
    public function advertismentControllerSearch()
    {
        
        $key       = request()->get('search');
        $startDate = request()->get('start_time');
        $updateDate   = request()->get('updated_time');

        if (!$key && !$startDate && !$updateDate) {
            return redirect()
                ->route('advertisment.index')
                ->with('error', 'Please enter a Keyword or Date');
        }

        $Adv = Advertisment::latest('id')
            ->where(function ($query) use ($key) {

                if ($key) {
                    $query->where('advertisment.id', 'like', "%{$key}%")
                        ->orWhere('advertisment.tittle', 'like', "%{$key}%");
                }
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('advertisment.start_time', '>=', $startDate);
            })
            ->when($updateDate, function ($query) use ($updateDate) {
                $query->whereDate('advertisment.updated_time', '<=', $updateDate);
            })
            ->paginate(15)
            ->appends(request()->query());

        return view('backend.advertisment.index', compact('Adv'));
    }
    # Function to Advertisment Add - 28/12/2025  
    public function advertismentAdd()
    {
        return view('backend.advertisment.add');
    }
    # Function to Advertisment Store - 28/12/2025  
    public function advertismentStore()
    {
        $validate = Validator::make(request()->all(), [
            'title' => 'required',
            'image' => 'required|image|mimes:png,jpeg,jpg,gif|max:25',
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
                    $adv = new Advertisment;
                        $adv->title = request()->title;
                        $adv->image = $imgName;
                        $adv->start_time = request()->start_time;
                        $adv->updated_time = request()->updated_time;
                        $adv->advlink = request()->advlink;
                        $adv->is_approve = 1;
                        $adv->save();
                        return Redirect::route('advertisment.index')->with('success', 'Advertisment Added Successfully');
                }
            }
            else {
                return Redirect::back()->with('error', 'Please Upload an Image');
            }
        }
    }
    # Function to Advertisment Add - 28/12/2025  
    public function advertismentEdit($id)
    {
        $adv = Advertisment::findOrFail($id);
        if ($adv) {
            return view('backend/advertisment/edit', compact('adv'));
        } else {
            return Redirect::route('advertisment.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Advertisment Delete - 28/12/2025  
    public function advertismentDelete($id)
    {
        $adv = Advertisment::find($id);
        if ($adv) {
            $path_offer = '/public/assets/themes/frondend/images/advetisment/' . $adv->image;
            @unlink($path_offer);
            Advertisment::destroy($id);
            return Redirect::route('advertisment.index')->with('success', 'Advertisment Deleted Successfully');
        } else {
            return Redirect::route('advertisment.index')->with('error', 'Invalid Reqeust');
        }
    }
    # Function to Advertisment Update - 28/12/2025  
    public function advertismentUpdate()
    {
        $validate = Validator::make(request()->all(), [
            'title' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('advertisment', 'title')->ignore(request()->id),
                ],
            'image' => 'required|image|mimes:png,jpeg,jpg,gif|max:25',
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
                    $start_time = request()->start_time;
                    $updated_time= request()->updated_time;
                    $is_approve = 1;
                    Advertisment::where('id', $id)->update([
                        'title' => $title,
                        'image' => $image,
                        'advlink' =>$advlink,
                        'start_time' => $start_time,
                        'updated_time' =>$updated_time,
                        ]);
                    return Redirect::route('advertisment.index')->with('success', 'Advertisment Updated Successfully');

                } else {
                    return Redirect::back()->with('error', 'Please Upload Image');
                }
            }else {

                $id = request()->id;
                $title = request()->title;
                $image = request()->image;
                $advlink = request()->advlink;
                $start_time = request()->start_time;
                $updated_time= request()->updated_time;
                    Advertisment::where('id', $id)->update([
                        'title' => $title,
                        'image' => $image,
                        'advlink' =>$advlink,
                        'start_time' => $start_time,
                        'updated_time' =>$updated_time,

                        ]);


                return Redirect::route('advertisment.index')->with('success', 'Event Updated Successfully!');

            }
        }
    }

}