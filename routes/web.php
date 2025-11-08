<?php

use Illuminate\Support\Facades\Route;
use Spatie\PdfToImage\Pdf;
use App\Models\User;
use App\Modules\Subscription\Notifications\SubscriptionNotification;
use Illuminate\Support\Facades\Notification;
use App\Modules\Subscription\Events\SubscriptionEvent;
use App\Modules\Resource\Events\ResourceEvent;
use SEO\Models\Page;

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

Route::get('/welcome', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/viewer1', function () {
    return view('resource::viewer.vue-pdf-app');
});

Route::get('/viewer2', function () {
    return view('resource::viewer.flipbook-swipe');
});

Route::get('/logout', function () {
    // Logout user
    auth()->logout();
    // Redirect to homepage
    return redirect('/');
});

Route::get('/create-resource-covers', function () {
    $resources = App\Modules\Resource\Models\Resource::all();
    
    foreach ($resources as $resource) {
        if ($resource->title) {
            $path = Storage::disk('local_rcovers')->url('');
            $img = Image::make(public_path('storage/resource-covers/templateq.jpg'));
            $img->text($resource->title, 12, 10, function ($font) {
                // Example: customize font settings here if needed
                // $font->file(public_path('path/font.ttf'));
                // $font->size(12);
                // $font->color('#4285F4');
            });
            $img->save(public_path('storage/resource-covers/text_with_image.jpg'));
        }
    }
});

Route::get('/routes', function () {
    $routes = Route::getRoutes();
    foreach ($routes as $route) {
        if ($route->getName() != '' && isset($route->middleware()[0]) && $route->middleware()[0] == 'web') {
            dd($route->action['prefix']);
        }
    }
});

Route::get('/generate-pages', function () {
    $resources = App\Modules\Resource\Models\Resource::all();

    foreach ($resources as $resource) {
        if ($resource->is_published) {
            $page = Page::firstOrNew(['title' => $resource->title]);
            $page->path = route('resources.show', $resource->slug);
            $page->title = mb_strimwidth($resource->title, 0, 50);
            $page->description_source = $resource->description;
            $page->description = mb_strimwidth($resource->description, 0, 200);
            $page->created_at = now();
            $page->updated_at = now();
            $page->change_frequency = 'monthly';
            $page->priority = 0.5;
            $page->save();
        }
    }
});

Route::get('/notify-success', function () {
    notify()->success('Success');
});

Route::get('/send-notification', function () {
    $resource = App\Modules\Resource\Models\Resource::first();
    event(new ResourceEvent($resource, 'published'));
});

// =======================
// Admin Credit System Routes
// =======================
Route::get('/admin/credit', [App\Http\Controllers\AdminCreditController::class, 'index'])->name('admin.credit.index');
Route::post('/admin/credit/process', [App\Http\Controllers\AdminCreditController::class, 'processCredit'])->name('admin.credit.process');
Route::post('/admin/credit/quick', [App\Http\Controllers\AdminCreditController::class, 'quickCredit'])->name('admin.credit.quick');

// =======================
// Paystack Packages Routes (Secure + No Duplicates)
// =======================

use App\Http\Controllers\PaystackPackagesController;

// 🔒 Protected routes — only for logged-in users
Route::middleware(['auth'])->group(function () {
    Route::get('/packages', [PaystackPackagesController::class, 'index'])->name('packages.index');
    Route::post('/paystack/initialize', [PaystackPackagesController::class, 'initializePayment'])->name('paystack.initialize');
    Route::get('/packages/success', [PaystackPackagesController::class, 'success'])->name('packages.success');
});

// 🌍 Public routes — Paystack needs these open
Route::get('/paystack/callback', [PaystackPackagesController::class, 'paymentCallback'])->name('paystack.callback');
Route::post('/paystack/webhook', [PaystackPackagesController::class, 'webhook'])->name('paystack.webhook');

Route::post('/upload/resource-cover', [App\Http\Controllers\ImageUploadController::class, 'uploadResourceCover'])->name('upload.resource-cover');
Route::post('/upload/blog-image', [App\Http\Controllers\ImageUploadController::class, 'uploadBlogImage'])->name('upload.blog-image');
Route::post('/upload/avatar/{user?}', [App\Http\Controllers\ImageUploadController::class, 'uploadUserAvatar'])->name('upload.avatar');
