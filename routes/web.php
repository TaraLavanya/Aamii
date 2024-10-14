<?php


use App\Http\Controllers\Auth\LoginController;
use App\Http\Livewire\CategorySummary;
use App\Http\Livewire\ChildCategorySummary;
use App\Http\Livewire\CitySummary;
use App\Http\Livewire\CountrySummary;
use App\Http\Livewire\Dashboard\Admin;
use App\Http\Livewire\Profile\UserProfileSettings;
use App\Http\Livewire\Settings\Employee\EmployeeSummary;
use App\Http\Livewire\StateSummary;
use App\Http\Livewire\SubCategorySummary;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/', function () {
    return view('auth.admin-login');
})->name('admin-login-form');

Route::post('/admin-login', [LoginController::class, 'authenticateAdmin'])->name('admin.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware([
    'web',
])->group(function () {

    Route::get('/country', CountrySummary::class)->name('country');
    Route::get('/state', StateSummary::class)->name('state');
    Route::get('/city', CitySummary::class)->name('city');
    Route::get('/dashboard', Admin::class)->name('dashboard');
    Route::get('/settings/user-profile', UserProfileSettings::class)->name('user.profile');
    Route::get('/settings/employees', EmployeeSummary::class)->name('employees.index');
    Route::get('/category', CategorySummary::class)->name('category');
    Route::get('/sub-category', SubCategorySummary::class)->name('sub-category');
    Route::get('/child-category', ChildCategorySummary::class)->name('child-category');
});

Route::get('cls', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cache is cleared";
});

Route::get('symlink', function () {
    Artisan::call('storage:link');
    return "Sym link created";
});

Route::get('migrate-tables', function () {
    Artisan::call('migrate', ['--force' => true]);
    return "Tables migrated";
});

Route::get('/set-default-password', function () {
    User::where('id', '>', 0)->update([
        'password' => Hash::make(config('app.default_user_password')),
    ]);
    echo "Done, set default password for all users";
});
