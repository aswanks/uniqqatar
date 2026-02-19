<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CpdeventsController;
use App\Http\Controllers\AdvertismentController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebContentsController;
use App\Http\Controllers\WingsController;
use App\Http\Controllers\UsefullinkController;
use App\Http\Controllers\OfferCategoryController;
use App\Http\Controllers\InformationsController;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\BlogcategoriesController;
use App\Http\Controllers\MobileAdvertisementsController;
use App\Http\Controllers\MobileGalleryController;
use App\Http\Controllers\Api\NotificationController;





use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
Use Illuminate\Support\Facades\Artisan;
//use Hash;

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

 # test password
//  Route::get('password', function(){
//      $p= Hash::make('0');
//      echo $p;
//  });
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    dd($exitCode);
    // return what you want
});
# To load Home page - 15/06/2021
Route::get('/', [HomeController::class, 'homeControllerIndex'])->name('homes.index');
# To load About page - 16/06/2021
Route::get('about', [HomeController::class, 'homeControllerAbout'])->name('homes.about');

Route::get('wings', [HomeController::class, 'wings'])->name('homes.wings');

# To load Contact page - 16/06/2021
Route::get('contact', [HomeController::class, 'homeControllerContact'])->name('homes.contact');
# To load Blog page - 16/06/2021
Route::get('blog', [HomeController::class, 'homeControllerBlog'])->name('homes.blog');
# To load Blog-Post page - 16/06/2021
Route::get('news/{id}/blog_Post', [HomeController::class, 'homeControllerBlogPost'])->name('homes.blog.post');
# To load Events page - 16/06/2021
Route::get('allevents', [HomeController::class, 'homeControllerEvents'])->name('homes.events');
# To load one Events page - 04/12/2021
Route::get('events/{id}/event', [HomeController::class, 'event'])->name('homes.events.one');
# To load Patron page - 16/06/2021
Route::get('patron', [HomeController::class, 'homeControllerPatron'])->name('homes.patron');
# To Aboutus_N_members page - 16/06/2021
Route::get('aboutus_N_members', [HomeController::class, 'homeControllerAboutus_N_Members'])->name('homes.aboutus_N_members');

Route::get('useful_links', [HomeController::class, 'usefull_links'])->name('homes.usefull_links');
Route::get('cpd_events', [HomeController::class, 'cpd_events'])->name('homes.cpd_events');
Route::get('cpdevents', [HomeController::class, 'cpdevents'])->name('homes.cpdevents');
Route::get('cpdevents/{slug}',[HomeController::class, 'homeControllerCpdeventPost'])->name('homes.cpdevent.post');
Route::get('Advertisment',[HomeController::class, 'display'])->name('homes.advertisment');
Route::get('Offerpage',[OfferController::class, 'offerdisplay'])->name('homes.offers');


# To load Mission & Vission page - 16/06/2021
Route::get('missionNvision', [HomeController::class, 'homeControllerMissionVission'])->name('homes.mission.vission');
# To load Media page - 16/06/2021
Route::get('media', [GalleryController::class, 'galleryControllerMedia'])->name('gallery.media');
# To load Staff page - 16/06/2021
// Route::get('staff', [HomeController::class, 'homeControllerStaff'])->name('homes.staff');

