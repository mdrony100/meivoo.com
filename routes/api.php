<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FeedPostController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\FamilyTreeController;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return ApiResponse::success('Authenticated user retrieved successfully.', $request->user());
});

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->middleware('throttle:refresh-token');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:forgot-password');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:verify-otp');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:resend-otp');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Messages
    Route::get('/messages', [MessageController::class, 'messages']);
    Route::get('/messages/poll', [MessageController::class, 'pollMessages']);
    Route::post('/messages', [MessageController::class, 'storeMessage']);

    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/recent', [EventController::class, 'recent']);
    Route::post('/event/create', [EventController::class, 'store']);
    Route::get('/event/{event}/view', [EventController::class, 'show']);
    Route::post('/event/update/{event}', [EventController::class, 'update']);
    Route::delete('/event/delete/{event}', [EventController::class, 'destroy']);
    Route::post('/event/{event}/rsvp', [EventController::class, 'rsvp']);
    Route::get('/event/{event}/attendances', [EventController::class, 'getAttendances']);

    // Feed post
    Route::get('/feed', [FeedPostController::class, 'feed']);
    Route::post('/feed/posts', [FeedPostController::class, 'storeFeedPost']);
    Route::delete('/feed/{post}', [FeedPostController::class, 'destroyFeedPost']);
    Route::post('/feed/{post}/like-toggle', [FeedPostController::class, 'toggleFeedLike']);
    Route::post('/feed/{post}/share', [FeedPostController::class, 'shareFeedPost']);
    Route::post('/feed/{post}/comments', [FeedPostController::class, 'storeFeedComment']);
    Route::post('/feed/{post}/comments/{comment}/reply', [FeedPostController::class, 'storeFeedReply']);

    // Member
    Route::get('/family-members', [FamilyTreeController::class, 'members']);
    // Route::get('/member/create', [FamilyTreeController::class, 'memberCreate']);
    Route::post('/add-member', [FamilyTreeController::class, 'storeMember']);
    Route::get('/members/{member}/edit', [FamilyTreeController::class, 'memberEdit']);
    Route::post('/members/{member}', [FamilyTreeController::class, 'updateMember']);
    Route::get('/view/member/{id}', [FamilyTreeController::class, 'viewMember']);
    Route::get('/memories', [FeedPostController::class, 'memories']);
    Route::get('/voice-memories', [FeedPostController::class, 'voiceMemories']);
    Route::get('/photos', [DashboardController::class, 'myPostImages']);
    Route::get('/family-tree/{member?}', [FamilyTreeController::class, 'familyTree']);
    Route::get('/family-member/{id}/info', [FamilyTreeController::class, 'getMemberInfo']);

});
