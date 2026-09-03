<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormsEmailController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Company\DeliveryController;
use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Company\DriverController;
use App\Http\Controllers\Company\SpecimenTypeController;
use App\Http\Controllers\Company\TemperatureRequirementController;
use App\Http\Controllers\Company\VehicleRequirementController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\Hospital\HospitalController;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Events\DriverLocationUpdated;
use App\Models\TemperatureRequirement;
use App\Models\DriverProfile;
use App\Events\DriverDisconnected;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\DriverCreated;


Route::get('/test-mail', function () {
    Mail::to('georgedriver@yopmail.com')->send(new DriverCreated(9));
});

// Landing page - Default homepage
Route::get('/', function () {
    return view('landing');
});
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-use', function () {
    return view('terms-of-use');
})->name('terms-of-use');

Route::get('/cookie-policy', function () {
    return view('cookie-policy');
})->name('cookie-policy');

// Login page
Route::get('/admin', function () {
    if (Auth::check()) {
        return redirect('/admin/dashboard');
    }
    return view('admin/login');
})->name('login');

//sign up
Route::get('/company/signup', function () {
    if (Auth::check()) {
        return redirect('/company/dashboard');
    }
    return view('company/signup');
})->name('company-signup');

Route::get('/company/login', function () {
    if (Auth::check()) {
        return redirect('/company/dashboard');
    }
    return view('company/company-login');
})->name('company-login');

Route::get('/hospital/login', function () {
    if (Auth::check()) {
        return redirect('/hospital/dashboard');
    }
    return view('hospital/hospital-login');
})->name('hospital-login');

Route::get('/test-driver-location', function () {
    Log::emergency('Location updated');
    $driver_id = 69;
    $company_id = 68;
    $lat = 30.7023120;
    $long = 76.6997280;
    // Redis::hset(
    //     'driver_locations',
    //     $driver_id,
    //     json_encode([
    //         'lat' => $lat,
    //         'lng' => $long,
    //         'updated_at' => now()->timestamp,
    //     ])
    // );
    $locations = Redis::hgetall('driver_locations');
    // dd($locations);

    // $driver = App\Models\Delivery::find(192); // Driver user ID

    // $lat = 30.7023120 + (rand(-1000, 1000) / 10000);
    // $lng = 76.6997280 + (rand(-1000, 1000) / 10000);

    // $driver->update([
    //     'pickup_latitude' => $lat,
    //     'pickup_longitude' => $lng,
    // ]);
    
    event(new DriverDisconnected([
        'driver_id' => $driver_id
    ]));
    // event(new DriverLocationUpdated([
    //     'company_id' => 68,
    //     'driver_id' => 76,
    //     'driver_name' => 'George',
    //     'lat' => '30.734215',
    //     'long' => '76.748689',
    // ]));

    return response()->json([
        'company_id' => 68,
        'driver_id' => $driver_id,
        'latitude' => $lat,
        'longitude' => $long,
        'message' => 'Location updated and event fired'
    ]);
});

Route::post('/driver-location-log', function (Request $request) {
    // Handle POST request for driver location logging
    Log::info('Driver location received', [
        'driver_id' => $request->driver_id,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
    ]);
});


// Email verification page
Route::get('/verify', function () {
    return view('common/verify');
});
Route::post('/admin', [AuthController::class, 'login'])->name('login');
Route::post('/company/signup', [CompanyAuthController::class, 'signup'])->name('signup');
Route::post('/company/login', [CompanyAuthController::class, 'login'])->name('company-login');
Route::post('/hospital/login', [HospitalController::class, 'login'])->name('hospital-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/verify-code', [AuthController::class, 'verifyCode']);
Route::post('/submit-verify', [AuthController::class, 'submitVerify'])->name('submit-verify');
Route::get('/set-password/{id}', [HomeController::class, 'setPassword'])->name('set-password');
Route::post('/submit-password/{id}', [HomeController::class, 'submitPassword'])->name('submit-password');