# Print Hashed Password for testing - 16/06/2021
Route::get('password', [UserController::class, 'userControllerTestPassword']);
# Admin Login page - 16/06/2021
Route::get('admin/login', [UserController::class, 'userControllerLogin'])->name('users.login');
# Admin Do Login - 16/06/2021
Route::post('admin/login', [UserController::class, 'userControllerDoLogin'])->name('users.doLogin');
# Admin Area - 16/06/2021
Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {

    # To Admin Dashboard - 16/06/2021
    Route::get('home', [UserController::class, 'userControllerDashboard'])->name('users.dashboard');
    # To Admin Logout - 16/06/2021
    Route::get('logout', [UserController::class, 'userControllerLogout'])->name('users.logout');
    # To Edit Admin Profile - 16/06/2021
    Route::get('profile', [UserController::class, 'userControllerProfileEdit'])->name('users.profile.edit');
    # To Update Admin Profile - 16/06/2021
    Route::post('update', [UserController::class, 'userControllerProfileUpdate'])->name('users.profile.update');
    # To Change Admin Password - 16/06/2021
    Route::post('resetPassword', [UserController::class, 'userControllerChangePassword'])->name('users.password.change');
    
    Route::get('about', [WebContentsController::class, 'index'])->name('admin.about');
    Route::get('about/add', [WebContentsController::class, 'aboutadd'])->name('admin.about.add');
    Route::post('about/add', [WebContentsController::class, 'aboutControllerStore'])->name('admin.about.store');
    Route::delete('about/{id}/delete', [WebContentsController::class, 'aboutControllerDelete'])->name('about.delete');
    Route::get('about/{id}/edit', [WebContentsController::class, 'aboutControllerEdit'])->name('about.edit');
    Route::post('about/update', [WebcontentsController::class, 'aboutControllerUpdate'])->name('about.update');

    Route::get('wings', [WingsController::class, 'index'])->name('admin.wings');
    Route::get('wings/add', [WingsController::class, 'wingsadd'])->name('admin.wings.add');
    Route::post('wings/add', [WingsController::class, 'wingsControllerStore'])->name('admin.wings.store');
    Route::delete('wings/{id}/delete', [WingsController::class, 'wingsControllerDelete'])->name('wings.delete');
    Route::get('wings/{id}/edit', [WingsController::class, 'wingsControllerEdit'])->name('wings.edit');
    Route::post('wings/update', [WingsController::class, 'wingsControllerUpdate'])->name('wings.update');

    Route::get('usefullinks', [UsefullinkController::class, 'usefullinkControllerIndex'])->name('usefullink.index');
    Route::get('usefullinks/add', [UsefullinkController::class, 'usefullinkControllerCreate'])->name('usefullink.add');
    Route::post('usefullinks/add', [UsefullinkController::class, 'usefullinkControllerStore'])->name('usefullink.store');
    Route::delete('usefullinks/{id}/delete', [UsefullinkController::class, 'usefullinkControllerDelete'])->name('usefullink.delete');
    Route::get('usefullinks/{id}/edit', [UsefullinkController::class, 'usefullinkControllerEdit'])->name('usefullink.edit');
    Route::post('usefullinks/update/{id}', [UsefullinkController::class, 'usefullinkControllerUpdate'])->name('usefillink.update');
    Route::any('usefullinks/delete/{id}', [UsefullinkController::class, 'deletepdf'])->name('usefullink.delete.delete');

    #TO CRUD Information - 03/07/2024
    Route::get('informations',[InformationsController::class,'index'])->name('information.index');
    Route::get('informations/add', [InformationsController::class, 'create'])->name('information.add');
    Route::post('informations/add', [InformationsController::class, 'store'])->name('information.store');
    Route::delete('informations/{id}/delete', [InformationsController::class, 'destroy'])->name('information.delete');
    Route::get('informations/{id}/edit', [InformationsController::class, 'edit'])->name('information.edit');
    Route::post('informations/update', [InformationsController::class, 'update'])->name('information.update');
    
    #TO CRUD Blogs - 03/07/2024
    Route::get('blogs',[BlogsController::class,'index'])->name('blog.index');
    Route::get('blogs/add', [BlogsController::class, 'create'])->name('blog.add');
    Route::post('blogs/add', [BlogsController::class, 'store'])->name('blog.store');
    Route::delete('blogs/{id}/delete', [BlogsController::class, 'destroy'])->name('blog.delete');
    Route::get('blogs/{id}/edit', [BlogsController::class, 'edit'])->name('blog.edit');
    Route::post('blogs/update', [BlogsController::class, 'update'])->name('blog.update');

    Route::get('blogcategory', [BlogcategoriesController::class, 'index'])->name('blogcategory.index');
    Route::get('blogcategory/search', [BlogcategoriesController::class, 'blogcategoriesControllerSearch'])->name('blogcategory.search');
    Route::get('blogcategory/add', [BlogcategoriesController::class, 'create'])->name('blogcategory.add');
    Route::post('blogcategory/add', [BlogcategoriesController::class, 'store'])->name('blogcategory.store');
    Route::delete('blogcategory/delete/{id}', [BlogcategoriesController::class, 'destroy'])->name('blogcategory.delete');
    Route::get('blogcategory/{id}/edit', [BlogcategoriesController::class, 'edit'])->name('blogcategory.edit');
    Route::post('blogcategory/update', [BlogcategoriesController::class, 'update'])->name('blogcategory.update');


    # To View all Events - 16/06/2021
    Route::get('events', [EventController::class, 'eventControllerIndexEvent'])->name('events.index');
    # To Add Event - 16/06/2021
    Route::get('events/add', [EventController::class, 'eventControllerAddEvent'])->name('events.add');
    # To Store Event - 16/06/2021
    Route::post('events/add', [EventController::class, 'eventControllerStoreEvent'])->name('events.store');
    # To Delete Event - 16/06/2021
    Route::delete('event/{id}/delete', [EventController::class, 'eventControllerDelete'])->name('events.delete');
    # To Edit Event - 16/06/2021
    Route::get('events/{id}/edit', [EventController::class, 'eventControllerEdit'])->name('events.edit');
    # To Update Event - 16/06/2021
    Route::post('events/update', [EventController::class, 'eventControllerUpdate'])->name('events.update');
    # To Search Event - 16/06/2021
    Route::get('events/search', [EventController::class, 'eventControllerSearch'])->name('events.search');
    # To View all Event Feed backs - 17/12/2025
    Route::post('event-feedbacks/export-pdf', [EventController::class, 'exporteventFeedBackPDF'])->name('events.feedback.exportpdf');
    Route::post('eventfeedbacks/export-excel', [EventController::class, 'exporteventFeedBackExcel'])->name('eventfeedback.export.excel');
    Route::get('event-feedbacks', [EventController::class, 'eventFeedbacklist'])->name('eventfeedbacks');
    Route::get('event-feedbacks/search', [EventController::class, 'eventFeedBackControllerSearch'])->name('eventfeedback.search');

    # To View all Event Feed back Questions- 23/12/2025
    Route::get('event-feedback-questions', [EventController::class, 'eventFeedbackQutlist'])->name('eventfeedback.questions.list');
    Route::get('event-feedback-questions/search', [EventController::class, 'eventFeedbackQutSearch'])->name('eventfeedback.questions.search');
    Route::get('event-feedback-questions/create', [EventController::class, 'eventFeedbackQutCreate'])->name('eventfeedback.questions.create');
    Route::post('event-feedback-questions/store', [EventController::class, 'eventFeedbackQutStore'])->name('eventfeedback.questions.store');
    Route::get('event-feedback-questions/edit/{id}', [EventController::class, 'eventFeedbackQutEdit'])->name('eventfeedback.questions.edit');
    Route::post('event-feedback-questions/update/{id}', [EventController::class, 'eventFeedbackQutUpdate'])->name('eventfeedback.questions.update');
    Route::delete('event-feedback-questions/delete/{id}', [EventController::class, 'eventFeedbackQutDelete'])->name('eventfeedback.questions.delete');


     #10/07/24
    Route::get('eventregisterform',[EventController::class,'eventregisterform'])->name('events.register.index');
    Route::delete('eventregisterdelete/{id}',[EventController::class,'eventregisterdelete'])->name('events.register.delete');
    Route::any('event/update-status',[EventController::class, 'updateStatus'])->name('events.status');

    Route::get('/search', [ EventController::class,'eventregisterform'])->name('search');
    Route::post('/export-pdf',[ EventController::class,'exportPDF'])->name('export.pdf');
    Route::post('/export-excel',[ EventController::class,'exportExcel'])->name('export.excel');


    Route::post('/send-notification', ['uses'=> 'App\Http\Controllers\Api\NotificationController@sendNotification']);



    # To View all Gallery - 16/06/2021
    Route::get('gallery', [GalleryController::class, 'galleryControllerAdminIndex'])->name('gallery.admin.index');
    # To Add Gallery - 16/06/2021
    Route::get('gallery/add', [GalleryController::class, 'galleryControllerAdd'])->name('gallery.add');
    # To Store Gallery - 16/06/2021
    Route::post('gallery/add', [GalleryController::class, 'galleryControllerStore'])->name('gallery.store');
    # To Delete Gallery - 16/06/2021
    Route::delete('gallery/{id}/delete', [GalleryController::class, 'galleryControllerDelete'])->name('gallery.destroy');
    # To Edit Gallery - 16/06/2021
    Route::get('gallery/{id}/edit', [GalleryController::class, 'galleryControllerEdit'])->name('gallery.edit');
    # To Update Gallery - 16/06/2021
    Route::post('gallery/update', [GalleryController::class, 'galleryControllerUpdate'])->name('gallery.update');
    # To Search Gallery - 17/06/2021
    Route::get('gallery/search', [GalleryController::class, 'galleryControllerSearch'])->name('gallery.search');

    # To View all Gallery - 5/09/2024
    Route::get('mobilegallery', [MobileGalleryController::class, 'index'])->name('mobilegallery.admin.index');
    # To Add Gallery - 5/09/2024
    Route::get('mobilegallery/add', [MobileGalleryController::class, 'add'])->name('mobilegallery.add');
    # To Store Gallery - 5/09/2024
    Route::post('mobilegallery/add', [MobileGalleryController::class, 'store'])->name('mobilegallery.store');
    # To Delete Gallery - 5/09/2024
    Route::delete('mobilegallery/{id}/delete', [MobileGalleryController::class, 'delete'])->name('mobilegallery.destroy');
    # To Edit Gallery - 5/09/2024
    Route::get('mobilegallery/{id}/edit', [MobileGalleryController::class, 'edit'])->name('mobilegallery.edit');
    # To Update Gallery - 5/09/2024
    Route::post('mobilegallery/update', [MobileGalleryController::class, 'update'])->name('mobilegallery.update');
    # To Search Gallery - 5/09/2024
    Route::get('mobilegallery/search', [MobileGalleryController::class, 'search'])->name('mobilegallery.search');




    # To List all News - 17/06/2021
    Route::get('news', [NewsController::class, 'newsControllerIndex'])->name('news.index');
    # To Create News - 18/06/2021
    Route::get('news/add', [NewsController::class, 'newsControllerCreate'])->name('news.create');
    # To Store News - 18/06/2021
    Route::post('news/add', [NewsController::class, 'newsControllerStore'])->name('news.store');
    # To Edit News - 18/06/2021
    Route::get('news/{id}/edit', [NewsController::class, 'newsControllerEdit'])->name('news.edit');
    # To Delete other images of News 18/06/2021
    Route::delete('news/other-image/{id}/delete', [NewsController::class, 'deleteotherimages'])->name('news.otherimaes.delete');
    # To Update News 18/06/2021
    Route::post('news/update', [NewsController::class, 'newsControllerUpdate'])->name('news.update');
    # To Delete News - 18/06/2021
    Route::delete('news/{id}/delete', [NewsController::class, 'newsControllerDelete'])->name('news.delete');
    # To Search News - 18/06/2021
    Route::get('news/search', [NewsController::class, 'newsControllerSearch'])->name('news.search');
    
     # To add,edit,delete offers by admin - 05/12/23
    Route::get('offers', [OfferController::class, 'offersControllerIndex'])->name('offer.index');
    Route::get('offers/search', [OfferController::class, 'offersControllerSearch'])->name('offer.search');
    Route::get('offers/add', [OfferController::class, 'offersControllerCreate'])->name('offer.add');
    Route::post('offers/add', [OfferController::class, 'offersControllerStore'])->name('offer.store');
    Route::delete('offers/{id}/delete', [OfferController::class, 'offersControllerDelete'])->name('offer.delete');
    Route::get('offers/{id}/edit', [OfferController::class, 'offersControllerEdit'])->name('offer.edit');
    Route::post('offers/update', [OfferController::class, 'offersControllerUpdate'])->name('offer.update');
    
    Route::get('offerscategory', [OfferCategoryController::class, 'index'])->name('offercategory.index');
    Route::get('offerscategory/search', [OfferCategoryController::class, 'offerscategoryControllerSearch'])->name('offercategory.search');
    Route::get('offercategory/add', [OfferCategoryController::class, 'create'])->name('offercategory.add');
    Route::post('offercategory/add', [OfferCategoryController::class, 'store'])->name('offercategory.store');
    Route::delete('offerscategory/delete/{id}', [OfferCategoryController::class, 'destroy'])->name('offercategory.delete');
    Route::get('offerscategory/{id}/edit', [OfferCategoryController::class, 'edit'])->name('offercategory.edit');
    Route::post('offerscategory/update', [OfferCategoryController::class, 'update'])->name('offercategory.update');


    #To add,edit,delete advertisment - 15/12/23
    Route::get('advertisment',[AdvertismentController::class,'index'])->name('advertisment.index');
    Route::get('advertisment/search',[AdvertismentController::class,'advertismentControllerSearch'])->name('advertisment.search');
    Route::get('advertisment/add', [AdvertismentController::class, 'advertismentAdd'])->name('advertisment.add');
    Route::post('advertisment/add', [AdvertismentController::class, 'advertismentStore'])->name('advertisment.store');
    Route::delete('advertisment/{id}/delete', [AdvertismentController::class, 'advertismentDelete'])->name('advertisment.delete');
    Route::get('advertisment/{id}/edit', [AdvertismentController::class, 'advertismentEdit'])->name('advertisment.edit');
    Route::post('advertisment/update', [AdvertismentController::class, 'advertismentUpdate'])->name('advertisment.update');
    
    #To add,edit,delete advertisment - 25/07/24
    Route::get('mobileadvertisment',[MobileAdvertisementsController::class,'index'])->name('mobileadvertisement.index');
    Route::get('mobileadvertisment/add', [MobileAdvertisementsController::class, 'advertismentAdd'])->name('mobileadvertisement.add');
    Route::post('mobileadvertisment/add', [MobileAdvertisementsController::class, 'advertismentStore'])->name('mobileadvertisement.store');
    Route::delete('mobileadvertisment/{id}/delete', [MobileAdvertisementsController::class, 'advertismentDelete'])->name('mobileadvertisement.delete');
    Route::get('mobileadvertisment/{id}/edit', [MobileAdvertisementsController::class, 'advertismentEdit'])->name('mobileadvertisement.edit');
    Route::post('mobileadvertisment/update', [MobileAdvertisementsController::class, 'advertismentUpdate'])->name('mobileadvertisement.update');


    # To Add CPD Events page - 23/11/2023
    Route::get('cpdevents', [CpdeventsController::class, 'cpdeventsControllerIndex'])->name('cpdevents.index');
    # To Search CPD Events  - 23/11/2023
    Route::get('cpdevents/search', [CpdeventsController::class, 'cpdeventsControllerSearch'])->name('cpdevents.search');
    # To create Cpd Events - 23/11/2023
    Route::get('cpdevents/add', [CpdeventsController::class, 'cpdeventsControllerCreate'])->name('cpdevents.create');
    # To Store Cpd Events - 23/11/2023
    Route::post('cpdevents/add', [CpdeventsController::class, 'cpdeventsControllerStore'])->name('cpdevents.store');
     # To Edit CPD Events - 23/11/2023
    Route::get('cpdevents/{id}/edit', [CpdeventsController::class, 'cpdeventsControllerEdit'])->name('cpdevents.edit');
    # To Delete CPD Events - 23/11/2023
    Route::delete('cpdevents/{id}/delete', [CpdeventsController::class, 'cpdeventsControllerDelete'])->name('cpdevents.delete');
    # To Update CPD Events 24/11/2023
    Route::post('cpdevents/update', [CpdeventsController::class, 'cpdeventsControllerUpdate'])->name('cpdevents.update');
    # To List all Members - 18/06/2021
    Route::get('members', [RegistrationController::class, 'registrationControllerIndex'])->name('members.index');
    # To List all Members - 18/06/2021
    Route::post('members/search/pdf', [RegistrationController::class, 'searchpdf'])->name('members.search.pdf');
    # To PDF List all searched Members - 07/03/2022
    Route::post('members/export/excel', [RegistrationController::class, 'exportExcel'])->name('members.export.excel');
    # To Approve Member - 18/06/2021
    //Route::get('members/search', [RegistrationController::class, 'getSearchMember'])->name('members.search');
    # To Approve Member - 18/06/2021
    Route::post('members/approve', [RegistrationController::class, 'registrationControllerApprove'])->name('members.approve');
    # To Reject Member - 18/06/2021
    Route::post('memebers/reject', [RegistrationController::class, 'registrationControllerReject'])->name('memebers.reject');
    # To Delete Member - 23/10/2021
    Route::delete('memebers/{id}/delete', [RegistrationController::class, 'destroy'])->name('memebers.delete');
    # To View more details about Member - 19/06/2021
    Route::get('memebers/{id}/view_more', [RegistrationController::class, 'registrationControllerViewMore'])->name('members.view.more');
    # To Passwrord Reset - 04/12/2021
    Route::get('members/password-reset/{id}', [RegistrationController::class, 'showPasswordResetForm'])->name('members.password.reset.form');
    # To Passwrord Reset - 04/12/2021
    Route::post('members/password-reset/{id}', [RegistrationController::class, 'saveNewPassword'])->name('members.password.reset.update');

    Route::post('members/resendCard', [RegistrationController::class, 'registrationControllerMembershipResend'])->name('members.resend.card');

    # To edit member details about Member - 05/06/2022
    Route::get('memebers/{id}/edit', [RegistrationController::class, 'registrationedit'])->name('members.edit');
    # To edit member details about Member - 05/06/2022
    Route::put('memebers/{id}', [RegistrationController::class, 'registrationupdate'])->name('members.update');

    # To share details with Member or other - 27/07/2021
    Route::post('memebers/sharedetails', [RegistrationController::class, 'sharememberdetails'])->name('memebers.share');
    # To share all member grid details with Member or other - 28/07/2021
    Route::post('memebers/sharealldetails', [RegistrationController::class, 'shareallmembersdetails'])->name('memebers.sharegrid');

    # To share dropdown selected status members details will be sent to above textbox email. - 15/01/2022
    Route::post('memebers/sharestatusdetails', [RegistrationController::class, 'sharestatusmembersdetails'])->name('memebers.sharegrid.status');

    # To Sent mail to Aproved Members - 27/07/2021
    Route::get('members/sentmail', [RegistrationController::class, 'sentmailtomembers'])->name('members.sentmail');
    # To Sent mail to Aproved Members - 27/07/2021
    Route::post('members/sentmail', [RegistrationController::class, 'postsentmailtomembers'])->name('members.postsentmail');
    
    Route::post('members/details/taken', [RegistrationController::class, 'detailstaken'])->name('members.detailstaken');

});

