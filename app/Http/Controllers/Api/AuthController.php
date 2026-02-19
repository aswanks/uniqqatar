<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eventregister;
use App\Models\EventFeedBackQuestion;
use App\Models\EventFeedback;
use App\Models\Event;
use App\Models\News;
use App\Models\Offer;
use App\Models\Mobileadvertisement;
use App\Models\Registarion;
use App\Models\Mobilegallery;
use App\Models\About;
use App\Models\Wing;
use App\Models\Usefullink;
use App\Models\Usefullinkfile;
use App\Models\Information;
use App\Models\Blog;
use App\Models\Like;
use App\Models\Cpd;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Str;
use setasign\Fpdi\Fpdi;
use App\Mail\RegistrationMail;
use App\Mail\EventConfirmationReplyMail;
use App\Mail\ApproveMail;
use App\Mail\RenewalMail;
use App\Mail\ShareMemberDetailsMail;
use App\Mail\SendCustomMail;
use Mail;
use App\Mail\SignupMail;
use App\Mail\DeleteProfileConfirmation;
use App\Mail\DeleteProfileConfirmationPassword;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{

    public function index(Request $request)
    {
       // return response()->json(auth()->user());
       $validator = Validator::make($request->all(), [
        'qid' => 'required',
        'password' => 'required',
        'fcm_token' => 'nullable|string',

      ]);
      if($validator->fails()) {
        return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
      }else{
        $qid = $request['qid'];
        $password = $request['password'];
            $fcmToken = $request->fcm_token;

        $user_data = \App\Models\User::select('id','firstname','lastname','email','password','qid','status')
             ->where('user_type',2)
             ->where('status',1)
             //->where('password',$password)
             ->where('qid',$qid)->first();
             if($user_data){
             if(Hash::check($password, $user_data->password))
             {
             $api_token = Str::random(20);
             $user_data->api_token = $api_token;
                         $user_data->fcm_token = $fcmToken;

             $user_data->save();
                \App\Models\User::where('id', $user_data->id);
                return response()->json(['Code'=>"200",'Message'=>"Login Success",'user'=>$user_data], 200);

              }else{
                  return response()->json(['Code'=>"401",'Message'=>"Invalid User"], 401);
             }}else{
                     return response()->json(['Code'=>"401",'Message'=>"Invalid User"], 401);
              }
            }


       // return view('frondend.customer.login');
    }
    public function doLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' =>'required'
          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{

            $user_id = $request['user_id'];
             $api_token =$request['api_token'];
          $user_data = validateUser($user_id,$api_token);
          //dd($user_data);
          if($user_data == true)

           {
               $users = Registration::select('uniq_id', 'image', 'registration_date','status')
            ->where('user_id', $user_id)
            ->first();

        if ($users) {
            // Calculate expiry date dynamically (assuming 1 year from registration)
            $registrationDate = Carbon::parse($users->registration_date);
    if ($registrationDate >= Carbon::create(2024, 1, 1)) {
        $expiryDate = $registrationDate->addYears(2);
        $users->expiry_date = $expiryDate->format('Y-m-d');
    } else {
        $expiryDate = $registrationDate->addYears(1);
        $users->expiry_date = $expiryDate->format('Y-m-d'); // Optional: Handle for older registrations
    }

        }
                $priorityOneEvent = Event::where('priority', 1)
                    ->where(function ($query) {
                        $query->where('end_date', '>=', now()) // Event has not ended yet
                            ->orWhereNull('end_date'); // No end date specified
                    })
                    ->orderBy('end_date', 'desc') // Order by end_date in ascending order to prioritize soonest ending or no end date
                    ->first();

                // If no active priority one event found, fetch the latest event overall
                if ($priorityOneEvent !== NULL ) {
                    // If an active priority 1 event is found, use it
                    $events = $priorityOneEvent;

                } else {
                    // If no active priority 1 event found, fetch the latest event overall
                    $events = Event::latest('id', 'desc')->first();


                }           
            $News=News::latest()->first();
            $advs = Mobileadvertisement::latest()->get()->toArray();
            $offers=Offer::count();
            $msgcount =Information::count();
            $blog = Blog::count();
           $galleries=Mobilegallery::whereNotNull('tittle')->select('tittle','image')->get();

                  $allData = [
                    'users' =>$users,
                    'events' => $events,
                    'News'  =>$News,
                    'advertisements' => $advs,
                    'galleries' =>$galleries,
                    'offers' =>$offers,
                    'messagecount' =>$msgcount,
                    'blog'=>$blog,
                    // Add other types of data as needed
                ];

                return response()->json($allData);
            }

        else{
             //return view('frondend.customer.login') -> withError('You are entered inavalid email');
             return response()->json([
                 'error' => 'You have entered an invalid user and api token.',
             ], 422);
        }}
    }
    
    public function offer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' => 'required'
          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $user_id = $request['user_id'];
            $api_token =$request['api_token'];
            $user_data = validateUser($user_id,$api_token);
          //dd($user_data);
          if($user_data == true)
          {

        if ($user_data) {
            // Check if the user's registration status is 1
            $register = \App\Models\Registration::where('user_id',$user_id)
            ->where('status',1)->first();
            if($register)
             {
                // Fetch offers by joining with the registrations table
              $Offers = \App\Models\Offer::orderBy('id', 'desc')->get();


                if ($Offers->count() > 0) {
                    return response()->json([
                        'status' => 200,
                        'offers' => $Offers,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'No Records Found',
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => 403,
                    'message' => 'Access Denied: Registration not active',
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
          }  }
        }
    }

    public function myEvents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|integer',
            'api_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 400,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user_id   = $request->user_id;
        $api_token = $request->api_token;

        $user_data = validateUser($user_id, $api_token);

        if (!$user_data) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthorized user.'
            ], 401);
        }

       $myEvents = Eventregister::join('events', 'events.id', '=', 'eventregisters.event_id')
            ->where('eventregisters.user_id', $user_id)
            ->select(
                'eventregisters.*',
                'events.tittle as event_title',
                'events.image as event_image'
            )
            ->get();


        return response()->json([
            'status'   => 200,
            'myevents' => $myEvents
        ], 200);
    }


    public function message(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' => 'required'
          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $user_id = $request['user_id'];
             $api_token =$request['api_token'];
          $user_data = validateUser($user_id,$api_token);
          //dd($user_data);
          if($user_data == true)
          {
                // \App\Models\Registration::where('id', $user_data->user_id);
        $msgs =\App\Models\Information::all();
        if($msgs->count() > 0){
            return response()->json([
                'status' =>200,
                'msgs' =>$msgs,

            ],200);
        }else{
            return response()->json([
                'status' =>404,
                'message' =>'No Records Found',

            ],404);
        }}else{
        return response()->json([
            'msgs'=>$msgs
        ],200);
    }}
    }
    
    public function postoffer($slug, Request $request)
    {
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' => 'required'
          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $user_id = $request['user_id'];
             $api_token =$request['api_token'];
          $user_data = validateUser($user_id,$api_token);
          //dd($user_data);
          if($user_data == true)
          {
            $offer = Offer::where('slug', $slug)->select('category_name', 'image', 'details')->first();
            $user = Registration::where('user_id', $user_id)
            ->where('status',1)->first();
           if(!$user)
           {
            return response()->json(['error' => 'User not found'], 404);

           }else{
             $now = Carbon::now();

             $approve = [
            'to_email' => $user->email,
            'FirstName' => $user->first_name,
            'LastName' => $user->last_name,
            'imgName' => $user->image,
            'bloodGroup' => $user->blood_grp,
            'qid' => $user->qid,
            'emplyoyer_crnt' => $user->employer_current,
            'uniq_id' => $user->uniq_id,
            'membership_validity_from' => $now->toDateString(),
            'membership_validity_to' => $now->addYear(2)->toDateString(),
        ];
        return response()->json(['data' => ['offer' => $offer, 'approve' => $approve]], 200);
             }

          }else {
            return response()->json(['error' => 'You are not a member (Not approved)'], 403);
            }

        }
    }
    
    public function blog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' => 'required'
          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $user_id = $request['user_id'];
             $api_token =$request['api_token'];
          $user_data = validateUser($user_id,$api_token);
          //dd($user_data);
          if($user_data == true)
          {
                // \App\Models\Registration::where('id', $user_data->user_id);
        $blogs =\App\Models\Blog::all();
        if($blogs->count() > 0){
            return response()->json([
                'status' =>200,
                'blog' =>$blogs,

            ],200);
        }else{
            return response()->json([
                'status' =>404,
                'message' =>'No Records Found',

            ],404);
        }}else{
        return response()->json([
            'blogs'=>$blogs
        ],200);
    }}
    }
    
    public function events(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' => 'required'
        ]);
        
        if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
        }else{
            $user_id = $request['user_id'];
            $api_token =$request['api_token'];
            
            $user_data = validateUser($user_id,$api_token);
            if($user_data == true)
            {
                
                $Events =\App\Models\Event::with('registrations')->get();
                if($Events->count() > 0){
                    return response()->json([
                        'status' =>200,
                        'events' =>$Events,

                    ],200);
                }else{
                    return response()->json([
                        'status' =>404,
                        'message' =>'No Records Found',

                    ],404);
                }
                
            }}
    }

    public function news(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' => 'required'
          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $user_id = $request['user_id'];
             $api_token =$request['api_token'];
          $user_data = validateUser($user_id,$api_token);
          //dd($user_data);
          if($user_data == true)
          {
                // \App\Models\Registration::where('id', $user_data->user_id);
        $News =\App\Models\News::all();
        if($News->count() > 0){
            return response()->json([
                'status' =>200,
                'news' =>$News,

            ],200);
        }else{
            return response()->json([
                'status' =>404,
                'message' =>'No Records Found',

            ],404);
        }}else{
        return response()->json([
            'news'=>$News
        ],200);
    }}
        // return view('frondend.customer.offerpage',compact('Offers'));
    }
    
    public function event(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',

          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $id = $request['id'];
            $Events =\App\Models\Event::where('id',$id)->select('id','tittle','contant','image','adult_status','child_below_5','child_5_to_10','start_date','end_date')->get();


       // $event = Event::findOrFail($id);
        if ($Events) {
            return response()->json(['events' => $Events]);

            //return view('frondend/pages/eachevent', compact('event'));
        } else {
            return response()->json(['error' => 'Invalid Request'], 404);

            //return redirect::back()->with('error', 'Invalid Request');
        }}
    }
     
     public function newsone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',

          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $id = $request['id'];
            $News =\App\Models\News::where('id',$id)->select('id','tittle','place','image','date','description')->get();


       // $event = Event::findOrFail($id);
        if ($News) {
            return response()->json(['news' => $News]);

            //return view('frondend/pages/eachevent', compact('event'));
        } else {
            return response()->json(['message' =>'No Records Found'], 404);

            //return redirect::back()->with('error', 'Invalid Request');
        }}
    }
    
    public function offerone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',

          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $id = $request['id'];
            $Offers =\App\Models\Offer::where('id',$id)->select('id','category_name','image','details','start_date','end_date')->get();


       // $event = Event::findOrFail($id);
        if ($Offers) {
            return response()->json(['Offers' => $Offers]);

            //return view('frondend/pages/eachevent', compact('event'));
        } else {
            return response()->json(['message' =>'No Records Found'], 404);

            //return redirect::back()->with('error', 'Invalid Request');
        }}
    }   
     
    public function membershipcard(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' => 'required'
          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{
            $user_id = $request['user_id'];
             $api_token =$request['api_token'];
          $user_data = validateUser($user_id,$api_token);
          //dd($user_data);
          if($user_data == true)
          {
            $user = Registration::where('user_id', $user_id)->first();
           if(!$user)
           {
            return response()->json(['error' => 'User not found'], 404);

           }else{
             $now = Carbon::now();

              $approve = [
            'to_email' => $user->email,
            'FirstName' => $user->first_name,
            'LastName' => $user->last_name,
            'imgName' => $user->image,
            'bloodGroup' => $user->blood_grp,
            'qid' => $user->qid,
            'emplyoyer_crnt' => $user->employer_current,
            'uniq_id' => $user->uniq_id,
            'membership_validity_from' => $now->toDateString(),
            'membership_validity_to' => $now->addYear(2)->toDateString(),
        ];

        return response()->json(['data' => ['approve' => $approve]], 200);
    }}}


    }
    
    public function about()
    {
       $about = About::all();
       return response()->json([
        'About'=>$about
    ],200);
    }
    
    public function wing()
    {
       $wing = Wing::all();
       return response()->json([
        'Wing'=>$wing
    ],200);

    }
    
    public function useful_link()
    {
       $Ufls = Usefullink::latest('id')
        ->select('id', 'title', 'image', 'description', 'link', 'location', 'status','location_name','link_title')
        ->with('files') // Eager load the files relationship
        ->paginate(15);

    // Return the paginated results with files
    return response()->json($Ufls, 200);

    }
    
    public function cpd()
    {
        $cpd = Cpd::all();
        return response()->json([
            'Cpd'=>$cpd
        ],200);

    }
    

    
   public function logout(Request $request)
   {
    // Auth::logout();
    // return Redirect::intended('/');
    $validator = Validator::make($request->all(), [
        'user_id' => 'required',
        'api_token' => 'required'
      ]);
      if($validator->fails()) {
        return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
      }else{
        $user_id = $request['user_id'];
         $api_token =$request['api_token'];
      $user_data = validateUser($user_id,$api_token);
      //dd($user_data);
      if($user_data == true)
      {
       $user_data = \App\Models\User::where('id', $user_id)
        ->where('api_token', $api_token)
        ->select('id', 'api_token', 'firstname', 'lastname', 'email')
        ->first();
       $user_data->api_token = null;
        $user_data->save();
        }}
    return response()->json(['message' => 'Successfully logged out']);
   }
   
    public function signup(Request $request)
   {
    $validator = Validator::make($request->all(), [
        'qid' => 'required',
        'email' => 'required'
      ]);
      if($validator->fails()) {
        return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
      }else{
        $password = Str::random(6); // Adjust the length of the password as needed

        // Send an email with the generated password
        Mail::to($request->input('email'))->send(new SignupMail($password));

        // Check if the user already exists in the database
        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            // Update the existing user's password
            $user->password = bcrypt($password);
            $user->save();
        } else {
            // Create a new user if the user doesn't exist
            $newUser = new User();
            $newUser->qid = $request->input('qid');
            $newUser->email = $request->input('email');
            $newUser->password = bcrypt($password);
            $newUser->save();
        }

        return response()->json(['Code' => "200", 'Message' => "User signed up successfully.", 'Password' => $password], 200);


      }
    }
   
    public function renewal()
    {
        $membershipInfo = [
            'lead' => '33613997',
            'mobile' => '33184454',
            'whatsapp' => '33184454',
            'email' => 'uniqqatar@gmail.com',
        ];

        return response()->json(['membershioInfo' =>$membershipInfo]);
    }
    public function resetPassword(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'api_token' =>'required',
            'old_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',       // At least one uppercase letter
                'regex:/[a-z]/',       // At least one lowercase letter
                'regex:/[0-9]/',       // At least one number
                'regex:/[@$!%*#?&]/',  // At least one special character
            ],          ]);
          if($validator->fails()) {
            return response()->json(['Code'=>"400",'Message'=>"Validation Error.",$validator->errors()], 400);
          }else{

            $user_id = $request->input('user_id');
    $api_token = $request->input('api_token');

    // Assuming validateUser is a helper function that validates the user based on the user ID and token
    if (validateUser($user_id, $api_token)) {
        // Retrieve the user by ID
        $user = User::find($user_id);

        if ($user) {
            // Check if the old password is correct
            if (Hash::check($request->input('old_password'), $user->password)) {
                // Update the user's password
                $user->password = Hash::make($request->input('new_password'));
                $user->save();

                return response()->json(['message' => 'Password reset successfully'], 200);
            } else {
                return response()->json(['Code' => "400", 'Message' => 'Old password is incorrect'], 400);
            }
        } else {
            return response()->json(['Code' => "404", 'Message' => 'User not found'], 404);
        }
    } else {
        return response()->json(['Code' => "401", 'Message' => 'Invalid API token or user ID'], 401);
    }
    }

    }
    
    // public function eventregistration(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required',
    //         'event_id' =>'required',
    //         'api_token' =>'required',
    //         'number_of_adult' => 'required|integer',
    //         'number_of_child' => 'required|integer',
    //         'workplace'=>'required'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['Code' => "400", 'Message' => "Validation Error.", 'Errors' => $validator->errors()], 400);
    //     } else {
                
    //             $user_id = $request['user_id'];
    //             $api_token =$request['api_token'];
                
    //             $user_data = validateUser($user_id,$api_token);
                
    //             if (!$user_data) {
    //                 return response()->json(['Code' => "401", 'Message' => "Unauthorized user."], 401);
    //             } 

    //             $alreadyRegistered = Eventregister::where('user_id',$user_id)  
    //             ->where('event_id', $request->event_id) 
    //             ->exists();
                
    //             if($alreadyRegistered ){
    //                 return response()->json([
    //                     'Code' => "409",
    //                     'Message' => "You are already registered for this event. Please contact Uniqqatar officials."
    //                 ], 409);
    //             }   

    //              $event = Event::select('adult_status', 'child_status')
    //                 ->where('id', $request->event_id)
    //                 ->first();

    //             if (!$event) {
    //                 return response()->json([
    //                     'Code' => "404",
    //                     'Message' => "Event not found."
    //                 ], 404);
    //             }
                
    //             $register = new Eventregister;
    //             $register->user_id = $request->input('user_id');
    //             $register->event_id = $request->input('event_id');
    //             $register->number_adult = $request->input('number_of_adult');
    //             $register->number_child = $request->input('number_of_child');
    //             $register->workplace = $request->input('workplace');
    //             $register->save();
    //             try {
    //                 Mail::to($register->user->email)->send(new EventConfirmationReplyMail($register));
    //                 return response()->json([
    //                     'Code' => "200", 
    //                     'Message' => "Registration successful."
    //                 ], 200);
    //             } catch (\Exception $e) {
    //                 return response()->json([
    //                     'Code' => 500,
    //                     'Message' => 'Mail sending failed, but the password was updated successfully.'
    //                 ], 500);
    //             }        }

    // }
    
    // public function eventregistration(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id'   => 'required|integer',
    //         'event_id'  => 'required|integer|exists:events,id',
    //         'api_token' => 'required',
    //         'workplace' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'Code' => "400",
    //             'Message' => "Validation Error.",
    //             'Errors' => $validator->errors()
    //         ], 400);
    //     }

    //     $user_data = validateUser($request->user_id, $request->api_token);
    //     if (!$user_data) {
    //         return response()->json([
    //             'Code' => "401",
    //             'Message' => "Unauthorized user."
    //         ], 401);
    //     }

    //     $alreadyRegistered = Eventregister::where('user_id', $request->user_id)
    //         ->where('event_id', $request->event_id)
    //         ->exists();

    //     if ($alreadyRegistered) {
    //         return response()->json([
    //             'Code' => "409",
    //             'Message' => "You are already registered for this event. Please contact Uniqqatar officials."
    //         ], 409);
    //     }

    //     $event = Event::select(
    //             'adult_status',
    //             'child_below_5',
    //             'child_5_to_10'
    //         )
    //         ->where('id', $request->event_id)
    //         ->first();

    //     if (!$event) {
    //         return response()->json([
    //             'Code' => "404",
    //             'Message' => "Event not found."
    //         ], 404);
    //     }

    //     if ($event->adult_status == 1) {
    //         $request->validate([
    //             'number_of_adult' => 'required|integer|min:1'
    //         ]);
    //     } else {
    //         if ((int) $request->number_of_adult > 0) {
    //             return response()->json([
    //                 'Code' => "400",
    //                 'Message' => "Adult registration is disabled for this event."
    //             ], 400);
    //         }
    //     }

    //     $minChild = 1;
    //     $maxChild = 10;
        
    //     if ($event->child_below_5 == 1 || $event->child_5_to_10 == 1) {
    //         $request->validate([
    //             'number_of_child' => "required|integer|min:$minChild|max:$maxChild"
    //         ]);
    //     } else {
    //         if ((int) $request->number_of_child > 0) {
    //             return response()->json([
    //                 'Code' => "400",
    //                 'Message' => "Child registration is disabled for this event."
    //             ], 400);
    //         }
    //     }

    //     // Save registration
    //     $register = new Eventregister();
    //     $register->user_id        = $request->user_id;
    //     $register->event_id       = $request->event_id;
    //     $register->number_adult   = $request->number_of_adult ?? 0;
    //     $register->number_child   = $request->number_of_child ?? 0;
    //     $register->workplace      = $request->workplace;
    //     $register->save();

    //     // Send mail
    //     try {
    //         // Mail::to($register->user->email)
    //         //     ->send(new EventConfirmationReplyMail($register));

    //         return response()->json([
    //             'Code' => "200",
    //             'Message' => "Registration successful."
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'Code' => 500,
    //             'Message' => 'Registration completed, but email sending failed.'
    //         ], 500);
    //     }
    // }

    public function eventregistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|integer',
            'event_id'  => 'required|integer|exists:events,id',
            'api_token' => 'required',
            'workplace' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Code' => "400",
                'Message' => "Validation Error.",
                'Errors' => $validator->errors()
            ], 400);
        }

        if (!validateUser($request->user_id, $request->api_token)) {
            return response()->json([
                'Code' => "401",
                'Message' => "Unauthorized user."
            ], 401);
        }

        if (
            Eventregister::where('user_id', $request->user_id)
                ->where('event_id', $request->event_id)
                ->exists()
        ) {
            return response()->json([
                'Code' => "409",
                'Message' => "You are already registered for this event."
            ], 409);
        }

        $event = Event::select(
            'adult_status',
            'child_above_10',
            'child_below_5',
            'child_5_to_10'
        )->find($request->event_id);

        if (!$event) {
            return response()->json([
                'Code' => "404",
                'Message' => "Event not found."
            ], 404);
        }

        $rules = [];
        $messages = [];

        // Adult 
        if ($event->adult_status == 1) {
            $rules['number_of_adult'] = 'required|integer|min:1|max:10';
        } else if ((int) $request->number_of_adult > 0) {
            return response()->json([
                'Code' => "400",
                'Message' => "Adult registration is disabled for this event."
            ], 400);
        }

        // Child below 5
        if ($event->child_below_5 == 1) {
            $rules['no_child_below_5'] = 'required|integer|min:0|max:10';
        } elseif ((int) $request->no_child_below_5 > 0) {
            return response()->json([
                'Code' => "400",
                'Message' => "Below 5 years child registration is disabled for this event."
            ], 400);
        }
        
        // Child 5 to 10 
        if ($event->child_5_to_10 == 1) {
            $rules['no_child_5_to_10'] = 'required|integer|min:0|max:10';
        } elseif ((int) $request->no_child_5_to_10 > 0) {
            return response()->json([
                'Code' => "400",
                'Message' => "5 to 10 years child registration is disabled for this event."
            ], 400);
        }

        if ($event->child_above_10 == 1) {
            $rules['no_child_above_10'] = 'required|integer|min:0|max:10';
        } elseif ((int)$request->no_child_above_10 > 0) {
            return response()->json([
                'Code' => 400,
                'Message' => '10+ years child registration is disabled.'
            ], 400);
        }
    
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'Code' => 400,
                    'Message' => 'Validation Error.',
                    'Errors' => $validator->errors()
                ], 400);
            }
        }

       $register = Eventregister::create([
            'user_id' => $request->user_id,
            'event_id' => $request->event_id,
            'number_adult' => $request->number_of_adult ?? 0,
            'no_child_below_5' => $request->no_child_below_5 ?? 0,
            'no_child_5_to_10' => $request->no_child_5_to_10 ?? 0,
            'no_child_above_10' => $request->no_child_above_10 ?? 0,
            'workplace' => $request->workplace,
        ]);

        // Send mail
        try {
            if ($register->user && $register->user->email) {
                Mail::to($register->user->email)
                    ->queue(new EventConfirmationReplyMail($register));
            }
        } catch (\Exception $e) {
            \Log::error('Event mail failed: ' . $e->getMessage());
        }

        return response()->json([
            'Code' => 200,
            'Message' => 'Registration successful.'
        ], 200);
    }


    public function likes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'blog_id' => 'required|exists:blogs,id',
            'api_token' => 'required',
            'likestatus' => 'required|boolean', // 1 for like, 0 for unlike
            'comment' => 'nullable|string|max:500' // Optional Comment
        ]);

        if ($validator->fails()) {
            return response()->json(['Code' => 400, 'Message' => "Validation Error.", 'Errors' => $validator->errors()], 400);
        }

        $user_data = validateUser($request->user_id, $request->api_token);
        if (!$user_data) {
            return response()->json(['message' => 'Invalid user or API token'], 401);
        }

        // Check if user has already interacted with the blog
        $like = Like::where('user_id', $request->user_id)
                    ->where('blog_id', $request->blog_id)
                    ->first();

        if ($request->likestatus == 1) { // Liking the post
            if (!$like) {
                // ✅ User is liking the post for the first time
                Like::create([
                    'user_id' => $request->user_id,
                    'blog_id' => $request->blog_id,
                    'likestatus' => 1,
                    'comment' => $request->comment ?? null // Store comment if provided
                ]);
                return response()->json(['message' => 'Post liked successfully'], 201);
            } elseif ($like->likestatus == 0) {
                // ✅ User previously unliked or commented, update to like
                $like->update([
                    'likestatus' => 1,
                    'comment' => $request->comment ?? $like->comment // Preserve existing comment
                ]);
                return response()->json(['message' => 'Post liked again'], 200);
            } else {
                return response()->json(['message' => 'Post already liked'], 200);
            }
        } else { // Unliking the post
            if ($like) {
                // ✅ Only update `likestatus`, keep the comment intact
                $like->update(['likestatus' => 0]);

                return response()->json(['message' => 'Post unliked successfully'], 200);
            } else {
                return response()->json(['message' => 'Post not liked yet'], 200);
            }
        }
    }

    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'blog_id' => 'required|exists:blogs,id',
            'api_token' => 'required',
            'comment' => 'required|string|max:500', // Comment is required
        ]);

        if ($validator->fails()) {
            return response()->json(['Code' => 400, 'Message' => "Validation Error.", 'Errors' => $validator->errors()], 400);
        }

        // Validate User
        $user_data = validateUser($request->user_id, $request->api_token);
        if (!$user_data) {
            return response()->json(['message' => 'Invalid user or API token'], 401);
        }

        // Check if user already has a row in the likes table for this blog
        $like = Like::where('user_id', $request->user_id)
                    ->where('blog_id', $request->blog_id)
                    ->first();

        if ($like) {
            // ✅ User has liked/unliked before, just update the comment
            $like->update(['comment' => $request->comment]);

            return response()->json(['message' => 'Comment updated successfully'], 200);
        } else {
            // ✅ User hasn't interacted before, create a new row with likestatus = 0
            Like::create([
                'user_id' => $request->user_id,
                'blog_id' => $request->blog_id,
                'likestatus' => 0, // User has commented but not liked
                'comment' => $request->comment
            ]);

            return response()->json(['message' => 'Comment added successfully without liking'], 201);
        }
    }

    public function getLikes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blog_id' => 'required|exists:likes,blog_id',
            'user_id' => 'required|exists:users,id' // Validate user_id in the users table
        ]);

        if ($validator->fails()) {
            return response()->json(['Code' => 400, 'Message' => "Validation Error.", 'Errors' => $validator->errors()], 400);
        }

        $blog_id = $request->blog_id;

        // Count total likes where likestatus = 1
        $likesCount = Like::where('blog_id', $blog_id)
                        ->where('likestatus', 1)
                        ->count();

        // Get list of users who liked the blog post with user details from the `register` table
        $likedUsers = Like::where('blog_id', $blog_id)
                        ->where('likestatus', 1)
                        ->join('registrations', 'registrations.user_id', '=', 'likes.user_id') // Get data from `register` table
                        ->join('users', 'users.id', '=', 'likes.user_id') // Validate user exists in `users`
                        ->get([
                            'registrations.user_id',
                            'registrations.first_name',
                            'registrations.image'
                        ]);

        // Check if the specific user has liked the post (Return 1 for liked, 0 for not liked)
        $likedStatus = 0;
        if ($request->has('user_id')) {
            $likedStatus = Like::where('blog_id', $blog_id)
                            ->where('user_id', $request->user_id)
                            ->where('likestatus', 1)
                            ->exists() ? 1 : 0;
        }

        // Fetch comments for the blog with user details from the `register` table
        $comments = Like::where('blog_id', $blog_id)
                        ->whereNotNull('comment')
                        ->where('comment', '!=', '')
                        ->join('registrations', 'registrations.user_id', '=', 'likes.user_id') // Get data from `register`
                        ->join('users', 'users.id', '=', 'likes.user_id') // Validate user exists in `users`
                        ->orderBy('likes.created_at', 'DESC')
                        ->get([
                            'likes.id AS comment_id',
                            'registrations.user_id',
                            'registrations.first_name',
                            'registrations.image',
                            'likes.comment',
                            'likes.created_at'
                        ]);

        return response()->json([
            'likescount' => $likesCount,
            'liked_users' => $likedUsers,
            'liked_status' => $likedStatus, // 1 if user liked, 0 if not
            'comments' => $comments // List of comments with user details
        ]);
    }

    public function updateComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'blog_id' => 'required|exists:blogs,id', // ✅ Ensure the blog exists
            'comment_id' => 'required|exists:likes,id', // ✅ Ensure the comment exists
            'comment' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Code' => 400,
                'Message' => "Validation Error.",
                'Errors' => $validator->errors()
            ], 400);
        }

        // ✅ Ensure the comment exists and belongs to the correct blog & user
        $comment = Like::where('id', $request->comment_id)
                    ->where('user_id', $request->user_id)
                    ->where('blog_id', $request->blog_id) // ✅ Check blog_id
                    ->first();

        if (!$comment) {
            return response()->json(['message' => 'Comment not found for this blog or you are not authorized'], 403);
        }

        // ✅ Update the comment
        $comment->update(['comment' => $request->comment]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => [
                'comment_id' => $comment->id,
                'blog_id' => $comment->blog_id, // ✅ Return blog_id in response
                'user_id' => $comment->user_id,
                'comment' => $comment->comment
            ]
        ], 200);
    }


    public function deleteComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'blog_id' => 'required|exists:blogs,id', // Validate that the blog exists
            'comment_id' => 'required|exists:likes,id' // Ensure the comment exists in likes table
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Code' => 400,
                'Message' => "Validation Error.",
                'Errors' => $validator->errors()
            ], 400);
        }

        // ✅ Ensure the comment exists, belongs to the correct blog, and was posted by the user
        $comment = Like::where('id', $request->comment_id)
                    ->where('user_id', $request->user_id)
                    ->where('blog_id', $request->blog_id)
                    ->first();

        if (!$comment) {
            return response()->json(['message' => 'Comment not found for this blog or you are not authorized'], 403);
        }

        // ✅ Only remove the comment, keep the row
        $comment->update(['comment' => null]);

        return response()->json([
            'message' => 'Comment removed successfully',
            'updated_record' => [
                'comment_id' => $request->comment_id,
                'blog_id' => $request->blog_id,
                'user_id' => $request->user_id,
                'likestatus' => $comment->likestatus, // Keep like status unchanged
                'comment' => null // Ensure comment is removed
            ]
        ], 200);
    }

