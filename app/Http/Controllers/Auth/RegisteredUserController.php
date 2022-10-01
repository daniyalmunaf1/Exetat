<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $date = date('dmy');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profilepic' => ['required', 'max:100000'],
            'TermsAndConditions' => ['required', 'accepted'],
        ]);
        $last_id = User::orderBy('six_digit_Id', 'desc')->first()->six_digit_Id;
        $six_digit = ++$last_id;
        $six_digit_Id = substr($six_digit,1);
        $userid = 'STUD-'.$date. '-'.$six_digit_Id;
        // dd($userid);
        
        
        // $user = User::create([
        //     'userid' => $abc,
        //     'six_digit_Id' => $last_id,
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);
        $profilepic = app('App\Http\Controllers\UploadImageController')->storage_upload($request->profilepic,'/app/public/Users/Profile/');
        $user = new User();
        $user->userid = $userid;
        $user->six_digit_Id = $six_digit;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->lock = 0;
        $user->wallet = 0;
        $user->number = $request->number;
        $user->profilepic = $profilepic;
        $user->password = Hash::make($request->password);
        $user->save();
        $role = Role::where('name','student')->first();
        $user->roles()->attach($role);


        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
