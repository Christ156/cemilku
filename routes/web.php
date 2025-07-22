<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CustomizeTowerBouquetController;
use App\Http\Controllers\DecorationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SnackController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MysteryBoxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ADMIN
Route::prefix('admin')->name('admin')->middleware(['auth'])->group(function () {
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

    // Resource routes
    Route::resource('snack', SnackController::class);
    Route::resource('decoration', DecorationController::class);
    Route::resource('collection', CollectionController::class);
    Route::resource('order', OrderController::class);
    Route::resource('user', UserController::class);
    Route::resource('customize-tower-bouquet', CustomizeTowerBouquetController::class);

    Route::get('/mysterybox', [MysteryBoxController::class, 'index'])->name('mysterybox');
    Route::post('/set-budget', [MysteryBoxController::class, 'setBudget'])->name('set-budget');
    Route::post('/set-mood', [MysteryBoxController::class, 'setMood'])->name('set-mood');
    Route::post('/reset-session', [MysteryBoxController::class, 'reset'])->name('reset-session');
});

Auth::routes(['verify' => true]);

Route::get('/auth-google-redirect', [RegisterController::class, 'google_redirect'])->name('google-redirect');
Route::get('/auth-google-callback', [RegisterController::class, 'google_callback'])->name('google-callback');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('s', [UserController::class, 'show'])->name('profile');




    Route::get('/cart', function () {
        return view('cart');
    })->name('cart');
    // START MODIFIKASI: Rute API Alamat
    // Grup ini TIDAK perlu middleware 'auth' karena sudah di dalam grup middleware 'auth' di atasnya.
    // Namun, validasi Auth::id() di controller tetap PENTING!
    Route::group([], function () {
        // PENTING: Hapus atau komentari baris ini! Ini adalah rute lama yang tidak memfilter berdasarkan user ID.
        // Route::get('/api/addresses', [AddressController::class, 'getAddressesApi'])->name('api.addresses.index');

        // BARU DITAMBAHKAN/DIKONFIRMASI: Ini adalah rute yang benar untuk mengambil alamat berdasarkan user ID
        Route::get('/api/users/{user}/addresses', [AddressController::class, 'getAddressesApi'])->name('api.users.addresses.index');

        // POST: Menyimpan alamat baru untuk user tertentu (user_id akan diambil dari {user})
        Route::post('/api/users/{user}/addresses', [AddressController::class, 'store'])->name('api.users.addresses.store');

        // PUT: Memperbarui alamat yang sudah ada untuk user tertentu
        Route::put('/api/users/{user}/addresses/{address}', [AddressController::class, 'update'])->name('api.users.addresses.update');

        // DELETE: Menghapus alamat untuk user tertentu
        Route::delete('/api/users/{user}/addresses/{address}', [AddressController::class, 'destroy'])->name('api.users.addresses.destroy');

        // PUT: Mengatur alamat sebagai alamat utama (primary) untuk user tertentu
        Route::put('/api/users/{user}/addresses/{address}/set-primary', [AddressController::class, 'setPrimary'])->name('api.users.addresses.setPrimary');
    });
    // END MODIFIKASI



    // Route::get('/mysterybox', function () {
    //     $mode = 'Budget';
    //     return view('mystery_box.create', compact('mode'));
    // })->name('mystery-box');

    // Route::match(['get', 'post'], '/mysterybox', function (Request  $request) {
    //     if ($request->isMethod('post')) {
    //         // Jika ada POST ke /mysterybox, langsung redirect ke GET /mysterybox
    //         return redirect()->route('mysterybox');
    //     }
    //     $mode = session('mode', 'Budget');
    //     $budget = session('budget');
    //     $mood = session('mood');
    //     return view('mystery_box.create', compact('mode', 'budget', 'mood'));
    // })->name('mysterybox');

    // Route::post('/set-budget', function (Request $request) {
    //     $request->validate(['budget' => 'required']);
    //     session(['budget' => $request->budget, 'mode' => 'Mood']);
    //     return redirect()->route('mysterybox');
    // })->name('set-budget');

    // Route::post('/set-mood', function (Request $request) {
    //     $request->validate(['mood' => 'required']);
    //     session(['mood' => $request->mood, 'mode' => 'Done']);
    //     return redirect()->route('mysterybox');
    // })->name('set-mood');

//     Route::get('/mysterybox', function () {
//         $mode = 'Budget';
//         return view('mystery_box.create', compact('mode'));
//     })->name('mystery-box');

//     Route::match(['get', 'post'], '/mysterybox', function (Request $request) {
//         if ($request->isMethod('post')) {
//             // Jika ada POST ke /mysterybox, langsung redirect ke GET /mysterybox
//             return redirect()->route('mysterybox');
//         }
//         $mode   = session('mode', 'Budget');
//         $budget = session('budget');
//         $mood   = session('mood');
//         return view('mystery_box.create', compact('mode', 'budget', 'mood'));
//     })->name('mysterybox');

//     Route::post('/set-budget', function (Request $request) {
//         $request->validate(['budget' => 'required']);
//         session(['budget' => $request->budget, 'mode' => 'Mood']);
//         return redirect()->route('mysterybox');
//     })->name('set-budget');

//     Route::post('/set-mood', function (Request $request) {
//         $request->validate(['mood' => 'required']);
//         session(['mood' => $request->mood, 'mode' => 'Done']);
//         return redirect()->route('mysterybox');
//     })->name('set-mood');

//     Route::post('/reset-session', function () {
//         session()->forget(['budget', 'mood', 'mode']);
//         return response()->json(['status' => 'reset']);
//     })->name('reset-session');

    Route::resource('user', UserController::class);
    Route::resource('address', AddressController::class);
    Route::get('/customize-tower-bouquet', [CustomizeTowerBouquetController::class, 'index'])->name('customer-tower-bouquet.index');
    Route::get('/customize-tower-bouquet/tower', [CustomizeTowerBouquetController::class, 'create_tower'])->name('customize-tower-bouquet.tower');
    Route::get('/customize-tower-bouquet/bouquet', [CustomizeTowerBouquetController::class, 'create_bouquet'])->name('customize-tower-bouquet.bouquet');
    Route::post('/customize-tower-bouquet/{type}/store', [CustomizeTowerBouquetController::class, 'store'])->name('customer-tower-bouquet.store');

    Route::resource('collections', CollectionController::class);

    // baru dibuat ni bang -jason
    Route::get('/checkout', function () {
        return view('checkout');
    })->name('checkout.index');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');

    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout');
});

// Route::get('/mysterybox', function () {
//         $mode = 'Budget';
//         return view('mystery_box.create', compact('mode'));
//     })->name('mystery-box');

// Route::match(['get', 'post'], '/mysterybox', function (Request  $request) {
//         if ($request->isMethod('post')) {
//             // Jika ada POST ke /mysterybox, langsung redirect ke GET /mysterybox
//             return redirect()->route('mysterybox');
//         }
//         $mode = session('mode', 'Budget');
//         $budget = session('budget');
//         $mood = session('mood');
//         return view('mystery_box.create', compact('mode', 'budget', 'mood'));
//     })->name('mysterybox');

//     Route::post('/set-budget', function (Request $request) {
//         $request->validate(['budget' => 'required']);
//         session(['budget' => $request->budget, 'mode' => 'Mood']);
//         return redirect()->route('mysterybox');
//     })->name('set-budget');

//     Route::post('/set-mood', function (Request $request) {
//         $request->validate(['mood' => 'required']);
//         session(['mood' => $request->mood, 'mode' => 'Done']);
//         return redirect()->route('mysterybox');
//     })->name('set-mood');

