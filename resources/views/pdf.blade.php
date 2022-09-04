<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic - Bootstrap 5 HTML, VueJS, React, Angular, Asp.Net Core, Blazor, Django, Flask & Laravel Admin Dashboard Theme
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head>
		<title>Metronic - the world's #1 selling Bootstrap Admin Theme Ecosystem for HTML, Vue, React, Angular &amp; Laravel by Keenthemes</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		
		<!--end::Global Stylesheets Bundle-->
		<style>
			

.student-profile .card .card-header .profile_img {
  width: 250px;
  height: 250px;
  object-fit: cover;
  margin: 10px auto;
  margin-left:215px;
  border: 10px solid #ccc;
  border-radius: 50%;
  display: flex;
  justify-content: center;
}


		</style>
	</head>
	<!--end::Head-->

<!-- Student Profile -->
<div class="student-profile py-4">
  <div class="container">
    <div class="row" style="">
        <div class="card shadow-sm">
          <div class="card-header bg-transparent text-center">
            <img style="text-align: center;justify-content: center;display: flex;" class="profile_img" src="{{asset('/storage/'.$user->profilepic)}}" alt="">
            <h1 style="text-align: center;justify-content: center;display: flex;">{{$user->name}}</h1>
          
            <hr>
			<h2 style="text-align: center;justify-content: center;display: flex;">General Information</h2>
            <h4 class="mb-0" style="text-align: center;justify-content: center;display: flex;">User Id : {{ implode(',', $user->roles()->get()->pluck('name')->toArray())}} </h5>
			<br>
            <h4 class="mb-0" style="text-align: center;justify-content: center;display: flex;">Role : {{$user->userid}} </h5>
			<br>
            <h4 class="mb-0" style="text-align: center;justify-content: center;display: flex;">Name : {{$user->name}} </h5>
			<br>
            <h4 class="mb-0" style="text-align: center;justify-content: center;display: flex;">Email : {{$user->email}} </h5>
			<br>
            <h4 class="mb-0" style="text-align: center;justify-content: center;display: flex;">Phone Number : {{$user->number}} </h5>

      </div>
	  
         
               
             
          
      
    </div>
  </div>
</div>





	