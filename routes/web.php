<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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

/*********************General Function for Both (Front-end & Back-end) ***********************/
// CSP violation reporting endpoint (CSRF excluded in VerifyCsrfToken middleware)
Route::post('/csp-report', [App\Http\Controllers\SecurityController::class, 'cspReport']);

// Cloudflare Email Address Obfuscation: /cdn-cgi/l/email-protection#<hex> — the #fragment is never sent to the server, so return a minimal page that decodes the hash (CF algorithm) and opens mailto:, or falls back to /contact.
Route::get('/cdn-cgi/l/email-protection', function () {
	return response()
		->view('cf-email-protection-fallback')
		->header('X-Robots-Tag', 'noindex, nofollow');
});

Route::middleware(['auth', 'verified', 'throttle:6,1'])->group(function () {
	Route::post('/clear-cache', function() {

		Artisan::call('config:clear');
		Artisan::call('view:clear');
		Artisan::call('route:clear');
		return response()->noContent();
	});
});


/*********************Frontend Routes ***********************/
//Home Page
Route::get('/test/index',function(){
    echo 'Hello word';
});
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->middleware('throttle:web-pages')->name('home');
Route::get('/index.html', function () {
    return redirect()->route('home', [], 301);
});
Route::get('/index', [App\Http\Controllers\HomeController::class, 'index'])->middleware('throttle:web-pages')->name('index');
// Simplified blog routes with /blog prefix
Route::get('/blog', [App\Http\Controllers\HomeController::class, 'blogExperimental'])->middleware('throttle:web-pages')->name('blog.index');
Route::get('/blog/category/{categorySlug}', [App\Http\Controllers\HomeController::class, 'blogCategoryExperimental'])->middleware('throttle:web-pages')->name('blog.category');
Route::get('/blog/{slug}', [App\Http\Controllers\HomeController::class, 'blogdetail'])->middleware('throttle:web-pages')->name('blog.detail');
Route::get('/blogs/list', [App\Http\Controllers\Api\BlogController::class, 'list'])->middleware('throttle:web-pages')->name('blogs.list');

Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contactus'])->middleware('throttle:web-pages');
Route::post('/contact_lawyer', [App\Http\Controllers\HomeController::class, 'contact'])->middleware('throttle:web-contact');

// Unified contact form routes
Route::post('/contact/submit', [App\Http\Controllers\HomeController::class, 'contactSubmit'])->name('contact.submit')->middleware('throttle:web-contact');
Route::get('/contact/thank-you', [App\Http\Controllers\HomeController::class, 'contactThankYou'])->name('contact.thankyou');

Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->middleware(['throttle:web-pages', 'cache.headers:etag'])->name('about');

Route::get('stripe/{appointmentId}', [App\Http\Controllers\HomeController::class, 'stripe']);
Route::post('stripe', [App\Http\Controllers\HomeController::class, 'stripePost'])->name('stripe.post1')->middleware('throttle:web-booking-post');
Route::get('payment-thankyou/{appointmentId?}', [App\Http\Controllers\HomeController::class, 'paymentThankYou'])->name('payment.thankyou');

// Booking page — stricter throttle (was 52 GiB bot bandwidth in recent logs)
Route::get('/book-an-appointment', [App\Http\Controllers\HomeController::class, 'bookappointment'])->middleware('throttle:web-booking')->name('bookappointment');
Route::get('/book-an-appointment1', [App\Http\Controllers\HomeController::class, 'bookappointment1'])->middleware('throttle:web-booking')->name('bookappointment1');
Route::post('/book-an-appointment/storepaid', [App\Http\Controllers\AppointmentBookController::class, 'storepaid'])->name('stripe.post')->middleware('throttle:web-booking-post');
// Promo code validation for booking
Route::post('/promo-code/check', [App\Http\Controllers\AppointmentBookController::class, 'checkpromocode'])->middleware('throttle:web-promo');
Route::match(['get', 'post'], '/getdatetime', [App\Http\Controllers\HomeController::class, 'getdatetime'])->middleware('throttle:web-ajax');
Route::post('/getdisableddatetime', [App\Http\Controllers\HomeController::class, 'getdisableddatetime'])->middleware('throttle:web-ajax');
Route::get('page/{slug}', [App\Http\Controllers\HomeController::class, 'Page'])->middleware('throttle:web-pages')->name('page.slug');