// public function updateComment(Request $request, $comment_id)
// {
//     $validator = Validator::make($request->all(), [
//         'user_id' => 'required|exists:users,id',
//         'blog_id' => 'required|exists:blogs,id', // ✅ Ensure the blog exists
//         'comment' => 'required|string|max:500'
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['Code' => 400, 'Message' => "Validation Error.", 'Errors' => $validator->errors()], 400);
//     }

//     // ✅ Ensure the comment exists and belongs to the correct blog and user
//     $comment = Like::where('id', $comment_id)
//                   ->where('user_id', $request->user_id)
//                   ->where('blog_id', $request->blog_id)
//                   ->first();

//     if (!$comment) {
//         return response()->json(['message' => 'Comment not found or you are not authorized'], 403);
//     }

//     $comment->update(['comment' => $request->comment]);

//     return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment], 200);
// }

    
//     // In your first function where the password is generated
// public function generatePassword()
// {
//     // Generate a password (for example purposes, we'll generate a random string)
//     $password = Str::random(16);  // You can use your own password generation logic
    
//     // Store the password in the session
//     session(['session_password' => $password]);

//     // Optionally, you can return or use the password in the same function
//     return $password;
// }

// // In your second function where you want to use the password
// public function usePassword()
// {
//     // Retrieve the password from the session
//     $password = session('session_password');
    
