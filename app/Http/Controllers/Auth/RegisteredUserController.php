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
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:11',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'TermsAndConditions' => ['required', 'accepted'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $last_id = User::orderBy('six_digit_Id', 'desc')->first()->six_digit_Id;
        $six_digit_Id = ++$last_id;

        $userid = 'STUD-'.$date.$six_digit_Id;
        
        
        // $user = User::create([
        //     'userid' => $abc,
        //     'six_digit_Id' => $last_id,
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);
        $user = new User();
        $user->userid = $userid;
        $user->six_digit_Id = $six_digit_Id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->lock = 0;
        $user->number = $request->number;
        $user->password = Hash::make($request->password);
        $user->save();
        $role = Role::where('name','student')->first();
        $user->roles()->attach($role);


        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
