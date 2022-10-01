<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Contributor\ContributorController;
use App\Http\Controllers\Student\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', App\Http\Controllers\Admin\UsersController::class, 'index')->middleware(['auth','verified','locked','admin'])->name('index');
Route::get('/dashboard', App\Http\Controllers\Admin\UsersController::class, 'index')->middleware(['auth','verified','locked','admin'])->name('dashboard');
Route::get('UserDetails/{user}', [App\Http\Controllers\Admin\UsersController::class, 'userDetails'])->middleware(['auth','verified','locked','admin'])->name('user-details');
Route::get('/AddUser', [App\Http\Controllers\Admin\UsersController::class, 'adduser'])->middleware(['auth','verified','locked','admin'])->name('add-user');

Route::get('/libraries-approval', [App\Http\Controllers\Admin\UsersController::class, 'librariesapproval'])->middleware(['auth','verified','locked','admin'])->name('libraries-approval');
Route::get('/edit-publication-{id}', [App\Http\Controllers\Admin\UsersController::class, 'editpublication'])->middleware(['auth','verified','locked','admin'])->name('edit-publication');
Route::post('/update-publication-{publication}', [App\Http\Controllers\Admin\UsersController::class, 'updatepublication'])->middleware(['auth','verified','locked','admin'])->name('update-publication');
Route::get('/publication-{id}', [App\Http\Controllers\Admin\UsersController::class, 'publication'])->middleware(['auth','verified','locked','admin'])->name('publication');
Route::get('/approve-publication-{id}', [App\Http\Controllers\Admin\UsersController::class, 'approvepublication'])->middleware(['auth','verified','locked','admin'])->name('approve-publication');
Route::get('/delete-publication-{id}', [App\Http\Controllers\Admin\UsersController::class, 'deletepublication'])->middleware(['auth','verified','locked','admin'])->name('delete-publication');

Route::get('/reviews-approval', [App\Http\Controllers\Admin\UsersController::class, 'reviewsapproval'])->middleware(['auth','verified','locked','admin'])->name('reviews-approval');
Route::get('/review-{id}', [App\Http\Controllers\Admin\UsersController::class, 'review'])->middleware(['auth','verified','locked','admin'])->name('review');
Route::get('/approve-reviews-{id}', [App\Http\Controllers\Admin\UsersController::class, 'approvereviews'])->middleware(['auth','verified','locked','admin'])->name('approve-reviews');
Route::get('/delete-reviews-{id}', [App\Http\Controllers\Admin\UsersController::class, 'deletereviews'])->middleware(['auth','verified','locked','admin'])->name('delete-reviews');

Route::get('/edit-package-{id}', [App\Http\Controllers\Admin\UsersController::class, 'editpackage'])->middleware(['auth','verified','locked','admin'])->name('edit-package');
Route::post('/update-package-{package}', [App\Http\Controllers\Admin\UsersController::class, 'updatepackage'])->middleware(['auth','verified','locked','admin'])->name('update-package');
Route::get('/delete-package-{id}', [App\Http\Controllers\Admin\UsersController::class, 'deletepackage'])->middleware(['auth','verified','locked','admin'])->name('delete-package');
Route::get('/new-package', [App\Http\Controllers\Admin\UsersController::class, 'newpackage'])->middleware(['auth','verified','locked','admin'])->name('new-package');
Route::post('/store-package', [App\Http\Controllers\Admin\UsersController::class, 'storepackage'])->middleware(['auth','verified','locked','admin'])->name('store-package');

Route::get('/edit-section-{id}', [App\Http\Controllers\Admin\UsersController::class, 'editsection'])->middleware(['auth','verified','locked','admin'])->name('edit-section');
Route::post('/update-section-{section}', [App\Http\Controllers\Admin\UsersController::class, 'updatesection'])->middleware(['auth','verified','locked','admin'])->name('update-section');
Route::get('/delete-section-{id}', [App\Http\Controllers\Admin\UsersController::class, 'deletesection'])->middleware(['auth','verified','locked','admin'])->name('delete-section');
Route::get('/new-section', [App\Http\Controllers\Admin\UsersController::class, 'newsection'])->middleware(['auth','verified','locked','admin'])->name('new-section');
Route::post('/store-section', [App\Http\Controllers\Admin\UsersController::class, 'storesection'])->middleware(['auth','verified','locked','admin'])->name('store-section');

Route::get('/edit-option-{id}', [App\Http\Controllers\Admin\UsersController::class, 'editoption'])->middleware(['auth','verified','locked','admin'])->name('edit-option');
Route::post('/update-option-{option}', [App\Http\Controllers\Admin\UsersController::class, 'updateoption'])->middleware(['auth','verified','locked','admin'])->name('update-option');
Route::get('/delete-option-{id}', [App\Http\Controllers\Admin\UsersController::class, 'deleteoption'])->middleware(['auth','verified','locked','admin'])->name('delete-option');
Route::get('/new-option', [App\Http\Controllers\Admin\UsersController::class, 'newoption'])->middleware(['auth','verified','locked','admin'])->name('new-option');
Route::post('/store-option', [App\Http\Controllers\Admin\UsersController::class, 'storeoption'])->middleware(['auth','verified','locked','admin'])->name('store-option');

