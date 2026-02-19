<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    # Function to print Hashed Password for testing - 16/06/2021
    public function userControllerTestPassword()
    {
        $password = Hash::make("123456");
        dd($password);
    }

    # Function to load Admin Login page - 16/06/2021
    public function userControllerLogin()
    {
        return view('backend/auth/login');
    }

    # Function to Admin Do Login - 16/06/2021
    public function userControllerDoLogin(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return Redirect()->back()->with('error', $validate->errors());
        } else {
            $userData = array(
                'email' => $request->email,
                'password' => $request->password,
            );
            // dd($userData);
            if (Auth::attempt($userData)) {
                $user = Auth::user();
                if ($user->status == 0) {
                    Auth::logout();
                    return Redirect::back()->with('error', 'Your Account is not Activated');
                }
                if ($user->user_type == 1) {
                    return Redirect::intended('admin/home');
                } else {
                    return Redirect::back()->with('error', 'You dont have Previlege');
                }
            } else {
                return Redirect::back()->with('error', 'Invalid Username or Password');
            }
        }
    }

    # Function load Admin Dashboard - 16/06/2021
    public function userControllerDashboard()
    {
        return view('backend/home/index');
    }

    # Function to Admin Logout - 16/06/2021
    public function userControllerLogout()
    {
        Auth::logout();
        return Redirect::intended('/');
    }

    # Function to Edit Admin Profile - 16/06/2021
    public function userControllerProfileEdit()
    {
        $user = Auth::user();
        $user_data = User::select('id', 'firstname', 'lastname', 'email', 'password', 'user_type', 'status')
            ->where('id', $user->id)->first();
        return view('backend/cms/profile', ['user' => $user_data]);
    }

    # Function to Update Admin Profile - 16/06/2021
    public function userControllerProfileUpdate(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
        ]);
        $id = $request->id;
        $user = User::findOrFail($id);
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;

        User::where('id', $id)->update([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
        ]);
        return Redirect()->intended('admin/profile')->with('success', 'Profile Updated Successfull');
        if ($validate->faisl()) {
            return Redirect::back()->with('error', $validate->errors());
        } else {

        }
    }

    # Function to Admin Change Password - 16/06/2021
    public function userControllerChangePassword(Request $request)
    {
        $user = Auth::user();
        $validate = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required',
            'old_password' => 'required',
        ]);
        if ($validate->fails()) {
            return Redirect::intended()->with('error', $validate->errors());
        } else {
            $password = $request->password;
            $c_password = $request->confirm_password;
            $old_password = $request->old_password;

            if ($password == $c_password) {
                $user_data = array(
                    'email' => $user->email,
                    'password' => $old_password,
                );
                if (Hash::check($password, $user->password)) {
                    return Redirect::intended('admin/profile')->with('error', 'Old & New Password cannot same');
                }
                if (Auth::attempt($user_data)) {
                    $new_password = Hash::make($password);
                    User::where('id', $user->id)->update([
                        'password' => $new_password,
                    ]);
                    return Redirect::intended('admin/profile')->with('success', 'Password changed Successfully');
                } else {
                    return Redirect::intended('admin/profile')->with('error', 'Incorrect Old Password, Please check & Retry');
                }
            } else {
                return Redirect::intended('admin/profile')->with('error', 'New Password & Confirm Password should be same');
            }
        }
    }
}