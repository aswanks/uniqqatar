<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Validation\Rule;
use App\Models\Blogcategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Str;
use Illuminate\Support\Facades\Redirect;


class BlogcategoriesController extends Controller
{
    # Function to Blogcategories index - 28/12/2025
    public function index(Request $request)
    {
        $categories = Blogcategory::all();
       return view('backend.blogcategory.index',compact('categories'));
    }
    # Function to Blogcategories create - 28/12/2025
    public function create()
    {
        return view('backend.blogcategory.add');
    }
    # Function to Blogcategories store - 28/12/2025
    public function store(Request $request)
    {   
        $validator = Validator::make(request()->all(), [
            'category_name'       => 'required|string|max:255|unique:blogcategories,category_name',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $category=new Blogcategory;
        $category->category_name = request()->category_name;
        $category->status = (int)(request()->status);
        $category->save();
        return Redirect::route('blogcategory.index')->with('success', 'Blogcategory Added Successfully');

    }
    # Function to Blogcategories edit - 28/12/2025
    public function edit($id)
    {
        $category = Blogcategory::findOrFail($id);
        return view('backend.blogcategory.edit',compact('category'));
    }
    # Function to Blogcategories update - 28/12/2025
    public function update()
    {   
        $validator = Validator::make(request()->all(), [
            'category_name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('blogcategories', 'category_name')->ignore(request()->id),
        ],
           
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $id = request()->id;
        $category_name = request()->category_name;
        $status = (int)(request()->status);
                    Blogcategory::where('id', $id)->update([
                        'category_name' => $category_name,
                        'status'        => $status
                    ]);
                    
        return Redirect::route('blogcategory.index')->with('success', 'Blogcategory category Updated Successfully');

    }
    # Function to Blogcategories destory - 28/12/2025
    public function destroy($id)
    {
        Blogcategory::find($id)->delete();
        return Redirect::route('blogcategory.index')->with('deleted','Data deleted successfully');
    }
    # Function to Blogcategories Search - 28/12/2025
    public function blogcategoriesControllerSearch()
    {
        $key       = request()->get('search');

        if (!$key) {
            return redirect()
                ->route('blogcategory.index')
                ->with('error', 'Please enter a Keyword or Date');
        }

        $categories = Blogcategory::latest('id')
            ->where(function ($query) use ($key) {

                if ($key) {
                    $query->where('blogcategories.id', 'like', "%{$key}%")
                        ->orWhere('blogcategories.category_name', 'like', "%{$key}%");
                }
            })
            ->paginate(15)
            ->appends(request()->query());

        return view('backend.blogcategory.index', compact('categories'));
    }
}