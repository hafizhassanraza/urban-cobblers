<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\OrderTrackingController;
use App\Http\Controllers\Shop\PageController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}/quick-view', [ProductController::class, 'quickView'])->name('shop.quick-view');
Route::get('/shop/{slug}', [ProductController::class, 'show'])->name('shop.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/size-guide', [PageController::class, 'sizeGuide'])->name('size-guide');
Route::get('/journal', [PageController::class, 'journal'])->name('journal');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('track-order.index');
Route::post('/track-order', [OrderTrackingController::class, 'lookup'])->name('track-order.lookup');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/login', [CheckoutController::class, 'login'])->name('checkout.login');
Route::post('/checkout/register', [CheckoutController::class, 'register'])->name('checkout.register');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/shop/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::post('products/bulk', [AdminProductController::class, 'bulkUpdate'])->name('products.bulk');

    Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/export', [AdminExportController::class, 'orders'])->name('orders.export');
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/orders/pdf', [AdminExportController::class, 'ordersPdf'])->name('reports.export.orders.pdf');
    Route::get('reports/export/sales/pdf', [AdminExportController::class, 'salesPdf'])->name('reports.export.sales.pdf');
    Route::get('reports/export/products/pdf', [AdminExportController::class, 'productsPdf'])->name('reports.export.products.pdf');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::get('customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/approval', [AdminReviewController::class, 'updateApproval'])->name('reviews.update-approval');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('contact', [AdminContactMessageController::class, 'index'])->name('contact.index');
    Route::get('contact/{message}', [AdminContactMessageController::class, 'show'])->name('contact.show');
    Route::patch('contact/{message}/read', [AdminContactMessageController::class, 'markRead'])->name('contact.read');
    Route::delete('contact/{message}', [AdminContactMessageController::class, 'destroy'])->name('contact.destroy');

    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
