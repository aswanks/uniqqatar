<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\ErrorLogController;


use Carbon\Carbon;
Use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
# To load Login Form - 18/12/2023
Route::post('login',['uses' =>'App\Http\Controllers\Api\AuthController@index']);

Route::post('newregistration', ['uses' =>'App\Http\Controllers\Api\RegistrationController@registrationControllerRegistration']);

# To load dashboard Form - 18/12/2023
Route::post('dashboard',['uses' =>'App\Http\Controllers\Api\AuthController@doLogin']);
# To load offerpage Form - 18/12/2023
Route::post('offerpage', ['uses' =>'App\Http\Controllers\Api\AuthController@offer']);
# To load only active member view offer details Form - 18/12/2023
Route::post('offerpage/{slug}', ['uses' =>'App\Http\Controllers\Api\AuthController@postoffer']);
# To load membercard Form - 18/12/2023
Route::post('membercard',['uses' =>'App\Http\Controllers\Api\AuthController@membershipcard']);

Route::post('news', ['uses' =>'App\Http\Controllers\Api\AuthController@news']);

Route::post('events', ['uses' =>'App\Http\Controllers\Api\AuthController@events']);

Route::post('my-events',['uses' =>'App\Http\Controllers\Api\AuthController@myEvents']);

Route::post('event-feedback/store', ['uses' =>'App\Http\Controllers\Api\AuthController@eventFeedbackStore']);

Route::post('logout', ['uses' =>'App\Http\Controllers\Api\AuthController@logout'])->name('users.logout');

Route::post('eventone', ['uses' =>'App\Http\Controllers\Api\AuthController@event']);

Route::post('newsone', ['uses' =>'App\Http\Controllers\Api\AuthController@newsone']);

Route::post('offerone', ['uses' =>'App\Http\Controllers\Api\AuthController@offerone']);

Route::post('about', ['uses' =>'App\Http\Controllers\Api\AuthController@about']);

Route::post('wings', ['uses' =>'App\Http\Controllers\Api\AuthController@wing']);

Route::post('usefullink', ['uses' =>'App\Http\Controllers\Api\AuthController@useful_link']);

Route::post('signup',['uses' =>'App\Http\Controllers\Api\AuthController@signup']);

Route::post('membership-info', ['uses' =>'App\Http\Controllers\Api\AuthController@renewal']);

Route::post('messages', ['uses' =>'App\Http\Controllers\Api\AuthController@message'])->name('message');

Route::post('/password/reset', ['uses' =>'App\Http\Controllers\Api\AuthController@resetPassword']);

Route::post('blogs', ['uses' =>'App\Http\Controllers\Api\AuthController@blog']);

Route::post('reference',['uses' =>'App\Http\Controllers\Api\RegistrationController@registrationControllerRegistrationForm']);

Route::post('eventregister',['uses'=>'App\Http\Controllers\Api\AuthController@eventregistration']);

Route::post('event-feed-back',['uses'=>'App\Http\Controllers\Api\AuthController@geteventFeedbackForm']);

Route::post('submit-event-feedback',['uses'=>'App\Http\Controllers\Api\AuthController@submitFeedbackForm']);
    
Route::post('event-download-certificate',['uses'=>'App\Http\Controllers\Api\AuthController@eventCertificateDownload']);

Route::post('cpd',['uses' =>'App\Http\Controllers\Api\AuthController@cpd']);

Route::post('log-error',['uses'=> 'App\Http\Controllers\Api\ErrorLogController@store']);

Route::post('deleteuser', ['uses'=> 'App\Http\Controllers\Api\AuthController@deleteUser']);

Route::post('confirmdeleteuser', ['uses'=> 'App\Http\Controllers\Api\AuthController@confirmAndDeleteUser']);




Route::post('check-email',  ['uses'=> 'App\Http\Controllers\Api\AuthController@checkEmail']);

Route::post('/send-notification', ['uses'=> 'App\Http\Controllers\Api\NotificationController@sendNotification']);

Route::post('blog/like', ['uses'=> 'App\Http\Controllers\Api\AuthController@likes']);

Route::post('blog/comment',['uses'=> 'App\Http\Controllers\Api\AuthController@addComment']);

Route::post('/blog/likes', ['uses'=> 'App\Http\Controllers\Api\AuthController@getLikes']);

Route::post('/comment/update', ['uses'=> 'App\Http\Controllers\Api\AuthController@updateComment']); // Edit user's own comment

Route::post('/comment/delete', ['uses'=> 'App\Http\Controllers\Api\AuthController@deleteComment']); // ✅ Use POST for deleting