//     // Check if password exists in session
//     if ($password) {
//         // Use the password as needed
//         return "Password is: " . $password;
//     } else {
//         return "Password not found in session.";
//   }
// }
    public function deleteUser(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'user_id' => 'required',
        'api_token' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['Code' => "400", 'Message' => "Validation Error.", 'Errors' => $validator->errors()], 400);
    }

    $user_id = $request->input('user_id');
    $api_token = $request->input('api_token');

    // Validate user credentials
    $user = User::where('id', $user_id)->where('api_token', $api_token)->first();
    if (!$user) {
        return response()->json(['Code' => "401", 'Message' => "Unauthorized access."], 401);
    }

    // Generate a random password
    $generated_password = Str::random(8);

    // Store the password in cache for 10 minutes
    Cache::put('delete_password_' . $user_id, $generated_password, now()->addMinutes(10));

    // Send the password to the user's email
    try {
        Mail::to($user->email)->send(new DeleteProfileConfirmationPassword($user, $generated_password));
    } catch (\Exception $e) {
        \Log::error("Email could not be sent: " . $e->getMessage());
    }

    return response()->json([
        'Code' => "200",
        'Message' => 'A password has been sent to your email. Use it to confirm account deletion.'
    ], 200);
}

public function confirmAndDeleteUser(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'user_id' => 'required',
        'delete_password' => 'required',
        'api_token' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['Code' => "400", 'Message' => "Validation Error.", 'Errors' => $validator->errors()], 400);
    }

    $user_id = $request->input('user_id');
    $delete_password = $request->input('delete_password');
    $api_token = $request->input('api_token');

    // Validate user credentials
    $user = User::where('id', $user_id)->where('api_token', $api_token)->first();
    if (!$user) {
        return response()->json(['Code' => "403", 'Message' => "Invalid API token."], 403);
    }

    // Retrieve the stored password from the cache
    $generated_password = Cache::get('delete_password_' . $user_id);
    if (!$generated_password) {
        return response()->json(['Code' => "403", 'Message' => "Password has expired or is invalid."], 403);
    }

    // Compare provided and stored passwords
    if ($delete_password !== $generated_password) {
        return response()->json(['Code' => "403", 'Message' => "Invalid password."], 403);
    }

    // Delete the registration record if it exists
    $registration = Registration::where('user_id', $user->id)->first();
    if ($registration) {
        $registration->delete();
    }

    // Delete the user profile
    $user->delete();

    // Clear the password from the cache
    Cache::forget('delete_password_' . $user_id);

    // Send account deletion confirmation email
    try {
        Mail::to($user->email)->send(new DeleteProfileConfirmation($user));
    } catch (\Exception $e) {
        \Log::error("Email could not be sent: " . $e->getMessage());
    }

    return response()->json([
        'Code' => "200",
        'Message' => 'Your profile has been deleted. A confirmation email has been sent to your email address.'
    ], 200);
}

    
// public function deleteUser(Request $request)
// {
//     // Validate the request
//     $validator = Validator::make($request->all(), [
//         'user_id' => 'required',
//         'api_token' => 'required'
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['Code' => "400", 'Message' => "Validation Error.", 'Errors' => $validator->errors()], 400);
//     } else {
//         $user_id = $request['user_id'];
//         $api_token = $request['api_token'];

