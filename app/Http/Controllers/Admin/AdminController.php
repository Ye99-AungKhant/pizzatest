<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //direct admin profile
    public function profile()
    {
        $id = auth()->user()->id;
        $userData = User::where('id', $id)->first();
        //dd($adminData->toArray());
        return view('admin.profile.index')->with(['admin' => $userData]);
    }

    //update profile
    public function updateProfile($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $updateData = $this->requestUserData($request);
        User::where('id', $id)->update($updateData);
        return back()->with(['updateSuccess' => 'User Info Updated']);
    }

    //change password
    public function changePassword($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = User::where('id', $id)->first();

        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;
        $confirmPassword = $request->confirmPassword;
        $hashedPassword = $data['password'];

        if (Hash::check($oldPassword, $hashedPassword)) { //db hash password and old password is same

            if ($newPassword != $confirmPassword) { //new and confirm password is not same
                return back()->with(['notSameError' => 'New Password Not Same Confirm Password! Try Again...']);
            } else { //new and confirm password is same 
                if (strlen($newPassword) <= 6 || strlen($confirmPassword) <= 6) { // <6
                    return back()->with(['lengthError' => 'Password Must Be Greater Than 6...']);
                } else { //change case
                    $hash = Hash::make($newPassword);

                    User::where('id', $id)->update([
                        'password' => $hash
                    ]);
                    return back()->with(['success' => 'Password Change...']);
                }
            }
        } else {
            return back()->with(['notMatchError' => 'Password Do Not Match! Try Again...']);
        }
    }

    public function changePasswordPage()
    {
        return view('admin.profile.changePassword');
    }

    private function requestUserData($request)
    {
        return [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];
    }
}
