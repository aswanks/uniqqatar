<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\News;
use App\Models\Offer;
use App\Models\Advertisment;
use App\Models\Registarion;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use DB;
use Str;
use Mail;





class AuthController extends Controller
{

    public function doLogin(Request $request)
    {
        $events = Event::select('id', 'tittle', 'contant', 'start_date', 'start_time', 'end_date', 'end_time', 'image','evnt_pdf' ,'status', 'created_at', 'updated_at', 'address')
        ->orderBy('start_date','asc')->get();
        //dd($event);
        $events_new = Event::select('id', 'tittle', 'contant', 'start_date', 'start_time', 'end_date', 'end_time', 'image','evnt_pdf' ,'status', 'created_at', 'updated_at', 'address')
            //->skip(1)
            ->orderBy('id', 'desc')
            ->take(1)->get();
        $News = News::select('id', 'tittle', 'image', 'description')
        ->skip(1)
         ->orderBy('id', 'desc')
         ->take(1)->get();
         $adv = Advertisment::select('id','title','image','advlink')->orderBy('id','desc')->first();




         $userdata = array(
            'email' => $request ->post('email'),
             'password' =>$request ->post('password')
           );

              // $credentials =$request->only('email');

           if(Auth::attempt($userdata))
           {

            $users = Registration::where('user_id',Auth::user()->id)->get();

                return view('frondend.customer.dashboard',compact('events','events_new','News','users','adv'))->withSuccess('You are Successfully Logged In');

           }
           else{
                return view('frondend.customer.login') -> withError('You are entered inavalid email');

           }

    }
    public function index()
    {
        return view('frondend.customer.login');
    }
    public function offer()
    {
        $Offers = Offer::select('id', 'category_name','slug', 'image', 'details')
         ->orderBy('id', 'desc')->get();
        return view('frondend.customer.offerpage',compact('Offers'));
    }
    public function postoffer($slug, Request $request)
    {

        $Offers = Offer::where('slug',$slug)->select('id', 'category_name','slug', 'image', 'details')->get();
        $user = Registration::where('user_id',Auth::user()->id )->first();
        $uniq_id =$user->uniq_id;
         $status =$user->status;
        //dd($status);
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
            'membership_validity_to' => $now->addYear(1)->toDateString(),
        ];

         $member = Registration::where('uniq_id', $uniq_id)->first();

        if ($member) {
            $registration_date = $member->registration_date;
            $now = Carbon::now()->toDateString();
            // $difference = $now->diffInDays($registration_date);

            $formatted_registration_date = Carbon::parse($registration_date);
            $formatted_now = Carbon::parse($now);
            $difference = $formatted_now->diffInDays($formatted_registration_date);
            // dd($difference);
            $to_expire = 730 - $difference;
            // dd($difference);
            if ($difference > 730) {
                return Redirect::route('memebers.renewal')->with('error', 'Your Registration was expired to ' . abs($to_expire) . ' days, Please Renew your account');
            } else if($status == 1) {
                return view('frondend.customer.offerviewpage',compact('Offers','user','approve'));
            }else{
                return Redirect::back()->withError('You are not member(Not approved)');
            }
        }

    }
    public function membershipcard()
    {
        $user = Registration::where('user_id',Auth::user()->id )->first();
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
            'membership_validity_to' => $now->addYear(1)->toDateString(),
        ];
        return view('frondend.customer.membershipcard',compact('approve'));

    }
    public function forgotPassword()
    {
        return view('frondend.customer.forgot-password');
    }
    public function checkForgotAccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        $token = Str::random(64);
        $password_resets=PasswordReset::where('email',$request->email)->first();
        if( $password_resets!= null )
        {  $password_resets->token =$token;
           $password_resets->created_at= Carbon::now();
           $password_resets->save();
        }else{
         DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
          ]);
        }
        $data =[
            'email'=>$request->email,
            'token'=>$token,
        ];
       Mail::send(['html'=>'templates.reset_password'],$data,function($message) use($request){
            $message->to($request->email);
            $message->From(config('custom.email_from'));
            $message->subject('Reset Password');
            return view('frondend.customer.forgot-password');
        });
        return back()->with('warning', 'We have e-mailed your password reset link!');
    }

    public function verifyResetPasswordLink(Request $request,$token)
    {
        $record = DB::table('password_resets')
        ->where([
          'email' => $request->email,
          'token' => $request->token
        ])->first();
        if(!$record){
            // return view('frontend.account.forgot-password')->withError('error', 'Invalid token!');
            Session::flash('warning', "Invalid password reset link");
            return view('frondend.customer.forgot-password');
        }
        return view('frondend.customer.passwordreset');
    }
    public function doResetPassword(Request $request)
    //function to change password and save the new one
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6',

        ]);
        $user = User::where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email'=> $request->email])->delete();

        return redirect('/login')->with('success', 'Your password has been changed!');
    }
    public function password($id)
    {
        $user = User::orderBy('id','ASC')->get();
        $user =User::findOrfail($id);
        return view('frondend.customer.password',compact('user'));
    }
    public function updatePassword(Request $request,$id)
    {
        $request->validate([
            'password'=>'required_with:confirmpassword|same:confirmpassword|min:6',
            'confirmpassword'=>'required|min:6',
        ]);
        $user =User::find($id);
        $user->password = Hash::make($request->password);
        $user->save();
        return back()->with('success','Password Changed Succesfully !');
    }
    
    public function logout()
      {
       Auth::logout();
       return Redirect::intended('/');
      }



}
