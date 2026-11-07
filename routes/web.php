<?php

use App\Http\Controllers\AiPhotoController;
use App\Http\Controllers\Frontend\GoogleAuthController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LegacyNameController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\EventController;
use App\Http\Controllers\User\FamilyTreeController;
use App\Http\Controllers\User\FeedPostController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\SearchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Colors\Profile;

Route::get('/', [FrontendController::class, 'home'])->name('frontend.home');
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('frontend.privacy-policy');
Route::get('/terms', [FrontendController::class, 'termsCondition'])->name('frontend.terms');
Route::get('/contact', [FrontendController::class, 'contact'])->name('frontend.contact');
Route::post('/contact/store', [FrontendController::class, 'contactStore'])->name('contact.store');
Route::post('/newsletter-subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Auth::routes();

Route::prefix('user')->name('user.')->middleware('auth')->group(function () {

    // Accessible without completed profile
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password/update', [DashboardController::class, 'updatePassword'])->name('password.update');

    // Require completed profile
    Route::middleware('profile.completed')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        // topbar: quick search + counters
        Route::get('/search', [SearchController::class, 'search'])->name('search');
        Route::get('/summary', [SearchController::class, 'summary'])->name('summary');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

        // feed post

        Route::post('/feed/posts', [FeedPostController::class, 'storeFeedPost'])->name('feed.posts.store');
        Route::delete('/feed/{post}', [FeedPostController::class, 'destroyFeedPost'])->name('feed.posts.destroy');
        Route::post('/feed/{post}/like-toggle', [FeedPostController::class, 'toggleFeedLike'])->name('feed.likes.toggle');
        Route::post('/feed/{post}/comments', [FeedPostController::class, 'storeFeedComment'])->name('feed.comments.store');
        Route::post('/feed/{post}/comments/{comment}/reply', [FeedPostController::class, 'storeFeedReply'])->name('feed.comments.reply');

        Route::post('/feed/posts/{id}/share', [FeedPostController::class, 'shareToMyFeed'])->name('posts.share');

        // messages
        Route::get('/messages', [MessageController::class, 'messages'])->name('messages');
        Route::get('/messages/poll', [MessageController::class, 'pollMessages'])->name('messages.poll');
        Route::post('/messages', [MessageController::class, 'storeMessage'])->name('messages.store');

        // events
        Route::get('/events', [EventController::class, 'events'])->name('events');
        Route::get('/events/create', [EventController::class, 'createEvent'])->name('events.create');
        Route::post('/events', [EventController::class, 'storeEvent'])->name('events.store');
        Route::get('/events/{event}/show', [EventController::class, 'showEvent'])->name('events.show');
        Route::get('/events/{event}/edit', [EventController::class, 'editEvent'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'updateEvent'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'deleteEvent'])->name('events.destroy');
        Route::post('/events/{event}/rsvp', [EventController::class, 'rsvp'])->name('events.rsvp');

        // legacy names
        Route::get('/legacy-names', [LegacyNameController::class, 'index'])->name('legacy-names');
        Route::get('/legacy-names/create', [LegacyNameController::class, 'create'])->name('legacy-names.create');
        Route::post('/legacy-names', [LegacyNameController::class, 'store'])->name('legacy-names.store');
        Route::get('/legacy-names/{legacyName}', [LegacyNameController::class, 'show'])->name('legacy-names.show');
        Route::get('/legacy-names/{legacyName}/edit', [LegacyNameController::class, 'edit'])->name('legacy-names.edit');
        Route::put('/legacy-names/{legacyName}', [LegacyNameController::class, 'update'])->name('legacy-names.update');
        Route::delete('/legacy-names/{legacyName}', [LegacyNameController::class, 'destroy'])->name('legacy-names.destroy');

        Route::prefix('ai-photo')->name('ai-photo.')->group(function () {
            Route::get('/', [AiPhotoController::class, 'index'])->name('index');
            // Route::post('/process', [AiPhotoController::class, 'testUploadSession'])->name('process');
            Route::post('/process', [AiPhotoController::class, 'process'])->name('process');
            Route::get('/download/{id}', [AiPhotoController::class, 'download'])->name('download');
        });

        // family tree
        Route::get('/family-tree/{member?}', [FamilyTreeController::class, 'familyTree'])->name('family-tree');
        Route::get('/search-member', [FamilyTreeController::class, 'searchMember'])->name('search-member');
        Route::get('/members', [FamilyTreeController::class, 'members'])->name('members');
        Route::get('/family-member/{id}/info', [FamilyTreeController::class, 'getMemberInfo'])->name('family-member.info');
        Route::post('/store-member', [FamilyTreeController::class, 'storeMember'])->name('member.store');
        Route::get('/members/{member}/edit', [FamilyTreeController::class, 'memberEdit'])->name('member.edit');
        Route::put('/members/{member}', [FamilyTreeController::class, 'updateMember'])->name('member.update');
        Route::post('/members/{member}/relations', [FamilyTreeController::class, 'storeRelation'])->name('member.relation.store');
        Route::put('/member-relations/{relationship}', [FamilyTreeController::class, 'updateRelation'])->name('member.relation.update');
        Route::delete('/member-relations/{relationship}', [FamilyTreeController::class, 'destroyRelation'])->name('member.relation.destroy');
        Route::delete('/member-gallery/{gallery}', [FamilyTreeController::class, 'destroyGalleryImage'])->name('member.gallery.destroy');
        Route::post('/store-family', [FamilyTreeController::class, 'storeFamily'])->name('storeFamily');
        Route::get('/member/create', [FamilyTreeController::class, 'memberCreate'])->name('member.create');
        Route::get('/member/{id?}/profile', [DashboardController::class, 'memberFullProfile'])->name('member.profile');
    });
});
Route::get('user/feed', [FeedPostController::class, 'feed'])->name('user.feed');
Route::get('user/feed/posts/{id}/show', [FeedPostController::class, 'showPost'])->name('user.posts.show');
