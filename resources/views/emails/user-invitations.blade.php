@component('mail::message')
# EXETAT

Hello {{ $name }}
We are inviting you to join our platforn as a {{$role}} , if you are interested click on the button below...
@component('mail::button', ['url' => route('add-user-via-mail',['name'=>$name,'email'=>$email,'role'=>$role,'number'=>$number])])
Create Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
