<?php

use App\Models\Workreport;
use Illuminate\Support\Facades\Request;
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

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/', [\App\Http\Controllers\AppController::class, 'home'])->name('home');
    Route::post('/', [\App\Http\Controllers\AppController::class, 'search'])->name('search');
    Route::get('arbeitsbericht/{customer}/{workreport?}', [\App\Http\Controllers\AppController::class, 'arbeitsbericht'])->name('arbeitsbericht');
    Route::get('customer/{customer?}', [\App\Http\Controllers\AppController::class, 'customer'])->name('customer');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class)->names('customers');
    Route::resource('materials', \App\Http\Controllers\MaterialController::class)->names('materials');
    Route::resource('servicecontracts', \App\Http\Controllers\ServicecontractController::class)->names('servicecontracts');
    Route::resource('workreports', \App\Http\Controllers\WorkreportController::class)->names('workreports');
    Route::post('workreports/savesignature', [\App\Http\Controllers\WorkreportController::class, 'savesignature'])->name('savesignature');
    Route::post('workreports/createpdf', [\App\Http\Controllers\WorkreportController::class, 'createpdf'])->name('createpdf');
    Route::post('workreports/unlock', [\App\Http\Controllers\WorkreportController::class, 'unlock'])->name('unlock');
    Route::post('workreports/delete', [\App\Http\Controllers\WorkreportController::class, 'delete'])->name('delete');

    Route::get('importcsv', [\App\Http\Controllers\ImportController::class, 'importcsv'])->name('importcsv');
    Route::post('importcsv', [\App\Http\Controllers\ImportController::class, 'runimportcsv'])->name('runimportcsv');

    Route::post('upload', [\App\Http\Controllers\WorkreportController::class, 'upload'])->name('upload');
    Route::post('deletefile', [\App\Http\Controllers\WorkreportController::class, 'deletefile'])->name('deletefile');
});



Route::get('import_from_live', [\App\Http\Controllers\ImportController::class, 'import'])->name('import_from_live');


Route::get('reset2fa', function() {
    #### ACHTUNG NUR AUSFÜHREN WENN USER NICHT MEHR REIN KOMMT
    // $user = \App\Models\User::where('email', '__kzuppa@pcs-muenchen.de__')->first();
    // $user->two_factor_secret = null;
    // $user->two_factor_recovery_codes = null;
    // $user->two_factor_confirmed_at = null;
    // $user->save();

    // return $user;
});




Route::get('pdf/{workreport}', function(Request $request, Workreport $workreport) {

    // $workreport = Workreport::find($request->get('arbeitsbericht_id'));
    $customer = $workreport->user->customer;

    return view('workreportpdf', compact(['workreport', 'customer']));
});






// Route::get('check', function(Request $request) {

//     $customers = \App\Models\Customer::all();

//     foreach ($customers as $customer) {
//         $workreports = $customer->workreports;
//         foreach($workreports as $workreport) {
//             if($customer->id != $workreport->user_id) {
//                 echo $customer->company . "<br>";
//                 echo $customer->id." - ".$workreport->id . " - ".$workreport->user_id."<br>";
//             }
//         }
//     }

// });




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/user/profile', function () {
        return view('profile.show');
    })->name('profile.show');
    
    // Two Factor Authentication management
    Route::get('/user/two-factor-authentication', function () {
        return view('profile.two-factor');
    })->name('two-factor.index');

    // Custom Two Factor Recovery Codes routes
    Route::get('/user/two-factor-recovery-codes', [\App\Http\Controllers\TwoFactorRecoveryCodesController::class, 'index'])
    ->name('two-factor.recovery-codes');
    Route::post('/user/two-factor-recovery-codes', [\App\Http\Controllers\TwoFactorRecoveryCodesController::class, 'store']);
});
