<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Request;
use App\Mail\RegistrationMail;
use App\Mail\ApproveMail;
use App\Mail\RenewalMail;
use App\Mail\ShareMemberDetailsMail;
use App\Mail\SendCustomMail;
use Mail;
use Illuminate\Support\Facades\DB;
use PDF;
use Hash;
use Str;


class RegistrationController extends Controller
{
    
     # Function to Load Registration Page - 19/06/2021
    public function registrationControllerRegistrationForm()
    {
        $refferedby = Registration::oldest('first_name')
            ->where('status', '=', '1')
            ->select('id', 'first_name', 'last_name')
            ->get();
            return response()->json([
                'referredBy' => $refferedby,
            ], 200);
       // return view('frondend/home/register', compact('refferedby'));
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
            'qid' => 'required',
            'address_qatar' => 'required',
            'address_ind' => 'required',
            'emplyoyer_crnt' => 'required',
            'designation' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg,pdf|max:5120',
            'qidfile' => 'required|image|mimes:png,jpg,jpeg,pdf|max:5120',
            'idcardimage' => 'required|image|mimes:png,jpg,jpeg,pdf|max:5120', 
            'N_regNo' =>'required',
        ], [
            'N_regNo.required' => 'QCHP ID Number / Nursing Registration Number is required.',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors(),
            ], 400);
        } elseif ($chkmailexist) {
            return response()->json([
                'error' => 'Email already exists.',
            ], 400);
        } elseif ($chkqidexist) {
            return response()->json([
                'error' => 'Qid already exists.',
            ], 400);
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
            
            
            # create customer details in user table
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
            $register->dob = (Request::get('dob') == 0) ? null : Request::get('dob');
            $register->image = $imgName;
            $register->gender = Request::get('gender');
            $register->email = Request::get('email');
            $register->mob_no = Request::get('mobile');
            $register->whatsapp = Request::get('whatsapp');
            $register->qid = Request::get('qid');
            $register->address_qatar = Request::get('address_qatar');
            $register->address_ind = Request::get('address_ind');
            $register->employer_current = Request::get('emplyoyer_crnt');
            $register->destination = Request::get('designation');
            //new updates on 31-07-2021
            $register->qidfile = $imgQidFile;
            $register->idcardimage = $imgIdCard;
            $register->designationifothers = Request::get('designationothers');
            $register->blood_grp = Request::get('bloodGroup');
            $register->reg_no = Request::get('N_regNo');
            $register->registration_date = $now->toDateString();
            $register->expiry_date = $now->addDays(365);
            $register->status = 0;
            $register->reffered_by = Request::get('refferedby');
            $register->save();


            $uniqId = "UN" . str_pad($register->id, 4, '0', STR_PAD_LEFT);
            $register->uniq_id = $uniqId;
            $register->update();
            // dd($user);
           // $user->update();
            //Mail Sending
            $to_email = Request::get('email');
            $registration = $register;
           try {
                   Mail::to($to_email)->send(new RegistrationMail($registration ,$password));
            } catch (\Exception $e) {
                       

                return Redirect::back()->with('success', 'Registration Successfully Completed. ');
            }
            //end mail sending
            return Redirect::back()->with('success', ' Registration Successfully Completed. Mail sent to your email ,please check.');
        
        }
    }

    

    
    # Function to List all Members - 18/06/2021
    public function registrationControllerIndex()
    {
        // $members = Registration::all();
        // foreach ($members as $member) {
        //     $last_updated_date = $member->updated_at;
        //     $now = Carbon::now()->toDateString();
        //     $difference = $last_updated_date->diffInDays($now);
        //     if ($difference > 365) {
        //         //dd('hi');
        //         Registration::where('id', $member->id)->update([
        //             'status' => 5,
        //         ]);
        //     }
        // }
        // if (request()->page) {
        //     $page = request()->page;
        // } else {
        //     $page = "1";
        // }

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
                't1.registration_date',
            )->latest('id');
            if (request()->has('searchquery')) {
                $status = 2;

                switch (strtoupper($key)) {
                    case 'PENDING':
                        $status = 0;
                        break;
                    case 'APPROVED':
                        $status = 1;
                        break;
                    case 'REJECTED':
                        $status = 2;
                        break;
                }
            $query->where('t1.first_name', 'like', '%' . $key . '%')
            ->orWhere('t1.last_name', 'like', '%' . $key . '%')
            ->orWhere('t1.uniq_id', 'like', '%' . $key . '%')
            ->orWhere('t1.mob_no', 'like', '%' . $key . '%')
            // ->orWhere('t1.alter_mob_no', 'like', '%' . $key . '%')
            ->orWhere('t1.dob', 'like', '%' . $key . '%')
            ->orWhere('t1.email', 'like', '%' . $key . '%')
            // ->orWhere('t1.passport', 'like', '%' . $key . '%')
            ->orWhere('t1.destination', 'like', '%' . $key . '%')
            ->orWhere('t1.employer_current', 'like', '%' . $key . '%')
            ->orWhere('t1.status', $status);
        }
        $members = $query->paginate(10);
        return response()->json($members);
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
            // ->orWhere('t1.alter_mob_no', 'like', '%' . $key . '%')
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
            //Mail::to($to_email)->send(new ApproveMail($approve));
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

            return Redirect::to('members.index')->json([
                'message' => 'Member Details are Approved. Email sent.',
            ], 200);
          } else {
            return Redirect::to('members.index')->json([
                'error' => 'Invalid Request',
            ], 404);

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
            return Redirect::to('members.index')->json([
                'message' => 'Member Details are Resend. Email sent.',
            ], 200);
          } else {
            return Redirect::to('members.index')->json([
                'error' => 'Invalid Request',
            ], 404);
            //$members = Registration::latest('id')->paginate(5);
           // return Redirect::route('members.index')->with('success', 'Member Details are Resend. Email sent.');
            //return view('backend/members/index', compact('members'))->with('success', 'Member Details are Approved');
        //} else {
           // return Redirect::route('members.index')->with('error', 'Invalid Request');
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
            return response()->json([
                'message' => 'Member Details are Rejected'],200);
        } else {
            return response('')->json([
                'error' => 'Invalid Request',
            ], 404);
        }
    }
    # Function to Delete Member - 23/10/2021
    public function destroy($id)
    {
        $registration = Registration::findOrFail($id);
        if ($registration) {
            $path_event = 'public/uploads/members/' . $registration->image;
            @unlink($path_event);
            $path_event = 'public/uploads/members/idcards/' . $registration->idcardimage;
            @unlink($path_event);
            $path_event = 'public/uploads/members/qidfiles/' . $registration->qidfile;
            @unlink($path_event);
            Registration::destroy($id);
            return response()->json([
                'message' => 'Member Deleted Successfully'], 200);
        } else {
            return response()->json([
                'error' => 'Invalid Request'], 404);
        }
    }
    # Function to Load Registration Page - 19/06/2021
    public function registration_confirm($id,$token)
    {
        $reg=Registration::where('id',$id)->first();
        if($reg)
        {
            if($token==md5($reg->created_at))
            {
                $reg->confirm_from_member='confirmed';
                $reg->save();
                return response()->json([
                    'message' =>'Member details confirmed.'], 200);
            }else {
                return response()->json([
                    'error' => 'Invalid token.',
                ], 400);
            }
        }
        else
        {
            return response()->json([
                'error' =>'error','Member details not confirmed.'], 404);
        }
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
            if ($member) {
                return response()->json([
                    'member' => $member,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Member not found.',
                ], 404);
            }
        //return view('backend/members/view_more', compact('member'));
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
            if ($member) {
                return response()->json([
                    'member' => $member,
                    'referredBy' => $refferedby,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Member not found.',
                ], 404);
            }
       // return view('backend.members.edit', compact('member','refferedby'));
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
            // 'passport' => 'required',
            'qid' => 'required',
            // 'qid_expiration' => 'required',
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
            return response()->json([
                'error' => $validate->errors(),
            ], 400);
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
            if(($register))
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
                // $register->alter_mob_no = Request::get('mob_no_alter');
                $register->whatsapp = Request::get('whatsapp');
                // $register->passport = Request::get('passport');
                // $register->ind_contact_no = Request::get('india_no');
                $register->qid = Request::get('qid');
                // $register->qid_expiration = Request::get('qid_expiration');
                $register->address_qatar = Request::get('address_qatar');
                $register->address_ind = Request::get('address_ind');
                // $register->employer_pre = Request::get('emplyoyer_pre');
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
                    $user->firstname = Request::get('firstname');
                    $user->lastname = Request::get('lastname');
                    $user->email = Request::get('email');
                    $user->qid = Request::get('qid');
                    $user->save();
                }
                
                   
                return response()->json([
                    'success' => 'Member details updated successfully.',
                ], 200);            }
            else
            {
                return response()->json([
                    'error' => 'Member not found.',
                ], 404);            }
        }
        return response()->json([
            'success' => 'Member details updated successfully.',
        ], 200);
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
            $to_expire = 730 - $difference;
            // dd($difference);
            if ($difference > 730) {
                return response()->json([
                    'message' => 'Your Registration was expired',
                    'days_to_expire' => abs($to_expire),
                ], 400);
                //return Redirect::route('memebers.renewal')->with('error', 'Your Registration was expired to ' . abs($to_expire) . ' days, Please Renew your account');
            } else {
                return response()->json([
                    'message' => 'Your Registration is not expired',
                    'days_to_expire' => $to_expire,
                ], 200);
                //return Redirect::back()->with('success', 'Your Registration is not expired, You have ' . $to_expire . ' days to expire');
            }
        } else {
            return response()->json([
                'error' => 'Member not found. You are not registered yet. Please register.',
            ], 404);
           // return Redirect::route('members.register')->with('error', 'You are not Registered yet, Please Register Here');
        }




    }

    # Function to Load Renewal Form - 19/06/2021
    public function registrationControllerRenewalForm()
    {
        $refferedby = Registration::oldest('first_name')
            ->where('status', '=', '1')
            ->select('id', 'first_name', 'last_name')
            ->get();
            return response()->json([
                'referred_by' => $refferedby,
            ], 200);
        //return view('frondend/home/renewal', compact('refferedby'));
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
            'emplyoyer_crnt' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors(),
            ], 422);
        } else {
            $uniq_id = Request()->uniq_id;
            $qid =Request()->qid;
            $email=Request()->email;
            //$member = Registration::find($uniq_id,$qid,$email);
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
                return response()->json([
                    'message' => 'Successfully renewed your registration.',
                ], 200);
                //return Redirect::route('homes.index')->with('success', 'Successfully Renewd your Registration');
            } else {
                return response()->json([
                    'error' => 'Please check your Membership No, Email, QID, and try again.',
                ], 404);
                //return Redirect::back()->with('error', 'Please Check your Membership No ,Email, QID & Try again');
            }
        }
    }
    # Function to Reject Member - 18/06/2021
    public function sharememberdetails()
    {
        $validate = Validator::make(Request()->all(), [
            'emailid' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors(),
            ], 422);
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
               // $to_email = Request::get('emailid');
                //Mail::to($to_email)->send(new ShareMemberDetailsMail($sharedetails));
                // $pdf = PDF::loadView('emails.shareMemberDetailsSendMail', compact('member'));
                // //return $pdf->download('AllMembers.pdf');
                // Mail::send('emails.shareMemberDetailsSendMail', compact('member'), function ($message) use ($id, $to_email, $pdf) {
                //     //$message->from('info@**********');
                //     $message->to($to_email);
                //     $message->subject('Share Member Details Mail');
                //     //Attach PDF doc
                //     $message->attachData($pdf->output(), 'MemberDetails_' . $id . '.pdf');
                // });
                return response()->json([
                    'message' => 'Successfully Mail Sent.',
                ], 200);
                //return Redirect::back()->with('success', 'Successfully Mail Sent. ');
            } else {
                return response()->json([
                    'error' => 'Invalid Request',
                ], 404);
                //return Redirect::route('members.index')->with('error', 'Invalid Request');
            }
        }
    }
    # Function to Reject Member - 18/06/2021
    public function shareallmembersdetails()
    {
        $validate = Validator::make(Request()->all(), [
            'emailid' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors(),
            ], 422);
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
           // $to_email = Request::get('emailid');
            // $pdf = PDF::loadView('emails.allMemberSendMail', compact('members'));
            // //return $pdf->download('AllMembers.pdf');
            // Mail::send('emails.allMemberSendMail', compact('members'), function ($message) use ($to_email, $pdf) {
            //     //$message->from('info@**********');
            //     $message->to($to_email);
            //     $message->subject('All Members Mail');
            //     //Attach PDF doc
            //     $message->attachData($pdf->output(), 'AllMembers.pdf');
            // });
            return response()->json([
                'message' => 'Successfully Mail Sent.',
            ], 200);
           // return Redirect::back()->with('success', 'Successfully Mail Sent. ');
        }
    }
    # Function to dropdown selected status members details will be sent to above textbox email - 15/01/2022
    public function sharestatusmembersdetails()
    {
        $validate = Validator::make(Request()->all(), [
            'emailid' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors(),
            ], 422);
        } else {
            $to_email = Request::get('emailid');
            $status = Request::get('status');
            //dd($status);
            //$statusInEnglish = "";
            $statusInEnglish = $this->getStatusInEnglish($status);
            switch ($status) {
                case '0':
                    return 'Pending';
                case '1':
                    return 'Approved';
                case '2':
                    return 'Rejected';
                case '3':
                    return 'Expired';
                default:
                    return '';
                }
            //DB::enableQueryLog();
            $members = DB::table('registrations as t1')
                ->leftJoin('registrations AS t2', 't1.reffered_by', '=', 't2.id')
                ->select(
                    't1.id',
                    't1.first_name',
                    't1.last_name',
                    't1.gender',
                    't1.dob',
                    't1.mob_no',
                    // 't1.alter_mob_no',
                    't1.email',
                    // 't1.passport',
                    't1.destination',
                    't1.employer_current',
                    't1.uniq_id',
                    DB::raw('DATE_ADD(t1.registration_date, INTERVAL 2 YEAR) as reg_validity'),
                    't1.status',
                    't1.confirm_from_member',
                    't1.reffered_by',
                    't2.first_name as rfname',
                    't2.last_name as rlname',
                );
                if($status == '3')
                {
                    $members=$members->whereRaw('DATE_ADD(t1.registration_date, INTERVAL 2 YEAR) <?',[Carbon::now()->format('Y-m-d')])
                    ->where('t1.status', '=', 1);//approved status only and daterange 1
                }
                else
                {
                    $members=$members->where('t1.status', '=', $status);
                }
                $members=$members->latest('t1.id')
                ->get();
                // dd($members->toSql());
                //dd($members);

            $pdf = PDF::loadView('emails.statusMemberSendMail', compact('members'));
            //return $pdf->download('AllMembers.pdf');
             Mail::send('emails.statusMemberSendMail', compact('members'), function ($message) use ($to_email, $pdf, $statusInEnglish) {
            //     //$message->from('info@**********');
                 $message->to($to_email);
                 $message->subject($statusInEnglish . ' Members Mail');
            //     //Attach PDF doc
                 $message->attachData($pdf->output(), $statusInEnglish . 'Members.pdf');
             });
             return response()->json([
                'message' => 'Successfully Mail Sent.',
            ], 200);
           // return Redirect::back()->with('success', 'Successfully Mail Sent. ');
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
        return response()->json([
            'approvedEmails' => $approvedemails,
        ], 200);
        //return view('backend/members/sentmail', compact('approvedemails'));
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
            // Mail::send('emails.sendCustomSendMail', compact('data'), function ($message) use ($myArrayEmails, $subject,  $full_path) {
            //     //$message->from('info@**********');

            //     $message->bcc($myArrayEmails);
            //     $message->subject($subject);

            //     //$message->attachData($file->output(), $imgAttachFile);
            //     if(isset($full_path))
            //     {
            //         $message->attach($full_path);
            //     }
            // });
            return response()->json([
                'success' => 'Successfully Mail Sent.',
            ], 200);
           // return Redirect::back()->with('success', 'Successfully Mail Sent. ');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
            //return Redirect::back()->with('error', $e->getMessage());
        }
    }
   
}