//         // Validate user based on user_id and api_token
//         $user_data = validateUser($user_id, $api_token);

//         if ($user_data) {
//             // Find the user by ID
//             $user = User::find($user_id);

//             if (!$user) {
//                 return response()->json(['Code' => "404", 'Message' => "User not found."], 404);
//             }

//             // Find the related registration record
//             $registration = Registration::where('user_id', $user->id)->first();

//             // Delete the registration record if it exists
//             if ($registration) {
//                 $registration->delete();
//             }

//             // Delete the user profile
//             $user->delete();

//             // Send confirmation email
//             $toEmail = $user->email;
//             try {
//                 Mail::to($toEmail)->send(new DeleteProfileConfirmation($user));
//             } catch (\Exception $e) {
//                 \Log::error("Email could not be sent: " . $e->getMessage());
//                 return response()->json(['Code' => "500", 'Message' => "User deleted, but confirmation email could not be sent."], 500);
//             }

//             return response()->json([
//                 'message' => 'Your profile has been deleted. A confirmation email has been sent to your email address.'
//             ], 200);
//         } else {
//             return response()->json(['Code' => "401", 'Message' => "Unauthorized access."], 401);
//         }
//     }
// }

    public function checkEmail(Request $request)
    {
        // Validate the input email
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        // Check if the email exists in 'users' or 'register' tables
        $emailExistsInUsers = User::where('email', $email)->exists();
        $emailExistsInRegister = DB::table('registrations')->where('email', $email)->exists();

        // Prepare the response
        if ($emailExistsInUsers || $emailExistsInRegister) {
            return response()->json([
                'message' => 'Email exists in the system.',
                'exists_in_users' => $emailExistsInUsers,
                'exists_in_register' => $emailExistsInRegister,
            ]);
        }

        return response()->json([
            'message' => 'Email does not exist in the system.',
        ], 404);
    }

    #Function to get feedback form - 27/12/2025
    // public function geteventFeedbackForm(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id'   => 'required|integer',
    //         'api_token' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => 400,
    //             'message' => 'Validation Error',
    //             'errors'  => $validator->errors(),
    //         ], 400);
    //     }

    //     $user_id   = $request->user_id;
    //     $api_token = $request->api_token;

    //     if (!validateUser($user_id, $api_token)) {
    //         return response()->json([
    //             'status'  => 401,
    //             'message' => 'Unauthorized access',
    //         ], 401);
    //     }

    //     $eventIds = Event::where('feedback_status', 1)
    //         ->orderBy('id', 'DESC')
    //         ->pluck('id');

    //     $feedbackQuestions = EventFeedBackQuestion::whereIn('event_id', $eventIds)
    //         ->where('is_required', 1)
    //         ->orderBy('id', 'DESC')
    //         ->get();

    //     if ($feedbackQuestions->isEmpty()) {
    //         return response()->json([
    //             'status'  => 404,
    //             'message' => 'No Records Found',
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 200,
    //         'data'   => $feedbackQuestions,
    //     ], 200);
    // }
    public function geteventFeedbackForm(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id'   => 'required|integer',
        'api_token' => 'required|string',
        'event_id'  => 'required|integer'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 400,
            'message' => 'Validation Error',
            'errors'  => $validator->errors(),
        ], 400);
    }

    if (!validateUser($request->user_id, $request->api_token)) {
        return response()->json([
            'status'  => 401,
            'message' => 'Unauthorized access',
        ], 401);
    }

    // ✅ Check event + feedback_status
    $event = Event::where('id', $request->event_id)
        ->where('feedback_status', 1)
        ->first();

    if (!$event) {
        return response()->json([
            'status'  => 403,
            'message' => 'Feedback is not enabled for this event.',
        ], 403);
    }

    $feedbackQuestions = EventFeedBackQuestion::where('event_id', $event->id)
        ->where('is_required', 1)
        ->orderBy('id', 'DESC')
        ->get();

    if ($feedbackQuestions->isEmpty()) {
        return response()->json([
            'status'  => 404,
            'message' => 'No Feedback Questions Found',
        ], 404);
    }

    return response()->json([
        'status' => 200,
        'data'   => $feedbackQuestions,
    ], 200);
}

    #Function to get feedback form - 27/12/2025
    public function submitFeedbackForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'               => 'required|integer',
            'api_token'             => 'required|string',
            'event_id'              => 'required|exists:events,id',
            'role'                  => 'required|string',
            'answers'               => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer',
            'answers.*.answer'      => 'required|string',
            'suggestion'            => 'required|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 400,
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ], 400);
        }

        if (!validateUser($request->user_id, $request->api_token)) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthorized access',
            ], 401);
        }

        DB::beginTransaction();

        try {
                EventFeedback::create([
                    'event_id'    => $request->event_id,
                    'user_id'     => $request->user_id,
                    'role'        => $request->role,
                    'answer'      => json_encode($request->answers),
                    'suggestion'  => $request->suggestion,
                ]);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Feedback submitted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    # Function to download certificate - 27/12/2025
    // public function eventCertificateDownload(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user_id'   => 'required|integer',
    //         'api_token' => 'required|string',
    //         'event_id'  => 'required|exists:events,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => 400,
    //             'message' => 'Validation Error',
    //             'errors'  => $validator->errors(),
    //         ], 400);
    //     }

    //     if (!validateUser($request->user_id, $request->api_token)) {
    //         return response()->json([
    //             'status'  => 401,
    //             'message' => 'Unauthorized access',
    //         ], 401);
    //     }

    //     // Check registration
    //     $registration = Eventregister::where('user_id', $request->user_id)
    //         ->where('event_id', $request->event_id)
    //         ->first();

    //     if (!$registration) {
    //         return response()->json([
    //             'status'  => 403,
    //             'message' => 'You are not registered for this event.',
    //         ], 403);
    //     }

    //     // Get event
    //     $event = Event::find($request->event_id);
    //     if (!$event) {
    //         return response()->json([
    //             'status'  => 404,
    //             'message' => 'Event not found.',
    //         ], 404);
    //     }

    //     if ($event->certi_status != 1) {
    //         return response()->json([
    //             'status'  => 403,
    //             'message' => 'Certificate is not enabled for this event.',
    //         ], 403);
    //     }

    //     // Certificate template
    //     $templatePath = public_path('uploads/images/certificates/' . $event->certi);
    //     if (!file_exists($templatePath)) {
    //         return response()->json([
    //             'status'  => 404,
    //             'message' => 'Certificate template not found.',
    //         ], 404);
    //     }

    //     // Get participant name
    //     $name = trim(($registration->user->firstname ?? '') . ' ' . ($registration->user->lastname ?? '')) ?: 'Participant';
    //     $certiDescription = $event->cert_description ?? '';
    //     $eventDate = $event->start_date ?? '';
    //     $eventName = $event->title ?? '';

    //     // Generate & download PDF
    //     return $this->generateCertificate($templatePath, $name, $eventName ,$certiDescription,$eventDate);
    // }
      public function eventCertificateDownload(Request $request)
    {
        // ================= VALIDATION =================
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|integer',
            'api_token' => 'required|string',
            'event_id'  => 'required|exists:events,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 400,
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ], 400);
        }

        // ================= AUTH CHECK =================
        if (!validateUser($request->user_id, $request->api_token)) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthorized access',
            ], 401);
        }

        // ================= REGISTRATION CHECK =================
        $registration = Eventregister::where('user_id', $request->user_id)
            ->where('event_id', $request->event_id)
            ->first();

        if (!$registration) {
            return response()->json([
                'status'  => 403,
                'message' => 'You are not registered for this event.',
            ], 403);
        }

        // ================= EVENT CHECK =================
        $event = Event::find($request->event_id);
        if (!$event) {
            return response()->json([
                'status'  => 404,
                'message' => 'Event not found.',
            ], 404);
        }

        if ($event->certi_status != 1) {
            return response()->json([
                'status'  => 403,
                'message' => 'Certificate is not enabled for this event.',
            ], 403);
        }

        // ================= TEMPLATE CHECK =================
        $templatePath = public_path('uploads/images/certificates/' . $event->certi);
        if (!file_exists($templatePath)) {
            return response()->json([
                'status'  => 404,
                'message' => 'Certificate template not found.',
            ], 404);
        }

        // ================= DATA =================
        $name = trim(($registration->user->firstname ?? '') . ' ' . ($registration->user->lastname ?? ''));
        $name = $name ?: 'Participant';

        $certiDescription = $event->cert_description ?? '';
        $eventDate        = $event->start_date ?? '';
        $eventName        = $event->tittle ?? '';

        // ================= GENERATE CERTIFICATE =================
        $fileName = $this->generateCertificate(
            $templatePath,
            $name,
            $eventName,
            $certiDescription,
            $eventDate,
            $request->user_id,
            $request->event_id
        );

        // ================= RESPONSE =================
        return response()->json([
            'status'  => 200,
            'message' => 'Certificate generated successfully',
            'data'    => [
                'name'=>$name,
                'event_name'=> $eventName,
                'event_date'=> $eventDate,
                'certificate_description'=>$certiDescription,
                'certificate_path' => $templatePath,
            ],
        ]);
    }

    /**
     * Generate Certificate PDF
     * Date: 01/02/2026
     */
    private function generateCertificate(
        $templatePath,
        $name,
        $eventName,
        $certiDescription,
        $eventDate,
        $userId,
        $eventId
    ) {
       // $pdf = new Fpdi();

        // Import template
        // $pdf->setSourceFile($templatePath);
        // $tpl  = $pdf->importPage(1);
        // $size = $pdf->getTemplateSize($tpl);

        // $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        // $pdf->useTemplate($tpl);

        // ================= NAME =================
        // $pdf->SetFont('Helvetica', 'B', 18);
        // $pdf->SetXY(65, 128);
        // $pdf->Cell(120, 10, strtoupper($name), 0, 0, 'C');

        // ================= DESCRIPTION =================
        // $pdf->SetFont('Helvetica', '', 12);
        // $pdf->SetXY(60, 145);
        // $pdf->MultiCell(
        //     130,
        //     7,
        //     strip_tags($certiDescription),
        //     0,
        //     'C'
        // );

        // ================= LOCATION =================
        // $pdf->SetFont('Helvetica', '', 11);
        // $pdf->SetXY(5, 264);
        // $pdf->Cell(20, 8, 'Doha - Qatar');

        // ================= DATE =================
        // $pdf->SetXY(5, 269);
        // $pdf->Cell(20, 8, $eventDate);

        // ================= SAVE FILE =================
         $fileName = 'certificate_' . $userId . '_' . $eventId . '.pdf';
         $savePath = public_path('uploads/certificates/' . $fileName);

        // Ensure directory exists
        // if (!file_exists(public_path('uploads/certificates'))) {
        //     mkdir(public_path('uploads/certificates'), 0777, true);
        // }

        //$pdf->Output($savePath, 'F');

        return $fileName;
    }
    
    # Function for generate certificate - 01/02/2026
    // public function generateCertificate($templatePath, $name , $eventName ,$certiDescription ,$eventDate)
    // {
    //     $pdf = new Fpdi();

    //     $pdf->setSourceFile($templatePath);
    //     $tpl  = $pdf->importPage(1);
    //     $size = $pdf->getTemplateSize($tpl);

    //     $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    //     $pdf->useTemplate($tpl);

    //     // ===== Name =====
    //     $pdf->SetFont('Helvetica', 'B', 18);
    //     $pdf->SetXY(65, 128);
    //     $pdf->Cell(120, 10, strtoupper($name), 0, 0, 'C');
        
    //     // ================= Certificate Decription ================= //
    //     $certiDescription = strip_tags($certiDescription);
    //     $pdf->SetFont('Helvetica', '', 12);
    //     $pdf->SetXY(60, 145);

    //     $pdf->MultiCell(
    //         130,
    //         7,
    //         $certiDescription,
    //         0,
    //         'C'
    //     );

        
    //     // ================= Location ================= //
    //     $pdf->SetFont('Helvetica', '', 11);
    //     $pdf->SetXY(5, 264);
    //     $pdf->Cell(20, 8, 'Doha - Qatar');
        
    //     // ================= DATE ================= //
    //     $pdf->SetFont('Helvetica', '', 11);
    //     $pdf->SetXY(5, 269);
    //     $pdf->Cell(20, 8, $eventDate, 0, 0, 'L');
        
    //     return response()->streamDownload(function () use ($pdf) {
    //         echo $pdf->Output('S');
    //     }, 'event-certificate.pdf', [
    //         'Content-Type' => 'application/pdf',
    //     ]);
    // }
    
    // public function generateCertificate($templatePath, $name, $eventName)
    // {
    //     $pdf = new Fpdi();

    //     $pdf->setSourceFile($templatePath);
    //     $tpl  = $pdf->importPage(1);
    //     $size = $pdf->getTemplateSize($tpl);

    //     $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    //     $pdf->useTemplate($tpl);

    //     // ===== Name =====
    //     $pdf->SetFont('Helvetica', 'B', 18);
    //     $pdf->SetXY(65, 128);
    //     $pdf->Cell(120, 10, strtoupper($name), 0, 0, 'C');

    //     return response()->streamDownload(function () use ($pdf) {
    //         echo $pdf->Output('S');
    //     }, 'event-certificate.pdf', [
    //         'Content-Type' => 'application/pdf',
    //     ]);
    // }


        
}