# To confirm Registration details - 06/06/2022
Route::get('registration/{id}/confirm/{token}', [RegistrationController::class, 'registration_confirm'])->name('members.register.confirm');


# To load Registration Form - 19/06/2021
Route::get('registration', [RegistrationController::class, 'registrationControllerRegistrationForm'])->name('members.register');
# To Do Member Registration - 19/06/2021
Route::post('newregistration', [RegistrationController::class, 'registrationControllerRegistration'])->name('memebers.registration');
# To Check Renewal of Member - 19/06/2021
Route::get('renewal/check', [RegistrationController::class, 'registrationControllerRenewalCheck'])->name('members.renewal.check');
# To Do Check Renewal of Member - 19/06/2021
Route::post('renewal/do/check', [RegistrationController::class, 'registrationControllerRenewalDoCheck'])->name('members.renewal.do.check');
# To load Renewal Form - 19/06/2021
Route::get('renewal', [RegistrationController::class, 'registrationControllerRenewalForm'])->name('memebers.renewal');
# To Do Renewal Membership - 19/06/2021
Route::post('renewal/do', [RegistrationController::class, 'registrationControllerDoRenewal'])->name('members.do.renewal');

# To load Registration Form - 19/06/2021
Route::get('dashboard', [RegistrationController::class, 'login'])->name('members.dashboard');