Route::get('/edit-question-{id}', [App\Http\Controllers\Admin\UsersController::class, 'editquestion'])->middleware(['auth','verified','locked','admin'])->name('edit-question');
Route::post('/update-question-{question}', [App\Http\Controllers\Admin\UsersController::class, 'updatequestion'])->middleware(['auth','verified','locked','admin'])->name('update-question');
Route::get('/delete-question-{id}', [App\Http\Controllers\Admin\UsersController::class, 'deletequestion'])->middleware(['auth','verified','locked','admin'])->name('delete-question');
Route::get('/questions-approval', [App\Http\Controllers\Admin\UsersController::class, 'questionsapproval'])->middleware(['auth','verified','locked','admin'])->name('questions-approval');
Route::get('/question-{id}', [App\Http\Controllers\Admin\UsersController::class, 'question'])->middleware(['auth','verified','locked','admin'])->name('question');
Route::get('/approve-question-{id}', [App\Http\Controllers\Admin\UsersController::class, 'approvequestion'])->middleware(['auth','verified','locked','admin'])->name('approve-question');
Route::get('/assign-question-{id}', [App\Http\Controllers\Admin\UsersController::class, 'assignquestion'])->middleware(['auth','verified','locked','admin'])->name('assign-question');
Route::post('/question-enabled', [App\Http\Controllers\Admin\UsersController::class, 'questionenabled'])->middleware(['auth','verified','locked','admin'])->name('question-enabled');
Route::post('/store-assign-question', [App\Http\Controllers\Admin\UsersController::class, 'storeassignquestion'])->middleware(['auth','verified','locked','admin'])->name('store-assign-question');


Route::post('/RegisterByEmail', [App\Http\Controllers\Admin\UsersController::class, 'storeuser'])->name('register-by-email');
Route::post('/SendMail', [App\Http\Controllers\Admin\UsersController::class, 'sendMail'])->middleware(['auth','verified','locked','admin'])->name('send-mail');
Route::get('Create/{name}/{email}/{role}/{number}', [App\Http\Controllers\Admin\UsersController::class, 'addUserViaMail'])->middleware(['Login'])->name('add-user-via-mail');


Route::namespace('App\Http\Controllers\Admin')->group(function(){
    Route::resource('users',UsersController::class)->except(['show','create','store']);
});
Route::Put('/{user}', [App\Http\Controllers\Admin\UsersController::class, 'lockuser'])->middleware(['auth','verified'])->name('lockuser');
Route::post('/change', [App\Http\Controllers\Admin\UsersController::class, 'changePassword'])->middleware(['auth','verified'])->name('change_password');
Route::get('/change-password',[App\Http\Controllers\Admin\UsersController::class, 'gotochangepassword'])->middleware(['auth','verified','locked'])->name('gotochangepassword');
Route::get('/Profile', [App\Http\Controllers\Admin\UsersController::class, 'profile'])->middleware(['auth','verified','locked'])->name('profile');
Route::get('/libraries', [App\Http\Controllers\Admin\UsersController::class, 'libraries'])->middleware(['auth','verified','locked'])->name('libraries');
Route::get('/new-publication', [App\Http\Controllers\Admin\UsersController::class, 'newpublication'])->middleware(['auth','verified','locked'])->name('new-publication');
Route::get('/view-publication-{id}', [App\Http\Controllers\Admin\UsersController::class, 'viewpublication'])->middleware(['auth','verified','locked'])->name('view-publication');
Route::post('/store-publication', [App\Http\Controllers\Admin\UsersController::class, 'storepublication'])->middleware(['auth','verified','locked'])->name('store-publication');
Route::get('/reviews', [App\Http\Controllers\Admin\UsersController::class, 'reviews'])->middleware(['auth','verified','locked'])->name('reviews');
Route::get('/new-reviews', [App\Http\Controllers\Admin\UsersController::class, 'newreviews'])->middleware(['auth','verified','locked'])->name('new-reviews');
Route::get('/view-reviews-{id}', [App\Http\Controllers\Admin\UsersController::class, 'viewreviews'])->middleware(['auth','verified','locked'])->name('view-reviews');
Route::post('/store-reviews', [App\Http\Controllers\Admin\UsersController::class, 'storereviews'])->middleware(['auth','verified','locked'])->name('store-reviews');
Route::get('/packages', [App\Http\Controllers\Admin\UsersController::class, 'packages'])->middleware(['auth','verified','locked'])->name('packages');
Route::get('/view-package-{id}', [App\Http\Controllers\Admin\UsersController::class, 'viewpackage'])->middleware(['auth','verified','locked'])->name('view-package');
Route::get('/sections', [App\Http\Controllers\Admin\UsersController::class, 'sections'])->middleware(['auth','verified','locked'])->name('sections');
Route::get('/view-options-{id}', [App\Http\Controllers\Admin\UsersController::class, 'viewoptions'])->middleware(['auth','verified','locked'])->name('view-options');
Route::get('/questions', [App\Http\Controllers\Admin\UsersController::class, 'questions'])->middleware(['auth','verified','locked'])->name('questions');
Route::get('/view-questions-{id}', [App\Http\Controllers\Admin\UsersController::class, 'viewquestions'])->middleware(['auth','verified','locked'])->name('view-questions');
Route::get('/new-question', [App\Http\Controllers\Admin\UsersController::class, 'newquestion'])->middleware(['auth','verified','locked'])->name('new-question');
Route::post('/store-question', [App\Http\Controllers\Admin\UsersController::class, 'storequestion'])->middleware(['auth','verified','locked'])->name('store-question');
Route::get('ExportUsersInformation/{user}', [App\Http\Controllers\Admin\UsersController::class, 'export_users_information'])->middleware(['auth','verified','locked'])->name('export-users-information');


/////student
Route::get('student/', App\Http\Controllers\Student\StudentController::class, 'index')->middleware(['auth','verified','locked'])->name('student.index');
/////contributor
Route::get('contributor/', App\Http\Controllers\Contributor\ContributorController::class, 'index')->middleware(['auth','verified','locked','contributor'])->name('contributor.index');


require __DIR__.'/auth.php';
