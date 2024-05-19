<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function userList(){
        $userData = User::where('role','user')->paginate(7);
        
        return view('admin.user.userList')->with(['user'=>$userData]);
    }

    public function adminList(){
        $userData = User::where('role','admin')->paginate(7);
        
        return view('admin.user.adminList')->with(['user'=>$userData]);
    }

    //user account search
    public function userSearch(Request $request){
        $key = $request->searchData;

        $searchData = User::where('role','user')
                        ->where(function($query) use ($key){
                            $query->orWhere('name','like','%'.$key.'%')
                            ->orWhere('email','like','%'.$key.'%')
                            ->orWhere('phone','like','%'.$key.'%')
                            ->orWhere('address','like','%'.$key.'%');
                        })
                        ->paginate(7);
        
        $searchData->appends($request->all());
        return view('admin.user.userList')->with(['user'=>$searchData]);
    }

    //edit user
    public function editUser($id){
        $data = User::select('users.id','users.name','users.email','users.phone','users.address','users.role')
                ->where('users.id',$id)
                ->first();
        return view('admin.user.edit')->with(['userData'=>$data]);
    }

    public function updateUser($id, Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'role' => 'required',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
        ];
        User::where('id',$id)->update($data);
        return redirect()->route('admin#userList')->with(['updateSuccess'=>'User Updated']);
    }

    public function userDelete($id){
        User::where('id',$id)->delete();
        return back()->with(['deleteSuccess'=>'User Deleted']);
    }

    //admin account search
    public function adminSearch(Request $request){
        $key = $request->searchData;

        $searchData = User::where('role','admin')
                        ->where(function ($query) use ($key){
                            $query->orWhere('name','like','%'.$key.'%')
                            ->orWhere('email','like','%'.$key.'%')
                            ->orWhere('phone','like','%'.$key.'%')
                            ->orWhere('address','like','%'.$key.'%');
                        })
                        ->paginate(7);
        
        $searchData->appends($request->all());
        return view('admin.user.adminList')->with(['user'=>$searchData]);
    }

    public function adminDelete($id){
        User::where('id',$id)->delete();
        return back()->with(['deleteSuccess'=>'Admin Account Deleted!']);
    }
}