/*********************Admin Panel Routes ***********************/
Route::prefix('admin')->group(function() {
     //Login and Logout
		Route::middleware('guest:admin')->group(function () {
			Route::get('/', [App\Http\Controllers\Auth\AdminAuthenticatedSessionController::class, 'create'])->name('admin.login.root');
			Route::get('/login', [App\Http\Controllers\Auth\AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
			Route::post('/', [App\Http\Controllers\Auth\AdminAuthenticatedSessionController::class, 'store']); // Handle POST to /admin
			Route::post('/login', [App\Http\Controllers\Auth\AdminAuthenticatedSessionController::class, 'store']);
		});

	//General (admin only)
		Route::middleware('auth:admin')->group(function () {
			Route::post('/logout', [App\Http\Controllers\Auth\AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
			Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('admin.dashboard');
			Route::get('/get_customer_detail', [App\Http\Controllers\Admin\AdminController::class, 'CustomerDetail'])->name('admin.get_customer_detail');
			Route::get('/my_profile', [App\Http\Controllers\Admin\AdminController::class, 'myProfile'])->name('admin.my_profile');
			Route::post('/my_profile', [App\Http\Controllers\Admin\AdminController::class, 'myProfile']);
			Route::get('/change_password', [App\Http\Controllers\Admin\AdminController::class, 'change_password'])->name('admin.change_password');
			Route::post('/change_password', [App\Http\Controllers\Admin\AdminController::class, 'change_password']);
			Route::get('/sessions', [App\Http\Controllers\Admin\AdminController::class, 'sessions'])->name('admin.sessions');
			Route::post('/sessions', [App\Http\Controllers\Admin\AdminController::class, 'sessions']);
        Route::post('/delete_action', [App\Http\Controllers\Admin\AdminController::class, 'deleteAction'])->name('admin.delete_action');
        Route::post('/declined_action', [App\Http\Controllers\Admin\AdminController::class, 'declinedAction']);
        Route::post('/approved_action', [App\Http\Controllers\Admin\AdminController::class, 'approvedAction']);
        Route::post('/process_action', [App\Http\Controllers\Admin\AdminController::class, 'processAction']);
        Route::post('/archive_action', [App\Http\Controllers\Admin\AdminController::class, 'archiveAction'])->name('admin.archive_action');
        Route::post('/move_action', [App\Http\Controllers\Admin\AdminController::class, 'moveAction']);

        //Blog
			Route::get('/blog', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('admin.blog.index');
			Route::get('/blog/create', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('admin.blog.create');
			Route::post('/blog/store', [App\Http\Controllers\Admin\BlogController::class, 'store'])->name('admin.blog.store');
			Route::get('/blog/edit/{id}', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('admin.blog.edit');
			Route::post('/blog/edit', [App\Http\Controllers\Admin\BlogController::class, 'edit']);

		    //Blog Category
			Route::get('/blogcategories', [App\Http\Controllers\Admin\BlogCategoryController::class, 'index'])->name('admin.blogcategory.index');
			Route::get('/blogcategories/create', [App\Http\Controllers\Admin\BlogCategoryController::class, 'create'])->name('admin.blogcategory.create');
			Route::post('/blogcategories/store', [App\Http\Controllers\Admin\BlogCategoryController::class, 'store'])->name('admin.blogcategory.store');
			Route::get('/blogcategories/edit/{id}', [App\Http\Controllers\Admin\BlogCategoryController::class, 'edit'])->name('admin.blogcategory.edit');
			Route::post('/blogcategories/edit', [App\Http\Controllers\Admin\BlogCategoryController::class, 'edit']);

			//CMS Pages
			Route::get('/cms_pages', [App\Http\Controllers\Admin\CmsPageController::class, 'index'])->name('admin.cms_pages.index');
			Route::get('/cms_pages/create', [App\Http\Controllers\Admin\CmsPageController::class, 'create'])->name('admin.cms_pages.create');
			Route::post('/cms_pages/store', [App\Http\Controllers\Admin\CmsPageController::class, 'store'])->name('admin.cms_pages.store');
			Route::get('/cms_pages/edit/{id}', [App\Http\Controllers\Admin\CmsPageController::class, 'editCmsPage'])->name('admin.edit_cms_page');
        Route::post('/cms_pages/edit', [App\Http\Controllers\Admin\CmsPageController::class, 'editCmsPage']);

        // Appointment Module
			Route::get('/appointments-others', [App\Http\Controllers\Admin\AdminController::class, 'appointmentsOthers'])->name('appointments-others');

			Route::resource('appointments', App\Http\Controllers\Admin\AppointmentsController::class);
			Route::get('/get-assigne-detail', [App\Http\Controllers\Admin\AppointmentsController::class, 'assignedetail']);
			Route::post('/update_appointment_status', [App\Http\Controllers\Admin\AppointmentsController::class, 'update_appointment_status']);
			Route::post('/update_appointment_priority', [App\Http\Controllers\Admin\AppointmentsController::class, 'update_appointment_priority']);
			Route::get('/change_assignee', [App\Http\Controllers\Admin\AppointmentsController::class, 'change_assignee']);
			Route::post('/update_apppointment_comment', [App\Http\Controllers\Admin\AppointmentsController::class, 'update_apppointment_comment']);
			Route::post('/update_apppointment_description', [App\Http\Controllers\Admin\AppointmentsController::class, 'update_apppointment_description']);

			// Booking Blocks module
			Route::prefix('booking-blocks')->name('admin.feature.bookingblocks.')->group(function () {
				Route::get('/', [App\Http\Controllers\Admin\BookingBlockController::class, 'index'])->name('index');
				Route::get('/create', [App\Http\Controllers\Admin\BookingBlockController::class, 'create'])->name('create');
				Route::post('/store', [App\Http\Controllers\Admin\BookingBlockController::class, 'store'])->name('store');
				Route::match(['get','post'], '/edit/{id?}', [App\Http\Controllers\Admin\BookingBlockController::class, 'edit'])->name('edit');
			});

        Route::post('/update_action', [\App\Http\Controllers\Admin\AdminController::class, 'updateAction'])->name('admin.update_action');

        // Recent Case
			Route::get('/recent_case', [App\Http\Controllers\Admin\RecentCaseController::class, 'index'])->name('admin.recent_case.index');
			Route::get('/recent_case/create', [App\Http\Controllers\Admin\RecentCaseController::class, 'create'])->name('admin.recent_case.create');
			Route::post('/recent_case/store', [App\Http\Controllers\Admin\RecentCaseController::class, 'store'])->name('admin.recent_case.store');
			Route::get('/recent_case/edit/{id}', [App\Http\Controllers\Admin\RecentCaseController::class, 'edit'])->name('admin.recent_case.edit');
			Route::post('/recent_case/edit', [App\Http\Controllers\Admin\RecentCaseController::class, 'edit']);

        Route::post('/delete_slot_action', [App\Http\Controllers\Admin\AdminController::class, 'deleteSlotAction']);

        // Contact Management
        Route::get('/contacts', [App\Http\Controllers\Admin\ContactController::class, 'index'])->name('admin.contacts.index');
        Route::get('/contacts/{id}', [App\Http\Controllers\Admin\ContactController::class, 'show'])->name('admin.contacts.show');
        Route::post('/contacts/{id}/send-to-bansal-email', [App\Http\Controllers\Admin\ContactController::class, 'sendToBansalEmail'])->name('admin.contacts.send-to-bansal-email');
        Route::post('/contacts/{id}/status', [App\Http\Controllers\Admin\ContactController::class, 'updateStatus'])->name('admin.contacts.update-status');
        Route::delete('/contacts/{id}', [App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('admin.contacts.destroy');
        Route::post('/contacts/bulk-delete', [App\Http\Controllers\Admin\ContactController::class, 'bulkDelete'])->name('admin.contacts.bulk-delete');
        Route::post('/contacts/bulk-send-to-bansal-email', [App\Http\Controllers\Admin\ContactController::class, 'bulkSendToBansalEmail'])->name('admin.contacts.bulk-send-to-bansal-email');
        Route::get('/contacts/export', [App\Http\Controllers\Admin\ContactController::class, 'export'])->name('admin.contacts.export');

        // Admin Users Management
        Route::get('/admin-users', [App\Http\Controllers\Admin\AdminController::class, 'adminUsers'])->name('admin.admin_users.index');
        Route::get('/admin-users/create', [App\Http\Controllers\Admin\AdminController::class, 'createAdminUser'])->name('admin.admin_users.create');
        Route::post('/admin-users/store', [App\Http\Controllers\Admin\AdminController::class, 'storeAdminUser'])->name('admin.admin_users.store');
        Route::get('/admin-users/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'editAdminUser'])->name('admin.admin_users.edit');
        Route::post('/admin-users/update/{id}', [App\Http\Controllers\Admin\AdminController::class, 'updateAdminUser'])->name('admin.admin_users.update');

		});

});


// Static informational pages — rate limited + ETag for repeat-visitor caching
Route::middleware(['throttle:web-pages', 'cache.headers:etag'])->group(function () {
    Route::get('/practice-areas', [\App\Http\Controllers\HomeController::class, 'practiceareas'])->name('practice-areas');
    Route::get('/case', [\App\Http\Controllers\HomeController::class, 'case'])->name('case');

    //Practice area main Page
    Route::get('/family-law', [\App\Http\Controllers\HomeController::class, 'familylawExperiment'])->name('family-law');
    Route::get('/migration-law', [\App\Http\Controllers\HomeController::class, 'migrationlawExperiment'])->name('migration-law');
    Route::get('/immigration-law', function () {
        return redirect()->route('migration-law', [], 301);
    });
    Route::get('/criminal-law', [\App\Http\Controllers\HomeController::class, 'criminallawExperiment'])->name('criminal-law');
    Route::get('/commercial-law', [\App\Http\Controllers\HomeController::class, 'commerciallawExperiment'])->name('commercial-law');
    Route::get('/property-law', [\App\Http\Controllers\HomeController::class, 'propertylawExperiment'])->name('property-law');

    /*********************Practice Area Inner Pages ***********************/
    Route::get('/divorce', [\App\Http\Controllers\HomeController::class, 'divorce'])->name('divorce');
    Route::get('/divorce-lawyers-melbourne', [\App\Http\Controllers\HomeController::class, 'divorceFamilyLawLanding'])->name('divorce-family-law-landing');
    Route::get('/landing', function () {
        return redirect('/divorce-lawyers-melbourne', 301);
    });
    Route::get('/child-custody', [\App\Http\Controllers\HomeController::class, 'childcustody'])->name('child-custody');
    Route::get('/family-violence', [\App\Http\Controllers\HomeController::class, 'familyviolence'])->name('family-violence');
    Route::get('/property-settlement', [\App\Http\Controllers\HomeController::class, 'propertysettlement'])->name('property-settlement');
    Route::get('/family-violence-orders', [\App\Http\Controllers\HomeController::class, 'familyviolenceorders'])->name('family-violence-orders');

    /*********************Migration Law ***********************/
    Route::get('/juridicational-error-federal-circuit-court-application', [\App\Http\Controllers\HomeController::class, 'juridicationalerrorfederalcircuitcourtapplication'])->name('juridicational-error-federal-circuit-court-application');
    Route::get('/art-application', [\App\Http\Controllers\HomeController::class, 'artapplication'])->name('art-application');
    Route::get('/visa-refusals-visa-cancellation', [\App\Http\Controllers\HomeController::class, 'visarefusalsvisacancellation'])->name('visa-refusals-visa-cancellation');
    Route::get('/federal-court-application', [\App\Http\Controllers\HomeController::class, 'federalcourtapplication'])->name('federal-court-application');

    /*********************Criminal Law ***********************/
    Route::get('/intervenition-orders', [\App\Http\Controllers\HomeController::class, 'intervenitionorders'])->name('intervenition-orders');
    Route::get('/trafic-offences', [\App\Http\Controllers\HomeController::class, 'traficoffences'])->name('trafic-offences');
    Route::get('/drink-driving-offences', [\App\Http\Controllers\HomeController::class, 'drinkdrivingoffences'])->name('drink-driving-offences');
    Route::get('/assualt-charges', [\App\Http\Controllers\HomeController::class, 'assualtcharges'])->name('assualt-charges');

    /*********************Commercial Law ***********************/
    Route::get('/business-law', [\App\Http\Controllers\HomeController::class, 'businesslaw'])->name('business-law');
    Route::get('/leasing-or-selling-a-business', [\App\Http\Controllers\HomeController::class, 'leasingorsellingabusiness'])->name('leasing-or-selling-a-business');
    Route::get('/contracts-or-business-agreements', [\App\Http\Controllers\HomeController::class, 'contractsorbusinessagreements'])->name('contracts-or-business-agreements');
    Route::get('/loan-agreement', [\App\Http\Controllers\HomeController::class, 'loanagreement'])->name('loan-agreement');

    /*********************Property Law ***********************/
    Route::get('/conveyancing', [\App\Http\Controllers\HomeController::class, 'conveyancing'])->name('conveyancing');
    Route::get('/building-and-construction-disputes', [\App\Http\Controllers\HomeController::class, 'buildingandconstructiondisputes'])->name('building-and-construction-disputes');
    Route::get('/caveats-disputs-and-removal', [\App\Http\Controllers\HomeController::class, 'caveatsdisputsandremoval'])->name('caveats-disputs-and-removal');
});

/*********************New Unified Blog and CMS Route ***********************/
// This handles CMS pages and recent cases at /{slug}
// IMPORTANT: This route must come after /blog routes to avoid conflicts
Route::get('/{slug}', [\App\Http\Controllers\HomeController::class, 'unifiedSlugHandler'])
	->middleware('throttle:web-pages')
	->where('slug', '^(?!admin\/|api\/|login$|register$|home$|invoice$|profile$|clear-cache$|js\/|css\/|images\/|img\/|assets\/|fonts\/|storage\/|blog$|blog\/).*$')
	->name('cms.slug');
