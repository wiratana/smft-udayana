<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\User\StoreUser;
use App\Http\Requests\User\UpdateUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // public function index()
    // {
    //     return view('admin.user', [
    //         'users' => User::where('role', '<>', 'SUPER_ADMIN')->get()
    //     ]);
    // }

    public function store(StoreUser $request)
    {
        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->nik      = $request->nik;
        $user->role     = $request->role; 
        $user->password = Hash::make($request->password);

        if($request->file('image')){            
            $image_path = $request->file('image')->store(User::IMAGE_PATH);
            $user->image_path = $image_path;
        }

        $user->save();

        return response()->json(['message' => 'User has been added'], 200);
    }

    public function update(UpdateUser $request, User $user)
    {
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->nik      = $request->nik;
        $user->role     = $request->role;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;

        if($request->file('image')){
            if($user->image_path)
                Storage::delete($user->image_path);
            
            $image_path = $request->file('image')->store(User::IMAGE_PATH);
            $user->image_path = $image_path;
        }

        $user->save();

        return response()->json(['message' => 'User has been updated'], 200);
    }

    public function destroy(User $user)
    {
        if($user->image_path)
            Storage::delete($user->image_path);

        $user->delete();

        return response()->json(['message' => 'User has been deleted'], 200);
    }
}