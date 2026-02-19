<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Request;
use App\Mail\RegistrationMail;
use App\Mail\ApproveMail;
use App\Mail\RenewalMail;
use App\Mail\ShareMemberDetailsMail;
use App\Mail\MemberPasswordResetMail;
use App\Mail\SendCustomMail;
use App\Exports\MemberRegistrationExport;
use App\Exports\RegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use Hash;
use Str;

class RegistrationController extends Controller
{
    # Function to List all Members - 18/06/2021
    public function registrationControllerIndex()
    {
        $key      = request('searchquery');
        $reg_date = request('regi_date');

        $query = DB::table('registrations as t1')
            ->leftJoin('registrations as t2', 't1.reffered_by', '=', 't2.id')
            ->select(
                't1.id',
                't1.first_name',
                't1.last_name',
                't1.image',
                't1.gender',
                't1.blood_grp',
                't1.mob_no',
                't1.dob',
                't1.email',
                't1.destination',
                't1.employer_current',
                't1.uniq_id',
                't1.status',
                't1.confirm_from_member',
                't2.first_name as rfname',
                't2.last_name as rlname',
                't1.registration_date'
            )
            ->orderByDesc('t1.id');

        /* ================= SEARCH FILTER ================= */
        if (!empty($key)) {

            // Detect status keyword
            $statusMap = [
                'PENDING'  => 0,
                'APPROVED' => 1,
                'REJECTED' => 2,
            ];

            $keyUpper = strtoupper($key);
            $status   = $statusMap[$keyUpper] ?? null;

            $query->where(function ($q) use ($key, $status) {
                $q->where('t1.first_name', 'like', "%{$key}%")
                ->orWhere('t1.last_name', 'like', "%{$key}%")
                ->orWhere('t1.uniq_id', 'like', "%{$key}%")
                ->orWhere('t1.mob_no', 'like', "%{$key}%")
                ->orWhere('t1.dob', 'like', "%{$key}%")
                ->orWhere('t1.email', 'like', "%{$key}%")
                ->orWhere('t1.destination', 'like', "%{$key}%")
                ->orWhere('t1.employer_current', 'like', "%{$key}%");

                // Apply status filter ONLY if keyword matches
                if ($status !== null) {
                    $q->orWhere('t1.status', $status);
                }
            });
        }

        /* ================= DATE FILTER ================= */
        $query->when($reg_date, function ($q) use ($reg_date) {
            $q->whereDate('t1.registration_date', $reg_date);
        });

        $members = $query->paginate(10);

        return view('backend/members/index', compact('members', 'key'));
    }

    //searched mebers pdf list
    public function searchpdf()
    {
        
        $key = Request()->searchquery;
        $query = DB::table('registrations as t1')
            ->leftJoin('registrations AS t2', 't1.reffered_by', '=', 't2.id')
            ->select(
                't1.id',
                't1.first_name',
                't1.last_name',
                't1.gender',
                't1.mob_no',
               // 't1.alter_mob_no',
                't1.dob',
                't1.email',
               // 't1.passport',
                't1.destination',
                't1.employer_current',
                't1.uniq_id',
                't1.status',
                't1.confirm_from_member',
                't2.first_name as rfname',
                't2.last_name as rlname',
            )->latest('id');
        if (Request::exists('searchquery')) {
            
            $status = 2;
            if (strtoupper($key) == 'PENDING') {
                $status = 0;
            } else if (strtoupper($key) == 'APPROVED') {
                $status = 1;
            } else if (strtoupper($key) == 'REJECTED') {
                $status = 2;
            } else {
                $status = 2;
            }
            $query->where('t1.first_name', 'like', '%' . $key . '%')
            ->orWhere('t1.last_name', 'like', '%' . $key . '%')
            ->orWhere('t1.uniq_id', 'like', '%' . $key . '%')
            ->orWhere('t1.mob_no', 'like', '%' . $key . '%')
            //->orWhere('t1.alter_mob_no', 'like', '%' . $key . '%')
            ->orWhere('t1.dob', 'like', '%' . $key . '%')
            ->orWhere('t1.email', 'like', '%' . $key . '%')
           // ->orWhere('t1.passport', 'like', '%' . $key . '%')
            ->orWhere('t1.destination', 'like', '%' . $key . '%')
            ->orWhere('t1.employer_current', 'like', '%' . $key . '%')
            ->orWhere('t1.status', $status);
        }
        $members = $query->get();
       
        $pdf = PDF::loadView('emails.searchpdfmembers', compact('members'));
        return $pdf->download('searchpdfmembers.pdf');
    }
    # Admin to search Members - 27/07/2021
    // public function getSearchMember()
    // {
    //     $key = Request()->searchquery;
    //     $status = 2;
    //     if (strtoupper($key) == 'PENDING') {
    //         $status = 0;
    //     } else if (strtoupper($key) == 'APPROVED') {
    //         $status = 1;
    //     } else if (strtoupper($key) == 'REJECTED') {
    //         $status = 2;
    //     } else {
    //         $status = 2;
    //     }
    //     $members = DB::table('registrations as t1')
    //         ->leftJoin('registrations AS t2', 't1.reffered_by', '=', 't2.id')
    //         ->select(
    //             't1.id',
    //             't1.first_name',
    //             't1.last_name',
    //             't1.gender',
    //             't1.mob_no',
    //             't1.alter_mob_no',
    //             't1.email',
    //             //'t1.passport',
    //             't1.destination',
    //             't1.employer_current',
    //             't1.uniq_id',
    //             't1.status',
    //             't2.first_name as rfname',
    //             't2.last_name as rlname',
    //         )
    //         ->where('t1.first_name', 'like', '%' . $key . '%')
    //         ->orWhere('t1.last_name', 'like', '%' . $key . '%')
    //         ->orWhere('t1.uniq_id', 'like', '%' . $key . '%')
    //         ->orWhere('t1.mob_no', 'like', '%' . $key . '%')
    //         ->orWhere('t1.alter_mob_no', 'like', '%' . $key . '%')
    //         ->orWhere('t1.email', 'like', '%' . $key . '%')
    //         ->orWhere('t1.passport', 'like', '%' . $key . '%')
    //         ->orWhere('t1.destination', 'like', '%' . $key . '%')
    //         ->orWhere('t1.employer_current', 'like', '%' . $key . '%')
    //         ->orWhere('t1.status', $status)
    //         ->paginate(10);
    //     //return Redirect::route('members.index',compact('members'));
    //     return view('backend/members/index', compact('members'));
    // }
    
