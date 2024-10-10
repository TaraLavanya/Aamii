<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExhibitorEventDashboardController;
use App\Http\Controllers\Api\ExhibitorProduct;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\PreviousEventController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VisitorAppointmentController;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\VisitorWhatsappController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::post('/login', [AuthController::class, 'login']);
Route::post('/otp-request', [AuthController::class, 'otpRequest']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::name('api.')
    ->middleware(['auth:sanctum', 'throttle:100,1'])
    ->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::post('/events/{event_id}/exhibitor/registration', [DashboardController::class, 'store']);
        Route::get('/exhibitor/profile', [ProfileController::class, 'show']);
        Route::post('/exhibitor/profile/edit', [ProfileController::class, 'updateProfile']);
        Route::post('/events/{event_id}/products', [ProfileController::class, 'updateEventProducts']);
        Route::post('/exhibitor/products/{product_id}', [ProfileController::class, 'storeProductImage']);
        Route::post('/exhibitor/products/{product_id}/images/{image_id}', [ProfileController::class, 'destroyProductImage']);
        Route::get('/events/{eventId}/appointments', [AppointmentController::class, 'showAppointments']);
        Route::post('/events/{eventId}/appointments/{appointmentId}', [AppointmentController::class, 'statusUpdate']);
        Route::get('/previous-events', [PreviousEventController::class, 'showPreviousEvents']);
        Route::get('/previous-events/{eventId}', [PreviousEventController::class, 'getPreviousEventCompletedAppointments']);
        Route::get('/products/{search}', [MasterController::class, 'showProducts']);
        Route::post('/products/{product_name}', [MasterController::class, 'addProducts']);
        Route::get('/exhibitors/{eventId}/dashboard', [ExhibitorEventDashboardController::class, 'getEventDashboardData']);
        Route::post('/exhibitor/logo', [ProfileController::class, 'updateExhibitorLogo']);
        Route::get('/event-dates', [AppointmentController::class, 'getEventDates']);
        Route::get('/events/{eventId}/appointments/{appointmentId}/ics-file', [AppointmentController::class, 'getICSFile']);
        Route::get('/announcements', [AnnouncementController::class, 'getAnnouncements']);
    });
//admin
Route::prefix('/admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/visitors', [AdminController::class, 'getVisitors']);
    Route::get('/exhibitors', [AdminController::class, 'getExhibitors']);
    Route::post('/update-stall-detail', [AdminController::class, 'updateStallDetail']);
    Route::get('/dashboard', [AdminController::class, 'getAdminDashboardData']);
});

// Integrating the whatsapp flow for the visitors
Route::prefix('/whatsapp')->group(function () {

    Route::get('/visitors/{mobileNumber}/appointments', [VisitorWhatsappController::class, 'getAppointmentsByMobilenumber']);
    Route::post('/visitors/appointments/{appointmentId}/cancel', [VisitorWhatsappController::class, 'cancelAppointment']);
    Route::post('/visitors/appointments/{appointmentId}/reschedule', [VisitorWhatsappController::class, 'rescheduleAppointment']);
    Route::get('/search-exhibitors/{search}', [VisitorWhatsappController::class, 'searchExhibitors']);
    Route::get('/search-products/{search}', [VisitorWhatsappController::class, 'searchProducts']);
    Route::get('/get-exhibitors-by-product', [VisitorWhatsappController::class, 'getExhibitorsByProduct']);
    Route::post('/make-appointment', [VisitorWhatsappController::class, 'makeAppointment']);

    Route::get('/exhibitor-products/{eventId}/{search}', [ExhibitorProduct::class, 'getExhibitorProduct']);
    Route::post('/visitors/make-appointment', [VisitorAppointmentController::class, 'makeAppointment']);
    Route::get('/search-products/{eventId}/{search}', [VisitorWhatsappController::class, 'searchProducts']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/visitors', [VisitorController::class, 'store']);
Route::post('/update-visitor-check-in-status', [VisitorController::class, 'updateVisitorCheckedInStatus']);
