<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Validation\Rule;
use App\Models\Offercategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Str;
use Illuminate\Support\Facades\Redirect;


class OfferCategoryController extends Controller
{   
    # Function to Offercategory List page - 24/11/2023
    public function index(Request $request)
    {
        $categories = Offercategory::all();
       return view('backend.offercategory.index',compact('categories'));
    }
    # Function to Offercategory Search - 24/11/2023
    public function offerscategoryControllerSearch(Request $request)
    {
        $key = request()->get('search');

        if (!$key) {
            return redirect()
                ->route('offercategory.index')
                ->with('error', 'Please enter a keyword');
        }

        $categories = Offercategory::latest('id')
            ->where(function ($query) use ($key) {
                $query->where('id', 'like', "%{$key}%")
                    ->orWhere('category_name', 'like', "%{$key}%");
            })
            ->paginate(15)
            ->appends(request()->query());

        return view('backend.offercategory.index', compact('categories'));
    }
    # Function to Offercategory Create page - 24/11/2023
    public function create()
    {
        return view('backend.offercategory.add');
    }
    # Function to Offercategory Store - 24/11/2023
    public function store(Request $request)
    {   
        $validator = Validator::make(request()->all(), [
            'category_name' => 'required|string|max:255|unique:offercategories,category_name',
            'status'        => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $category= new Offercategory;
        $category->category_name = request()->category_name;
        $category->status = (int)(request()->status);
        $category->save();
        return Redirect::route('offercategory.index')->with('success', 'Offers Added Successfully');

    }
    # Function to Offercategory Edit - 24/11/2023
    public function edit($id)
    {
        $categories = Offercategory::findOrFail($id);
        return view('backend.offercategory.edit',compact('categories'));
    }
    # Function to Offercategory Store - 24/11/2023
    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'id'            => 'required|exists:offercategories,id',
            'category_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('offercategories', 'category_name')->ignore(request()->id),
                ],
            'status'        => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        Offercategory::where('id', request()->id)->update([
            'category_name' => request()->category_name,
            'status'        => (int) request()->status,
        ]);

        return redirect()
            ->route('offercategory.index')
            ->with('success', 'Offers category updated successfully');
    }
    # Function to Offercategory delete   - 24/11/2023
    public function destroy($id)
    {
        Offercategory::find($id)->delete();
        return Redirect::route('offercategory.index')->with('deleted','Data deleted successfully');
    }
}