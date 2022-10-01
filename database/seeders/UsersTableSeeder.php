<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        DB::table('role_user')->truncate();

        $adminRole = Role::where('name','admin')->first();
        $helpdeskRole = Role::where('name','helpdesk')->first();
        $contributorRole = Role::where('name','contributor')->first();
        $studentRole = Role::where('name','student')->first();

        $admin = User::create([
            'userid' => 'SYSA-270822-000001',
            'six_digit_id' => '_000001',
            'name' => 'Admin',
            'lock' => 0,
            'wallet' => 0,
            'number' => '03123456789',
            'profilepic' => '/app/public/Users/Profile/78155563001661826913.avatar-male-1.jpg',
            'email' => 'admin@admin.com',
            'password' => Hash::make('qwertyuiop'),
            'email_verified_at' => '2022-08-19 18:34:11'
        ]);
        $helpdesk = User::create([
            'userid' => 'HELP-270822-000002',
            'six_digit_id' => '_000002',
            'name' => 'Helpdesk',
            'lock' => 0,
            'wallet' => 0,
            'number' => '031234567890',
            'profilepic' => '/app/public/Users/Profile/78155563001661826913.avatar-male-2.jpg',
            'email' => 'helpdesk@helpdesk.com',
            'password' => Hash::make('qwertyuiop'),
            'email_verified_at' => '2022-08-19 18:34:11'
        ]);
        $contributor = User::create([
            'userid' => 'CONT-270822-000003',
            'six_digit_id' => '_000003',
            'name' => 'xyz Contributor',
            'lock' => 0,
            'wallet' => 0,
            'number' => '0312345678901',
            'profilepic' => '/app/public/Users/Profile/78155563001661826913.avatar-male-3.jpg',
            'email' => 'contributor@contributor.com',
            'password' => Hash::make('qwertyuiop'),
            'email_verified_at' => '2022-08-19 18:34:11'
        ]);
        $student = User::create([
            'userid' => 'STUD-270822-000004',
            'six_digit_id' => '_000004',
            'name' => 'xyz Student',
            'lock' => 0,
            'wallet' => 0,
            'number' => '03123456789012',
            'profilepic' => '/app/public/Users/Profile/78155563001661826913.avatar-male-4.jpg',
            'email' => 'student@student.com',
            'password' => Hash::make('qwertyuiop'),
            'email_verified_at' => '2022-08-19 18:34:11'
        ]);

        $admin->roles()->attach($adminRole);
        $helpdesk->roles()->attach($helpdeskRole);
        $contributor->roles()->attach($contributorRole);
        $student->roles()->attach($studentRole);
    }
}
