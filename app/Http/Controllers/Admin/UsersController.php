<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmailLink;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $users = User::all();
        return view('admin.index')->with('users',$users);

    }

    public function gotochangepassword()
    {
        return view('auth.change-password');
    }

    public function adduser()
    {
        return view('admin.add-user');
    }

    public function addUserViaMail($name,$email,$role)
    {
        return view('auth.register-by-invite')->with([
            'name'=>$name,
            'email'=>$email,
            'role'=>$role
        ]);;
    }
   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            $roles = Role::all();
            return view('admin.edit')->with([
                'user'=>$user,
                'roles'=>$roles
            ]);
        }
        else
        {
            if($user->hasRole('student'))
            {
                $roles = Role::all();
                return view('admin.edit')->with([
                    'user'=>$user,
                    'roles'=>$roles
                ]);
            }
            else{
                return redirect()->route('index');

            }
        }
    }

    public function export_users_information(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            $roles = Role::all();
            $pdf = PDF::loadView('pdf', [
                'user'=>$user,
                'roles'=>$roles
            ])->setOptions(['defaultFont' => 'sans-serif', 'isHtml5ParseEnabled' => true , 'isRemoteEnabled' => true]);
            return $pdf->download($user->userid.'.pdf');
            
        }
        else
        {
            return Redirect()->back()->with('message', 'You are not Allowed for this Action');   
        }
        
    }

    public function userDetails(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            $roles = Role::all();
            return view('admin.user_detail')->with([
                'user'=>$user,
                'roles'=>$roles
            ]);
        }
        else
        {
            if($user->hasRole('student'))
            {
                $roles = Role::all();
                return view('admin.user_detail')->with([
                    'user'=>$user,
                    'roles'=>$roles
                ]);
            }
            else{
                return redirect()->route('index');

            }
        }
    }

    public function profile()
    {
        $roles = Role::all();
        $user = Auth::user();
        return view('profile')->with([
            'user'=>$user,
            'roles'=>$roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:11',
            'email' => ['required', 'string', 'email', 'max:255']
        ]);
        if($request->profilepic != null)
        {
        $profilepic = app('App\Http\Controllers\UploadImageController')->storage_upload($request->profilepic,'/app/public/Users/Profile/');
            $user->profilepic = $profilepic;

        }
        $user->roles()->sync($request->roles);
        $user->name = $request->name;
        $user->number = $request->number;
        $user->save();
        
        return Redirect()->back()->with('message', 'Details Updated Successfully');   
    }

    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        // $user->userid = Auth::user()->userid;
        // $user->six_digit_Id = Auth::user()->six_digit_Id;
        // $user->name = Auth::user()->name;
        // $user->email = Auth::user()->email;
        // $user->lock = Auth::user()->lock;
        // $user->number = Auth::user()->number;
        
        DB::table('users')
        ->where('id', Auth::user()->id)  // find your user by their email
        ->update(array('password' => Hash::make($request->password)));
        
        return redirect()->route('index');
    }


    public function lockuser(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            if($user->lock==1)
            {
                $user->lock = 0;
                $user->save();
                return redirect()->route('index');
            }
            else
            {
                $user->lock = 1;
                $user->save();
                return redirect()->route('index');
            }
        }
        else
        {
            if($user->hasRole('student'))
            {
                if($user->lock==1)
                {
                    $user->lock = 0;
                    $user->save();
                    return redirect()->route('index');
                }
                else
                {
                    $user->lock = 1;
                    $user->save();
                    return redirect()->route('index');
                }
            }
            else{
                return redirect()->route('index');

            }
        }
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
        $name = $request->name; 
        $email = $request->email;
        $role = $request->role;
  
        Mail::to($email)->send(new SendEmailLink($name,$email,$role));
        return Redirect()->back()->with('message', 'Invitation Sent Successfully');   
    }

    public function storeuser(Request $request)
    {
        // dd($request->role);

        $date = date('dmy');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:11',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profilepic' => ['required', 'max:10000'],
        ]);
        $last_id = User::orderBy('six_digit_Id', 'desc')->first()->six_digit_Id;
        $six_digit = ++$last_id;
        $six_digit_Id = substr($six_digit,1);
        if($request->role == 'student')
        {
            $userid = 'STUD-'.$date. '-'.$six_digit_Id;
        }
        else if($request->role == 'contributor'){
            $userid = 'CONT-'.$date. '-'.$six_digit_Id;
        }
        else if($request->role == 'helpdesk'){
            $userid = 'HELP-'.$date. '-'.$six_digit_Id;
        }
        else if($request->role == 'admin'){
            $userid = 'SYSA-'.$date. '-'.$six_digit_Id;
        }
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
        $user->number = $request->number;
        $user->profilepic = $profilepic;
        $user->password = Hash::make($request->password);
        $user->save();
        $role = Role::where('name',$request->role)->first();
        $user->roles()->attach($role);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if(Auth::user()->hasRole('admin')){
            $user->roles()->detach();
            $user->delete();
            return redirect()->route('index');
        }
        else
        {
            if($user->hasRole('student'))
            {
                $user->roles()->detach();
                $user->delete();
                return redirect()->route('index');
            }
            else{
                return redirect()->route('index');

            }
        }
    }
}
