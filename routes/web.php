<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CustomizeTowerBouquetController;
use App\Http\Controllers\DecorationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\MysteryBoxController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SnackController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// ADMIN
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Snack Export/Import
    Route::get('/snack/export', [SnackController::class, 'export'])->name('snack.export');
    Route::post('/snack/import', [SnackController::class, 'import'])->name('snack.import');
    // Snack Recycle Bin
    Route::get('/snack/trash', [SnackController::class, 'trash'])->name('snack.trash');

    // Snack Restore soft delete
    Route::put('/snack/{id}/restore', [SnackController::class, 'restore'])->name('snack.restore');

    Route::put('/snack/restore-all', [SnackController::class, 'restoreAll'])->name('snack.restore-all');

    // Snack Force delete
    Route::delete('/snack/{id}/force-delete', [SnackController::class, 'forceDelete'])->name('snack.force-delete');

    // Decoration Export/Import
    Route::get('/decoration/export', [DecorationController::class, 'export'])->name('decoration.export');
    Route::post('/decoration/import', [DecorationController::class, 'import'])->name('decoration.import');

    // Decoration Recycle Bin
    Route::get('/decoration/trash', [DecorationController::class, 'trash'])->name('decoration.trash');

    // Decoration Restore soft delete
    Route::put('/decoration/{id}/restore', [DecorationController::class, 'restore'])->name('decoration.restore');

    Route::put('/decoration/restore-all', [DecorationController::class, 'restoreAll'])->name('decoration.restore-all');

    // Decoration Force delete
    Route::delete('/decoration/{id}/force-delete', [DecorationController::class, 'forceDelete'])->name('decoration.force-delete');

    // Collection Export/Import
    Route::get('/collection/export', [CollectionController::class, 'export'])->name('collection.export');
    Route::post('/collection/import', [CollectionController::class, 'import'])->name('collection.import');

    // Collection Recycle Bin
    Route::get('/collection/trash', [CollectionController::class, 'trash'])->name('collection.trash');

    // Collection Restore soft delete
    Route::put('/collection/{id}/restore', [CollectionController::class, 'restore'])->name('collection.restore');
    Route::put('/collection/restore-all', [CollectionController::class, 'restoreAll'])->name('collection.restore-all');

    // Collection Force delete
    Route::delete('/collection/{id}/force-delete', [CollectionController::class, 'forceDelete'])->name('collection.force-delete');

    // Order Export/Import
    Route::get('/order/export', [OrderController::class, 'export'])->name('order.export');

    // Order untuk update status
    Route::post('/orders/{id}/ship', [OrderController::class, 'ship'])->name('order.ship');



    // User Export/Import
    Route::get('/user/export', [UserController::class, 'export'])->name('user.export');

    Route::post('/users/{id}/block', [UserController::class, 'block'])->name('user.block');

    Route::post('/users/{id}/toggle-block', [UserController::class, 'toggleBlock'])->name('user.block');

    Route::get('/admin/users/{userId}/logs', [ActivityLogController::class, 'showUserLogs'])->name('admin.users.logs');


    // Resource routes
    Route::resource('snack', SnackController::class);
    Route::resource('decoration', DecorationController::class);
    Route::resource('collection', CollectionController::class);
    Route::resource('order', OrderController::class);
    Route::resource('user', UserController::class);
    Route::resource('customize-tower-bouquet', CustomizeTowerBouquetController::class);

});

Auth::routes(['verify' => true]);

Route::get('/auth-google-redirect', [RegisterController::class, 'google_redirect'])->name('google-redirect');
Route::get('/auth-google-callback', [RegisterController::class, 'google_callback'])->name('google-callback');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Add this to your web.php routes
    Route::get('/profile/{id}/{slug}', [UserController::class, 'show'])->name('profile');
    // Rute untuk memperbarui profil (PUT)
    Route::put('/profile/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update'); // Gunakan 'user.update' sesuai form action Anda

    // Mystery Box
    Route::get('/mysterybox', [MysteryBoxController::class, 'index'])->name('mysterybox');
    Route::post('/set-budget', [MysteryBoxController::class, 'setBudget'])->name('set-budget');
    Route::post('/set-mood', [MysteryBoxController::class, 'setMood'])->name('set-mood');
    Route::post('/reset-session', [MysteryBoxController::class, 'reset'])->name('reset-session');

    Route::resource('user', UserController::class);
    Route::resource('address', AddressController::class);
    Route::post('/addresses/{address}/toggle-primary', [AddressController::class, 'togglePrimary'])
     ->name('addresses.togglePrimary');
    Route::get('/customize-tower-bouquet', [CustomizeTowerBouquetController::class, 'index'])->name('customer-tower-bouquet.index');
    Route::get('/customize-tower-bouquet/tower', [CustomizeTowerBouquetController::class, 'create_tower'])->name('customize-tower-bouquet.tower');
    Route::get('/customize-tower-bouquet/bouquet', [CustomizeTowerBouquetController::class, 'create_bouquet'])->name('customize-tower-bouquet.bouquet');
    Route::post('/customize-tower-bouquet/{type}/store', [CustomizeTowerBouquetController::class, 'store'])->name('customer-tower-bouquet.store');

    Route::resource('collections', CollectionController::class);
    Route::post('/collection/{id_collection}/add-to-cart/{quantity}', [CollectionController::class, 'add_to_cart'])->name('collection.to.cart');

    Route::get('/{id_user}/{slug}/xart', [CartController::class, 'index'])->name('cart.index');
    Route::delete('/{id_user}/{slug}/cart/{count_items}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/{id_user}/{slug}/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('{id_user}/{slug}/cart/add-address', [CartController::class, 'store_address'])->name('cart.new.address');
    Route::put('{id_user}/{slug}/cart/set-primary-address', [CartController::class, 'set_primary_address'])->name('cart.primary.address');
    Route::put('{id_user}/{slug}/cart/update-quantity-item', [CartController::class, 'update_quantity_item'])->name('cart.update.quantity');
    Route::post('collections/search', [CollectionController::class, 'search'])->name('collection.search');

    // baru dibuat ni bang -jason
    Route::get('/checkout/{order_id}/payment', [CheckoutController::class, 'index'])->name('checkout.index');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');

    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout');
});
