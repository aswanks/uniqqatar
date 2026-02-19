<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Cpd;
use App\Models\About;
use App\Models\Wing;
use App\Models\Advertisment;
use App\Models\Usefullink;
use App\Models\Usefullinkfile;
use Illuminate\Support\Facades\Redirect;



class HomeController extends Controller
{
    # Function to load Home page - 15/06/2021
    public function homeControllerIndex()
    {
        $event_latest = Event::select('id', 'tittle', 'contant', 'start_date', 'start_time', 'end_date', 'end_time', 'image', 'status', 'created_at', 'updated_at', 'address')
            ->orderBy('start_date', 'asc')
            ->get();

        $events_up = Event::select('id', 'tittle', 'contant', 'start_date', 'start_time', 'end_date', 'end_time', 'image','evnt_pdf' ,'status', 'created_at', 'updated_at', 'address')
            //->skip(1)
            ->orderBy('id', 'desc')
            ->take(3)->get();

        $gallires = Gallery::select('id', 'tittle', 'image', 'category_id', 'vdo_url', 'gallery_type')
            ->inRandomOrder()->take(5)->get();

        $News = News::select('id', 'tittle', 'image', 'description')
            ->skip(1)
            ->orderBy('id', 'desc')
            ->take(3)->get();
        $Cpd = Cpd::select('id', 'title', 'slug','image', 'eventlink')
            ->skip(1)
            ->orderBy('id', 'desc')
            ->take(3)->get();

        $firstNews = News::select('id', 'tittle', 'image', 'description')
            ->orderBy('id', 'desc')
            ->first();
        $adv = Advertisment::select('id','title','image','advlink')->orderBy('id','desc')->first();
        
        return view('frondend/home/index', compact('event_latest', 'events_up', 'gallires', 'News', 'Cpd', 'firstNews','adv'));
    }

    # Function to load About-Us page - 16/06/2021
    public function homeControllerAbout()
    {
       $abt =About::select('id','title','brief','details')->first();
        return view('frondend.pages.about',compact('abt'));
    }

    public function wings()
    {
        $wings = Wing::select('image')->where('id',1)->get();
        return view('frondend/pages/wings',compact('wings'));
    }

    # Function to load Contact page - 16/06/2021
    public function homeControllerContact()
    {
        return view('frondend/pages/contact');
    }

    # Function to load Blog page - 16/06/2021
    public function homeControllerBlog()
    {
        $News = News::latest('id')
            ->select('id', 'tittle', 'image', 'description', 'status', 'created_at', 'updated_at')
            ->paginate(10);

        return view('frondend/pages/blog', compact('News'));
    }

    # Function to load Blog-Post - 16/06/2021
    public function homeControllerBlogPost($id)
    {
        $news = News::find($id);
        // dd($news);
        if ($news) {
            return view('frondend/pages/blog-post', compact('news'));
        } else {
            return redirect::back()->with('error', 'Invalid Request');
        }
    }

    # Function to load Event page - 16/06/2021
    public function homeControllerEvents()
    {
        $events = Event::select('id', 'tittle', 'contant', 'start_date', 'start_time', 'end_date', 'end_time', 'image', 'evnt_pdf', 'status', 'created_at', 'updated_at', 'address')
            ->latest('id')
            ->get();
        return view('frondend/pages/events', compact('events'));
    }

    # Function to load one Event page - 04/12/2021
    public function event($id)
    {
        $event = Event::findOrFail($id);
        if ($event) {
            return view('frondend/pages/eachevent', compact('event'));
        } else {
            return redirect::back()->with('error', 'Invalid Request');
        }
    }

    # Function to load Patron page - 16/06/2021
    public function homeControllerPatron()
    {
        return view('frondend/pages/patron');
    }

    # Function to About N Memebers page - 16/06/2021
    public function homeControllerAboutus_N_Members()
    {
        return view('frondend/pages/aboutus_N_members');
    }

    # Function to About N Memebers page - 03/08/2023
    public function usefull_links()
    {
            $ufls = Usefullink::latest('id')
            ->select('id', 'title', 'image', 'description','link','location', 'status')
            ->paginate(15);
            foreach ($ufls as $ufl) {
                $query = Usefullinkfile::where('link_id', $ufl->id)->get();
                $ufl->morepdfdownloads = $query; // Attach morepdfdownloads to each Ufl
            }
        return view('frondend/pages/useful_links',compact('ufls'));    }

    public function cpd_events()
    {
        return view('frondend/pages/cpd_events');
    }
    public function cpdevents()
    {
        $Cpd = Cpd::latest('id')
            ->select('id', 'title', 'slug','image', 'eventlink', 'status', 'created_at', 'updated_at')
            ->paginate(10);
            //dd($Cpd);
        return view('frondend/pages/cpdevents', compact('Cpd'));
    }
    public function homeControllerCpdeventPost($slug)
    {
       $Cpd=Cpd::where('slug',$slug)->select('id','title','slug','eventlink','image','status')->get();
       //dd($Cpd);
        return view('frondend/pages/cpdevent-post', compact('Cpd'));

    }

    # Function to Mission & Vission page - 16/06/2020
    public function homeControllerMissionVission()
    {
        return view('frondend/pages/missionNvision');
    }

    # Function to load Staff page - 16/06/2021
    public function homeControllerStaff()
    {
        return view('frondend.pages.staff');
    }
    public function display()
    {
        $Adv = Advertisment::latest('id')
        ->select('id', 'title', 'image','advlink', 'is_approve', 'start_time', 'updated_time','created_at','updated_at')
        ->get();
          return view('frondend.pages.advertisment', compact('Adv'));
    }

}