// # To load login Form - 19/11/2023
// Route::get('login', [AuthController::class, 'index'])->name('members.login');
// # To view dashboard Form - 19/11/2023
// Route::post('dashboard',[AuthController::class,'doLogin'])->name('members.dashboard');
// # To load offers Form - 19/11/2023
// Route::get('offerpage', [AuthController::class, 'offer'])->name('members.offer');
// # To load only active member view offer details Form - 19/11/2023
// Route::get('offerpage/{slug}', [AuthController::class, 'postoffer'])->name('members.offerdetails');
// # To load membercard Form - 19/11/2023
// Route::get('membercard',[AuthController::class,'membershipcard'])->name('members.viewcard');
// //Route::get('offers/search', [AuthController::class, 'offerSearch'])->name('offer.search');

// # To forgot password section Form - 19/11/2023
// Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('members.password');
// Route::post('check-forgot-account',[AuthController::class, 'checkForgotAccount'])->name('members.password.check');
// Route::get('password/reset/{token}',[AuthController::class, 'verifyResetPasswordLink'])->name('members.password.verify');
// Route::post('reset-password',[AuthController::class, 'doResetPassword'])->name('members.password.change');
// Route::get('password/{user_id}',[AuthController::class,'password'])->name('customer.paswwordreset');
// Route::post('update-password/{user_id}',[AuthController::class,'updatePassword'])->name('customer.update');
// Route::get('logout', [AuthController::class, 'logout'])->name('users.logout');