// Admin Dashboard Routes
// Route::get('/enquiries', [DashboardController::class, 'enquiries'])->name('dashboard.enquiries');
Route::prefix('admin/dashboard')->middleware(['admin.auth', 'can:view-enquiries','no.cache'])->group(function () {
    Route::get('/enquiries', [DashboardController::class, 'enquiries'])->name('dashboard.enquiries');
    Route::get('/enquiries/{id}', [DashboardController::class, 'enquiriesDetails'])->name('dashboard.enquiries-details');
    Route::get('/reject-enquiry/{id}', [DashboardController::class, 'rejectEnquiry'])->name('dashboard.reject-enquiry');
    //Generate credentials for waitlist submission
    Route::get('/waitlist/{id}/generate-credentials', [DashboardController::class, 'generateCredentials'])->name('dashboard.generate-credentials');
    // Route::get('/tenants', function () {
    //     return view('admin.tenants');
    // });
    Route::get('/tenants', [DashboardController::class, 'tenants'])->name('dashboard.tenants');
    Route::post('/tenants', [DashboardController::class, 'addTenant'])->name('dashboard.add-tenant');
    Route::get('/tenants/{id}', [DashboardController::class, 'tenantsDetails'])->name('dashboard.tenants-details');
    Route::post('/update-tenant/{id}', [DashboardController::class, 'updateTenant'])->name('dashboard.update-tenant');
    Route::get('/users', function () {
        return view('admin.users');
    });
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});


Route::prefix('hospital')->middleware('hospital.auth','no.cache')->group(function () {
    Route::get('/dashboard', [HospitalController::class, 'index'])->name('hospital-dashboard');
    Route::get('/profile/edit', function () {
        return view('profile.edit');
    });
    Route::get('/profile/change-password', function () {
        return view('profile.change-password');
    });
});
Route::prefix('company/dashboard')->middleware('custom.auth','no.cache')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('company-dashboard');
    
    Route::get('/create-job', [CompanyDashboardController::class, 'createJob'])->name('create-job');

    Route::get('/deliveries', function () {
        return view('company.deliveries');
    });

    Route::get('/deliveries/{id}', function ($id) {
        return view('company.delivery-details', ['id' => $id]);
    });

    Route::get('/edit-deliveries/{id}', [DeliveryController::class, 'index'])->name('edit-job');

    Route::get('/drivers', function () {
        return view('company.drivers');
    });

    Route::get('/drivers/create', function () {
        return view('company.driver-create');
    });
    Route::post('/register-driver', [DriverController::class, 'registerDriver'])->name('driver.register');
    Route::post('/update-driver', [DriverController::class, 'updateDriver'])->name('driver.update');

    Route::get('/drivers/{id}', function ($id) {
        return view('company.driver-details', ['id' => $id]);
    });

    Route::get('/drivers/{id}/edit', [DriverController::class, 'editDriver'])->name('driver.edit');
    // Route::get('/drivers/{id}/edit', function ($id) {
    //     return view('company.driver-edit', ['id' => $id]);
    // });
    
    Route::get('/activity-logs', function () {
        return view('company.activity-logs');
    });

    Route::get('/maps',[DashboardController::class, 'maps'])->name('company-maps');

    Route::get('/test-data', function () {
        return view('admin.test-data');
    });

    Route::get('/contacts', function () {
        return view('company.contacts');
    });

    Route::resource('specimen-types', SpecimenTypeController::class);
    Route::resource('temperature-requirement', TemperatureRequirementController::class);
    Route::resource('vehicle-requirement', VehicleRequirementController::class);
});

Route::prefix('admin')->middleware('admin.auth','no.cache')->group(function () {
    // Profile Routes
    Route::get('/profile/edit', function () {
        return view('profile.edit');
    });

    Route::get('/profile/change-password', function () {
        return view('profile.change-password');
    });
});

Route::prefix('company')->middleware('custom.auth','no.cache')->group(function () {
    // Profile Routes
    Route::get('/profile/edit', function () {
        return view('profile.edit');
    });

    Route::get('/profile/change-password', function () {
        return view('profile.change-password');
    });
});

// API Documentation homepage
Route::get('/api-docs', function () {
    return view('api-docs');
});

// Original Laravel welcome page (optional)
Route::get('/welcome', function () {
    return view('welcome');
});


Route::get('/test-url', function () {
    ///Auth::loginUsingId(1);
    // dd(auth()->user());
    return 'Mail sent';
});

//Stripe keys
// Route::get('/payment', [StripeController::class, 'index']);
// Route::post('/checkout', [StripeController::class, 'checkout']);
// Route::get('/success', [StripeController::class, 'success']);
// Route::get('/cancel', [StripeController::class, 'cancel']);