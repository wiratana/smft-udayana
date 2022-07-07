<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ChangePasswordController extends Controller
{
    /**
     * Where to redirect users after change their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Change User Password.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = User::find(Auth::id());

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect($this->redirectTo);
    }
}