    # Function to Export Members - 17/01/2026
    public function exportExcel()
    {
        $key      = trim(request()->get('searchquery'));
        $reg_date = request()->get('regi_date');

        $fileName = 'All_Members_At_' . now()->format('Y-m-d') . '.xlsx';

        if ($key) {
            $fileName = 'Members_' . Str::slug($key, '_') . '_' . now()->format('Y-m-d') . '.xlsx';
        }

        if ($reg_date) {
            $fileName = 'Members_' . $reg_date . '_' . now()->format('Y-m-d') . '.xlsx';
        }

        return Excel::download(
            new MemberRegistrationExport($key, $reg_date),
            $fileName
        );
    }

    # Function to Approve Member - 18/06/2021
    public function registrationControllerApprove()
    {
        $id = Request()->id;
        $member = Registration::findOrFail($id);
        if ($member) {
            $now = Carbon::now();
            Registration::where('id', $id)->update([
                'status' => 1,
                'registration_date' => $now->toDateString()
            ]);

            $to_email = $member->email;
            $approve = [
                'to_email' => $member->email,
                'FirstName' => $member->first_name,
                'LastName' => $member->last_name,
                'imgName' => $member->image,
                'bloodGroup' => $member->blood_grp,
                'qid' => $member->qid,
                'emplyoyer_crnt' => $member->employer_current,
                'uniq_id' => $member->uniq_id,
                'membership_validity_from' => $now->toDateString(),
                'membership_validity_to' => $now->addYear(2)->toDateString(),
            ];
            Mail::to($to_email)->send(new ApproveMail($approve));            
            $pdf = PDF::loadView('emails.ApproveSendMail', compact('approve')); //->setPaper('a4')
            //return $pdf->download('AdmitCard.pdf');
            Mail::send('emails.ApproveSendMail', compact('approve'), function ($message) use ($approve, $pdf) {
                //$message->from('info@**********');
                $message->to($approve['to_email']);
                $message->subject('Approve Mail');
                //Attach PDF doc
                $message->attachData($pdf->output(), 'MembershipCard.pdf');
            });
            //$members = Registration::latest('id')->paginate(5);
            return Redirect::route('members.index')->with('success', 'Member Details are Approved. Email sent.');
            //return view('backend/members/index', compact('members'))->with('success', 'Member Details are Approved');
        } else {
            return Redirect::route('members.index')->with('error', 'Invalid Request');
        }
    }
    public function registrationControllerMembershipResend()
    {
        $id = Request()->id;
        $member = Registration::findOrFail($id);
        if ($member) {
            $to_email = $member->email;
            $approve = [
                'to_email' => $member->email,
                'FirstName' => $member->first_name,
                'LastName' => $member->last_name,
                'imgName' => $member->image,
                'bloodGroup' => $member->blood_grp,
                'qid' => $member->qid,
                'emplyoyer_crnt' => $member->employer_current,
                'uniq_id' => $member->uniq_id,
                'membership_validity_from' => Carbon::parse($member->registration_date)->toDateString(),
                'membership_validity_to' => Carbon::parse($member->registration_date)->addYear($member->registration_date<2024 ? 1 : 2)->toDateString(),
            ];          
            $pdf = PDF::loadView('emails.ApproveSendMail', compact('approve')); //->setPaper('a4')
         
            Mail::send('emails.ApproveSendMail', compact('approve'), function ($message) use ($approve, $pdf) {
                //$message->from('info@**********');
                $message->to($approve['to_email']);
                $message->subject('Resend Mebership Card Mail');
                //Attach PDF doc
                $message->attachData($pdf->output(), 'MembershipCard.pdf');
            });
            //$members = Registration::latest('id')->paginate(5);
            return Redirect::route('members.index')->with('success', 'Member Details are Resend. Email sent.');
            //return view('backend/members/index', compact('members'))->with('success', 'Member Details are Approved');
        } else {
            return Redirect::route('members.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Reject Member - 18/06/2021
    public function registrationControllerReject()
    {
        $id = Request()->id;
        $member = Registration::findOrFail($id);
        if ($member) {
            Registration::where('id', $id)->update([
                'status' => 2,
            ]);
            //$members = Registration::latest('id')->paginate(5);
            return Redirect::route('members.index')->with('success', 'Member Details are Rejected');
        } else {
            return Redirect::route('members.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Delete Member - 23/10/2021
    public function destroy($id)
    {
        $registration = Registration::findOrFail($id);
        if ($registration) {
            User::where('id',$registration->user_id)->delete();
            $path_event = 'public/uploads/members/' . $registration->image;
            @unlink($path_event);
            $path_event = 'public/uploads/members/idcards/' . $registration->idcardimage;
            @unlink($path_event);
            $path_event = 'public/uploads/members/qidfiles/' . $registration->qidfile;
            @unlink($path_event);
            Registration::destroy($id);
            return Redirect::route('members.index')->with('success', 'Member Deleted Successfully');
        } else {
            return Redirect::route('members.index')->with('error', 'Invalid Request');
        }
    }
    # Function to Load Registration Page - 19/06/2021
    public function registration_confirm($id,$token)
    {
        $reg=Registration::where('id',$id)->first();
        if(isset($reg))
        {
            if($token==md5($reg->created_at))
            {
                $reg->confirm_from_member='confirmed';
                $reg->save();
            }
            return redirect()->route('members.register')->with('success','Member details confirmed.');
        }
        else
        {
            return redirect()->route('members.register')->with('error','Member details not confirmed.');
        }        
    }
    # Function to Load Registration Page - 19/06/2021
    public function registrationControllerRegistrationForm()
    {
        $refferedby = Registration::oldest('first_name')
            ->where('status', '=', '1')
            ->select('id', 'first_name', 'last_name')
            ->get();
        return view('frondend/home/register', compact('refferedby'));
    }

    # Function to Do Registration - 19/06/2021
    public function registrationControllerRegistration()
    {
        $chkmailexist = User::where('email', '=', Request::get('email'))
            ->Where(function ($query) {
                $query->where('status', '=', '1')
                    ->orwhere('status', '=', '0');
            })
            ->first();
        //print_r($chkmailexist);
        $chkqidexist = Registration::where('qid', '=', Request::get('qid'))
            ->Where(function ($query) {
                $query->where('status', '=', '1')
                    ->orwhere('status', '=', '0');
            })
            ->first();
        //print_r($chkqidexist);
        $validate = Validator::make(Request::all(), [
            'FirstName' => 'required',
            'LastName' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'bloodGroup' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            //'mob_no_alter' => 'required',
            //'whatsapp' => 'required',
            //'passport' => 'required',
            'qid' => 'required',
            //'qid_expiration' => 'required',
            'address_qatar' => 'required',
            'address_ind' => 'required',
            'emplyoyer_crnt' => 'required',
            'designation' => 'required',
            'N_regNo' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'qidfile' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'idcardimage' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);
        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else if ($chkmailexist) {
            return Redirect::back()->with('error', 'Email already exists.');
        } else if ($chkqidexist) {
            return Redirect::back()->with('error', 'Qid already exists.');
        } else {
            $now = Carbon::now();
            $orginal_img_name = Request::file('image')->getClientOriginalName();
            $extension_img = Request::file('image')->getClientOriginalExtension();
            $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
            $fullPath_img = '/public/uploads/members' . $imgName;
            Request::file('image')->move(base_path() . '/public/uploads/members', $imgName);

            $orginal_idcard = Request::file('idcardimage')->getClientOriginalName();
            $extension_idcard = Request::file('idcardimage')->getClientOriginalExtension();
            $imgIdCard = setNewNameForPhoto($orginal_idcard) . '.' . generateRandomString(2, 15) . '.' . $extension_idcard;
            Request::file('idcardimage')->move(base_path() . '/public/uploads/members/idcards', $imgIdCard);

            $orginal_qidfile = Request::file('qidfile')->getClientOriginalName();
            $extension_qidfile = Request::file('qidfile')->getClientOriginalExtension();
            $imgQidFile = setNewNameForPhoto($orginal_qidfile) . '.' . generateRandomString(2, 15) . '.' . $extension_qidfile;
            Request::file('qidfile')->move(base_path() . '/public/uploads/members/qidfiles', $imgQidFile);
            
            // Generate a random password
            $password = Str::random(6);
             
            $user = new User;
            $user->firstname = Request::get('FirstName');
            $user->lastname = Request::get('LastName');
            $user->email = Request::get('email');
            $user->qid = Request::get('qid');
            $user->password = Hash::make($password);
            $user->status = 1;
            $user->user_type = 2;
            $user->remember_token=1;
            $user->save();
            
            $register = new Registration;
            $register->user_id = $user->id;
            $register->first_name = Request::get('FirstName');
            $register->last_name = Request::get('LastName');
            $register->dob = Request::get('dob');
            $register->image = $imgName;
            $register->gender = Request::get('gender');
            $register->email = Request::get('email');
            $register->mob_no = Request::get('mobile');
           // $register->alter_mob_no = Request::get('mob_no_alter');
            $register->whatsapp = Request::get('whatsapp');
           // $register->passport = Request::get('passport');
            //$register->ind_contact_no = Request::get('india_no');
            $register->qid = Request::get('qid');
            //$register->qid_expiration = Request::get('qid_expiration');
            $register->address_qatar = Request::get('address_qatar');
            $register->address_ind = Request::get('address_ind');
            //$register->employer_pre = Request::get('emplyoyer_pre');
            $register->employer_current = Request::get('emplyoyer_crnt');
            $register->destination = Request::get('designation');
            //new updates on 31-07-2021
            $register->qidfile = $imgQidFile;
            $register->idcardimage = $imgIdCard;
            $register->designationifothers = Request::get('designationothers');
            ///
            $register->reg_no = Request::get('N_regNo');
            $register->blood_grp = Request::get('bloodGroup');

            $register->registration_date = $now->toDateString();
            $register->expiry_date = $now->addDays(730);

            $register->status = 0;
            $register->reffered_by = Request::get('refferedby');
            $register->save();
            $uniqId = "UN" . str_pad($register->id, 4, '0', STR_PAD_LEFT);
            $register->uniq_id = $uniqId;
            $register->update();
            //Mail Sending
            $to_email = Request::get('email');
            $registration = $register;
            try {
                Mail::to($to_email)->send(new RegistrationMail($registration, $password));
            } catch (\Exception $e) {

                return Redirect::back()->with('success', ' Registration Successfully Completed. ');
            }
             $this->sendNotification($registration);

            //end mail sending
            return Redirect::back()->with('success', ' Registration Successfully Completed. Mail sent to your email ,please check.');
        }
    }
    
    # Function to Password Reset Form - 04/12/2025
    public function showPasswordResetForm($id)
    {
        $member = Registration::find($id);
        return view('backend/members/reset-password',compact('member'));
    }

    # Function to Save New Password - 04/12/2025
    public function saveNewPassword(Request $request, $id)
    {
        $validate = Validator::make(Request::all(), [
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);
        
        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {

        $member = Registration::findOrFail($id);

        $user = User::where('qid', $member->qid)->first(); 

        if (!$user) {
            return back()->with('error', 'User not found');
        }

        $user->password = Hash::make(Request::get('new_password'));
        $user->save();

        $to_email = $user->email;
        $password = Request::get('new_password');
        $member = $member;
        
        try {
            Mail::to($to_email)->send(new MemberPasswordResetMail($member, $password));

            return Redirect::back()->with('success', 'Password updated successfully');
        } catch (\Exception $e) {

            return Redirect::back()->with('error', 'Mail sending failed, but password updated. Error: ' . $e->getMessage());
        }

    }

    }
    
    public  function sendNotification()
    {
        Log::info("sendNotification() called");

        $url = "https://uniq-rho.vercel.app/send-notification";
        $members = Registration::all();

        if ($members->isEmpty()) {
            Log::info("No members found.");
            return;
        }

        foreach ($members as $member) {
            $user = User::find($member->user_id);

            if (!$user || !$user->fcm_token) {
                Log::info("Skipping user ID {$member->user_id}, FCM token missing or user not found");
                continue;
            }

            $registrationDate = Carbon::parse($member->registration_date);
            $expiryDate = $registrationDate->copy()->addYears(1);
            $daysLeft = now()->diffInDays($expiryDate, false);

            Log::info("User ID {$user->id}, days left: {$daysLeft}");

            if ($daysLeft <= 30 && $daysLeft >= 0) {
                $payload = [
                    "title" => "Membership Expiry Reminder",
                    "body" => "Hi {$user->firstname}, your membership will expire in {$daysLeft} days. Please renew soon.",
                    "token" => $user->fcm_token,
                    "data" => [
                        "type" => "membership_expiry",
                        "days_left" => $daysLeft,
                        "user_id" => $user->id,
                    ],
                ];

                try {
                    $response = Http::post($url, $payload);
                    Log::info("Notification response for {$user->id}: " . $response->body());
                } catch (\Exception $e) {
                    Log::error("Error sending notification to {$user->id}: " . $e->getMessage());
                }
            }
        }

        Log::info("sendNotification() finished");
    }


    # Function to View more details about Member - 19/06/2021
    public function registrationControllerViewMore($id)
    {
        //$member = Registration::findOrFail($id);
        $member = DB::table('registrations as t1')
            ->leftJoin('registrations AS t2', 't1.reffered_by', '=', 't2.id')
            ->where('t1.id', '=', $id)
            ->select(
                't1.*',
                't2.first_name as rfname',
                't2.last_name as rlname',
            )
            ->first();
        return view('backend/members/view_more', compact('member'));
    }

    # Function to edit details about Member - 05/06/2022
    public function registrationedit($id)
    {
        $member = DB::table('registrations as t1')
            ->leftJoin('registrations AS t2', 't1.reffered_by', '=', 't2.id')
            ->where('t1.id', '=', $id)
            ->select(
                't1.*',
                't2.id as rid',
                't2.first_name as rfname',
                't2.last_name as rlname',
            )
            ->first();
        $refferedby = Registration::oldest('first_name')
            ->where('status', '=', '1')
            ->select('id', 'first_name', 'last_name')
            ->get();
        return view('backend.members.edit', compact('member','refferedby'));
    }

    # Function to edit details about Member - 05/06/2022
    public function registrationupdate( $id)
    {
        //dd($id);
        // $chkmailexist = Registration::where('email', '=', Request::get('email'))
        //     ->Where(function ($query) {
        //         $query->where('status', '=', '1')
        //             ->orwhere('status', '=', '0');
        //     })
        //     ->first();
        // $chkqidexist = Registration::where('qid', '=', Request::get('qid'))
        //     ->Where(function ($query) {
        //         $query->where('status', '=', '1')
        //             ->orwhere('status', '=', '0');
        //     })
        //     ->first();
        $validate = Validator::make(Request::all(), [
            'FirstName' => 'required',
            'LastName' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'bloodGroup' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            //'mob_no_alter' => 'required',
            //'whatsapp' => 'required',
            //'passport' => 'required',
            'qid' => 'required',
            //'qid_expiration' => 'required',
            'address_qatar' => 'required',
            'address_ind' => 'required',
            'emplyoyer_crnt' => 'required',
            'designation' => 'required',
            'N_regNo' => 'required',
            'image' => 'image|mimes:png,jpg,jpeg|max:2048',
            'qidfile' => 'image|mimes:png,jpg,jpeg|max:2048',
            'idcardimage' => 'image|mimes:png,jpg,jpeg|max:2048',
        ]);
        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } 
        else {

            $now = Carbon::now();
            if(Request::hasFile('image'))
            {
                $orginal_img_name = Request::file('image')->getClientOriginalName();
                $extension_img = Request::file('image')->getClientOriginalExtension();
                $imgName = setNewNameForPhoto($orginal_img_name) . '.' . generateRandomString(2, 15) . '.' . $extension_img;
                //$fullPath_img = '/public/uploads/members' . $imgName;
                Request::file('image')->move(base_path() . '/public/uploads/members', $imgName);
            }
            if(Request::hasFile('idcardimage'))
            {
                $orginal_idcard = Request::file('idcardimage')->getClientOriginalName();
                $extension_idcard = Request::file('idcardimage')->getClientOriginalExtension();
                $imgIdCard = setNewNameForPhoto($orginal_idcard) . '.' . generateRandomString(2, 15) . '.' . $extension_idcard;
                Request::file('idcardimage')->move(base_path() . '/public/uploads/members/idcards', $imgIdCard);
            }
            if(Request::hasFile('qidfile'))
            {
                $orginal_qidfile = Request::file('qidfile')->getClientOriginalName();
                $extension_qidfile = Request::file('qidfile')->getClientOriginalExtension();
                $imgQidFile = setNewNameForPhoto($orginal_qidfile) . '.' . generateRandomString(2, 15) . '.' . $extension_qidfile;
                Request::file('qidfile')->move(base_path() . '/public/uploads/members/qidfiles', $imgQidFile);
            }
            $register=Registration::where('id',$id)->first();
            if(isset($register))
            {
                $register->first_name = Request::get('FirstName');
                $register->last_name = Request::get('LastName');
                $register->dob = Request::get('dob');
                if(Request::hasFile('image'))
                {
                    $register->image = $imgName;
                }
                $register->gender = Request::get('gender');
                $register->email = Request::get('email');
                $register->mob_no = Request::get('mobile');
                //$register->alter_mob_no = Request::get('mob_no_alter');
                $register->whatsapp = Request::get('whatsapp');
                //$register->passport = Request::get('passport');
                //$register->ind_contact_no = Request::get('india_no');
                $register->qid = Request::get('qid');
                //$register->qid_expiration = Request::get('qid_expiration');
                $register->address_qatar = Request::get('address_qatar');
                $register->address_ind = Request::get('address_ind');
                //$register->employer_pre = Request::get('emplyoyer_pre');
                $register->employer_current = Request::get('emplyoyer_crnt');
                $register->destination = Request::get('designation');
                if(Request::hasFile('qidfile'))
                {
                    $register->qidfile = $imgQidFile;
                }
                if(Request::hasFile('idcardimage'))
                {
                    $register->idcardimage = $imgIdCard;
                }
                $register->designationifothers = Request::get('designationothers');
                
                $register->reg_no = Request::get('N_regNo');
                $register->blood_grp = Request::get('bloodGroup');                ;
                $register->reffered_by = Request::get('refferedby');

                $register->confirm_from_member = Request::get('confirm_from_member');

                $register->save();

                $user = User::find($register->user_id);
                
                if ($user) {
                    $user->firstname = Request::get('FirstName');
                    $user->lastname = Request::get('LastName');
                    $user->email = Request::get('email');
                    $user->qid = Request::get('qid');
                    $user->save();
                }
                
                return Redirect::back()->with('success', 'Successfully updated. ');
            }
            else
            {
                return Redirect::back()->with('error', 'Not found Member. ');
            }            
        }
        return redirect()->route('members.index')->with('success','Member details updated.');
    }

    # Function to Load Renewal Check Form - 19/06/2021
    public function registrationControllerRenewalCheck()
    {
        return view('frondend/home/renewal_check');
    }

    # Function to Do Check Renewal of Member - 19/06/2021
    public function registrationControllerRenewalDoCheck()
    {
        $uniq_id = Request()->uniq_id;
        $member = Registration::where('uniq_id', $uniq_id)->first();
        if ($member) {
            $registration_date = $member->registration_date;
            $now = Carbon::now()->toDateString();
            // $difference = $now->diffInDays($registration_date);
            $formatted_registration_date = Carbon::parse($registration_date);
            $formatted_now = Carbon::parse($now);
            $difference = $formatted_now->diffInDays($formatted_registration_date);
            // dd($difference);
            $to_expire = 365 - $difference;
            // dd($difference);
            if ($difference > 365) {
                return Redirect::route('memebers.renewal')->with('error', 'Your Registration was expired to ' . abs($to_expire) . ' days, Please Renew your account');
            } else {
                return Redirect::back()->with('success', 'Your Registration is not expired, You have ' . $to_expire . ' days to expire');
            }
        } else {
            return Redirect::route('members.register')->with('error', 'You are not Registered yet, Please Register Here');
        }
    }

    # Function to Load Renewal Form - 19/06/2021
    public function registrationControllerRenewalForm()
    {
        $refferedby = Registration::oldest('first_name')
            ->where('status', '=', '1')
            ->select('id', 'first_name', 'last_name')
            ->get();
        return view('frondend/home/renewal', compact('refferedby'));
    }

    # Function to Do Renewal Membership - 19/06/2021
    public function registrationControllerDoRenewal()
    {
        $validate = Validator::make(Request()->all(), [
            'FirstName' => 'required',
            'LastName' => 'required',
            'email' => 'required|email', //|unique:registrations,email',
            'blood_grp' => 'required',
            'mobile' => 'required',
            //'whatsapp' => 'required',
            'uniq_id' => 'required',
            'designation' => 'required',
            'qid' => 'required',
            //'qid_expiration' => 'required',
            //'emplyoyer_pre' => 'required',
            'emplyoyer_crnt' => 'required',
        ]);
        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {
            $uniq_id = Request()->uniq_id;
            $qid =Request()->qid;
            $email=Request()->email;
            $member = Registration::where('uniq_id', $uniq_id)
              ->where('qid', $qid)
              ->where('email', $email)
              ->first();
            if ($member) {
                Registration::where('uniq_id', $uniq_id)->update([
                    'first_name' => Request()->FirstName,
                    'last_name' => Request()->LastName,
                    'email' => Request()->email,
                    'blood_grp' => Request()->blood_grp,
                    'mob_no' => Request()->mobile,
                    'whatsapp' => Request()->whatsapp,
                    'destination' => Request()->designation,
                    'qid' => Request()->qid,
                   // 'qid_expiration' => Request()->qid_expiration,
                    //'employer_pre' => Request()->emplyoyer_pre,
                    'employer_current' => Request()->emplyoyer_crnt,
                    'reffered_by' => Request()->refferedby,
                ]);
                $imgName = Registration::where('uniq_id', $uniq_id)->select('image')->first();
                //Mail Sending
                $to_email = Request::get('email');
                $renewal = [
                    'FirstName' => Request::get('FirstName'),
                    'LastName' => Request::get('LastName'),
                    'imgName' => $imgName,
                    'bloodGroup' => Request::get('blood_grp'),
                    'qid' => Request::get('qid'),
                    'emplyoyer_crnt' => Request::get('emplyoyer_crnt'),
                    'uniq_id' => $uniq_id,
                ];
                Mail::to($to_email)->send(new RenewalMail($renewal));
                //end mail sending
                return Redirect::route('homes.index')->with('success', 'Successfully Renewd your Registration');
            } else {
                return Redirect::back()->with('error', 'Please Check your Membership No, Email Id, QID & Try again');
            }
        }
    }
    # Function to Reject Member - 18/06/2021
    public function sharememberdetails()
    {
                    ini_set('memory_limit', '256M');

        $validate = Validator::make(Request()->all(), [
            'emailid' => 'required',
        ]);
        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {

            $id = Request()->id;
            $chkregistration = Registration::findOrFail($id);
            if ($chkregistration) {
                $member = DB::table('registrations as t1')
                    ->leftJoin('registrations AS t2', 't1.reffered_by', '=', 't2.id')
                    ->where('t1.id', '=', $id)
                    ->select(
                        't1.*',
                        't2.first_name as rfname',
                        't2.last_name as rlname',
                    )
                    ->first();
                $to_email = Request::get('emailid');
                //Mail::to($to_email)->send(new ShareMemberDetailsMail($sharedetails));
                $pdf = PDF::loadView('emails.shareMemberDetailsSendMail', compact('member'));
                //return $pdf->download('AllMembers.pdf'); 
                Mail::send('emails.shareMemberDetailsSendMail', compact('member'), function ($message) use ($id, $to_email, $pdf) {
                    //$message->from('info@**********');
                    $message->to($to_email);
                    $message->subject('Share Member Details Mail');
                    //Attach PDF doc
                    $message->attachData($pdf->output(), 'MemberDetails_' . $id . '.pdf');
                });
                return Redirect::back()->with('success', 'Successfully Mail Sent. ');
            } else {
                return Redirect::route('members.index')->with('error', 'Invalid Request');
            }
        }
    }
    # Function to Reject Member - 18/06/2021
    public function shareallmembersdetails()
    {
                    ini_set('memory_limit', '256M');

        $validate = Validator::make(Request()->all(), [
            'emailid' => 'required',
        ]);
        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {
            //DB::connection()->enableQueryLog();
            $members = DB::table('registrations as t1')
                ->leftJoin('registrations AS t2', 't1.reffered_by', '=', 't2.id')
                ->select(
                    't1.id',
                    't1.first_name',
                    't1.last_name',
                    't1.gender',
                    't1.mob_no',
                   // 't1.alter_mob_no',
                    't1.email',
                   // 't1.passport',
                    't1.destination',
                    't1.employer_current',
                    't1.uniq_id',
                    //'DATE_ADD(t1.registration_date, INTERVAL 1 YEAR) as reg_validity',//
                    DB::raw('DATE_ADD(t1.registration_date, INTERVAL 2 YEAR) as reg_validity'),
                    't1.status',
                    't1.reffered_by',
                    't2.first_name as rfname',
                    't2.last_name as rlname',
                )
                ->latest('id')
                ->get();
            //dd(DB::getQueryLog());
            $to_email = Request::get('emailid');
            $pdf = PDF::loadView('emails.allMemberSendMail', compact('members'));
            //return $pdf->download('AllMembers.pdf'); 
            Mail::send('emails.allMemberSendMail', compact('members'), function ($message) use ($to_email, $pdf) {
                //$message->from('info@**********');
                $message->to($to_email);
                $message->subject('All Members Mail');
                //Attach PDF doc
                $message->attachData($pdf->output(), 'AllMembers.pdf');
            });
            return Redirect::back()->with('success', 'Successfully Mail Sent. ');
        }
    }
    # Function to dropdown selected status members details will be sent to above textbox email - 15/01/2022
    public function sharestatusmembersdetails()
    {
                ini_set('memory_limit', '256M');

        $validate = Validator::make(request()->all(), [
            'emailid' => 'required|email',
        ]);

        if ($validate->fails()) {
            return Redirect::back()->with('error', $validate->errors());
        }

        $to_email = request()->get('emailid');
        $status = request()->get('status');
        $now = Carbon::now()->toDateString();

        $query = DB::table('registrations as t1')
            ->leftJoin('registrations AS t2', 't1.reffered_by', '=', 't2.id')
            ->select(
                't1.id',
                't1.first_name',
                't1.last_name',
                't1.gender',
                't1.dob',
                't1.mob_no',
                't1.email',
                't1.destination',
                't1.employer_current',
                't1.uniq_id',
                DB::raw('DATE_ADD(t1.registration_date, INTERVAL 2 YEAR) as reg_validity'),
                't1.status',
                't1.confirm_from_member',
                't1.reffered_by',
                't2.first_name as rfname',
                't2.last_name as rlname'
            )->orderBy('t1.id');

        $statusInEnglish = "";

        if ($status == '1') {
            $query->where('t1.status', '1');
            $statusInEnglish = "Approved";
        } elseif ($status == '0') {
            $query->where('t1.status', '0');
            $statusInEnglish = "Pending";
        } elseif ($status == '2') {
            $query->where('t1.status', '2');
            $statusInEnglish = "Rejected";
        } elseif ($status == '3') {
            $query->where('t1.status', '1')
                ->whereRaw('DATE_ADD(t1.registration_date, INTERVAL 2 YEAR) < ?', [$now]);
            $statusInEnglish = "Expired";
        } elseif ($status == '4') {
            $query->where('t1.status', '1')
                ->whereRaw('DATE_ADD(t1.registration_date, INTERVAL 2 YEAR) >= ?', $now);
            $statusInEnglish = "Valid Members";
        }

        $members = $query->get();
                // dd($members);

        try {
            $pdf = PDF::loadView('emails.statusMemberSendMail', compact('members'));
            //dd($pdf);
            //return $pdf->download('AllMembers.pdf');


            Mail::send('emails.statusMemberSendMail', compact('members'), function ($message) use ($to_email, $pdf, $statusInEnglish) {
                $message->to($to_email);
                $message->subject($statusInEnglish . ' Members Mail');
                $message->attachData($pdf->output(), $statusInEnglish . 'Members.pdf');
            });

            return Redirect::back()->with('success', 'Successfully Mail Sent.');
        } catch (\Exception $e) {
            \Log::error('Mail sending error: ' . $e->getMessage());
            return Redirect::back()->with('error', 'Mail failed: ' . $e->getMessage());
        }

    }

    # Function to sent mail to approved Members - 27/07/2021
    public function sentmailtomembers()
    {

        $members = Registration::where('status', '=', '1')
            ->select('email')
            ->latest('id')
            ->get();
        $allemails = '';
        foreach ($members as $value) {
            $allemails = $value->email . ',' . $allemails;
        }
        $approvedemails = $allemails;
        return view('backend/members/sentmail', compact('approvedemails'));
    }
    # Function to sent mail to approved Members - 27/07/2021
    public function postsentmailtomembers()
    {
        try {
            $myString = Request::get('tomail');
            $myArrayEmails = explode(',', $myString);
            foreach ($myArrayEmails as $key => $tag_name) {
                if ($tag_name == '') {
                    unset($myArrayEmails[$key]);
                }
            }
            //$message = Request::get('descriptionmail');
            // Mail::to($myArrayEmails)->send(new SendCustomMail($message));
            $msg_desc = Request::get('descriptionmail');
            $subject = Request::input('subject');
            $full_path=null;
            if (Request::file('file')) {
                $orginal_qidfile = Request::file('file')->getClientOriginalName();
                $extension_qidfile = Request::file('file')->getClientOriginalExtension();
                $imgQidFile = setNewNameForPhoto($orginal_qidfile) . '.' . generateRandomString(2, 15) . '.' . $extension_qidfile;
                Request::file('file')->move(base_path() . '/public/uploads/customemailfiles', $imgQidFile);
                $full_path = base_path() . '/public/uploads/customemailfiles/' . $imgQidFile;
                $data = [
                    'msg_desc' => $msg_desc,
                    'img_path' => $imgQidFile,

                ];
                
            } else {
                $data = [
                    'msg_desc' => $msg_desc,
                    'img_path' => null,

                ];
            }
            Mail::send('emails.sendCustomSendMail', compact('data'), function ($message) use ($myArrayEmails, $subject,  $full_path) {
                //$message->from('info@**********');
                $message->bcc($myArrayEmails);
                $message->subject($subject);

                //$message->attachData($file->output(), $imgAttachFile);
                if(isset($full_path))
                {
                    $message->attach($full_path);
                }
            });
            return Redirect::back()->with('success', 'Successfully Mail Sent. ');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
    
    // public function detailstaken(Request $request)
    // {
        //         ini_set('memory_limit', '256M');

        // // Validate the request
        // $validatedData = Request()->validate([
        //     'sdate' => 'required|date',
        //     'edate' => 'required|date|after_or_equal:sdate',
        // ]);

        // try {
            // Parse the start and end dates
            // $start_date = Carbon::parse(Request::get('sdate'));
            // $end_date = Carbon::parse(Request::get('edate'));

           // Fetch registrations within the date range
            // $query = Registration::whereBetween('registration_date', [$start_date, $end_date]);
           
                    // Filter by status if provided
        // $status = Request::input('status');
        // $statusInEnglish = '';

        // Apply status filter
        // if ($status == '1') {
        //     $query->where('status', '1');
        // } elseif ($status == '0') {
        //     $query->where('status', '0');
        // } elseif ($status == '2') {
        //     $query->where('status', '2');
        // } elseif ($status == '3') {
        //     $query->where('status', '1')->whereRaw("DATE_ADD(registration_date, INTERVAL 2 YEAR) < ?", [Carbon::now()]);
        // } elseif ($status == '4') {
        //     $query->where('status', '1')->whereRaw("DATE_ADD(registration_date, INTERVAL 2 YEAR) >= ?",  Carbon::now());
        // }


        // Fetch records
                //$registrations = $query->get();

 //dd($registrations);
    //     $registrations = $query->get()->map(function ($registration) {
    //         $registration->expiry_date = Carbon::parse($registration->registration_date)->addYear(1)->toDateString();
    //         return $registration;
    //     });
    //         // Check if no records are found
    //         if ($registrations->isEmpty()) {
    //             return back()->with('message', 'No records found for the selected dates.');
    //         }


    //         // Generate and download PDF if requested
    //         if (Request::has('download') && Request::get('download') == true) {
    //             $pdf = PDF::loadView('backend.members.detailspdf', compact('registrations'));
    //             return $pdf->download('details.pdf');
    //         }

    //         // If no download, display data on a web page
    //         return view('backend.members.index', ['message' => 'No records found for the selected dates.', 'registrations' => []]);
    //     } catch (\Exception $e) {
    //         // Log errors and return a user-friendly message
    //         Log::error('Error generating PDF: ' . $e->getMessage());
    //         return back()->with('error', 'An error occurred while processing the request.');
    //     }
    // }
    
    public function detailstaken(Request $request)
    {
        ini_set('memory_limit', '256M');

        $validatedData = Request()->validate([
            'sdate' => 'required|date',
            'edate' => 'required|date|after_or_equal:sdate',
        ]);

        try {

            $start_date = Carbon::parse(Request::get('sdate'));
            $end_date   = Carbon::parse(Request::get('edate'));

            $query = Registration::whereBetween('registration_date', [$start_date, $end_date]);

            $status = Request::input('status');

            if ($status == '1') {
                $query->where('status', '1');
            } elseif ($status == '0') {
                $query->where('status', '0');
            } elseif ($status == '2') {
                $query->where('status', '2');
            } elseif ($status == '3') {
                $query->where('status', '1')
                    ->whereRaw("DATE_ADD(registration_date, INTERVAL 2 YEAR) < ?", [Carbon::now()]);
            } elseif ($status == '4') {
                $query->where('status', '1')
                    ->whereRaw("DATE_ADD(registration_date, INTERVAL 2 YEAR) >= ?", [Carbon::now()]);
            }

            $registrations = $query->get();

            if ($registrations->isEmpty()) {
                return back()->with('error', 'No records found for the selected dates.');
            }

            // ✅ Excel Download
            if (request()->has('excel') && request()->excel == true) {
                $start = Carbon::parse(Request::get('sdate'))->format('Y-m-d');
                $end   = Carbon::parse(Request::get('edate'))->format('Y-m-d');
                
                $statusMap = [
                    '1' => 'Approved',
                    '0' => 'Pending',
                    '2' => 'Rejected',
                    '3' => 'Expired',
                    '4' => 'Valid Member'
                ];

                $statusText = $statusMap[$status] ?? 'all';

                $fileName = "registration_details__{$statusText}_{$start}_to_{$end}.xlsx";


                return Excel::download(
                    new RegistrationsExport($registrations),
                    $fileName
                );
            }

            return view('backend.members.index', compact('registrations'));

        } catch (\Exception $e) {

            Log::error('Error exporting Excel: ' . $e->getMessage());

            return back()->with('error', 'Something went wrong.');
        }
    }


}