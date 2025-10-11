<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PusatController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UppMaterialController;
use App\Http\Controllers\AktivitasHarianController;

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

// Group semua API routes dengan middleware auth:sanctum for token authentication
Route::middleware(['auth:sanctum'])->group(function () {

    // =================== DASHBOARD API ===================

    // Get stock data untuk grafik/dashboard
    Route::get('/stock-data', [DashboardController::class, 'getStockDataApi'])
        ->name('api.stock.data');

    // Update kapasitas stok
    Route::post('/stock-capacity', [DashboardController::class, 'updateCapacityApi'])
        ->name('api.capacity.update');


    // =================== REGIONS API ===================

    // Get all regions
    Route::get('/regions', function () {
        return \App\Models\Region::all();
    })->name('api.regions.index');

    // Get region by ID
    Route::get('/regions/{id}', function ($id) {
        return \App\Models\Region::with('plants')->findOrFail($id);
    })->name('api.regions.show');


    // =================== PLANTS API ===================

    // Get all plants dengan region
    Route::get('/plants', function () {
        return \App\Models\Plant::with('region')->get();
    })->name('api.plants.index');

    // Get plant by ID
    Route::get('/plants/{id}', function ($id) {
        return \App\Models\Plant::with('region')->findOrFail($id);
    })->name('api.plants.show');

    // Get plants by region
    Route::get('/regions/{regionId}/plants', function ($regionId) {
        return \App\Models\Plant::where('region_id', $regionId)->get();
    })->name('api.regions.plants');


    // =================== ITEMS API ===================

    // Get all items
    Route::get('/items', function () {
        return \App\Models\Item::all();
    })->name('api.items.index');

    // Get item by ID
    Route::get('/items/{id}', function ($id) {
        return \App\Models\Item::findOrFail($id);
    })->name('api.items.show');

    // Get items by category
    Route::get('/items/category/{category}', function ($category) {
        return \App\Models\Item::where('kategori_material', $category)->get();
    })->name('api.items.by-category');


    // =================== STOCKS API ===================

    // Get current stocks
    Route::get('/stocks/current', function () {
        return \App\Models\CurrentStock::with('item')->get();
    })->name('api.stocks.current');

    // Get stocks by location
    Route::get('/stocks/current/location/{locationId}', function ($locationId) {
        return \App\Models\CurrentStock::with('item')
            ->where('lokasi_id', $locationId)
            ->get();
    })->name('api.stocks.by-location');

    // Get stocks by item
    Route::get('/stocks/current/item/{itemId}', function ($itemId) {
        return \App\Models\CurrentStock::with('item')
            ->where('item_id', $itemId)
            ->get();
    })->name('api.stocks.by-item');


    // =================== TRANSACTION LOGS API ===================

    // Get all transaction logs
    Route::get('/transactions/logs', function () {
        return \App\Models\TransactionLog::with(['item', 'destinationSale'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();
    })->name('api.transactions.logs');

    // Get transactions by date range
    Route::get('/transactions/logs/date-range', function () {
        $startDate = request('start_date');
        $endDate = request('end_date');

        return \App\Models\TransactionLog::with(['item', 'destinationSale'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();
    })->name('api.transactions.by-date-range');

    // Get transactions by movement type
    Route::get('/transactions/logs/type/{type}', function ($type) {
        return \App\Models\TransactionLog::with(['item', 'destinationSale'])
            ->where('tipe_pergerakan', $type)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();
    })->name('api.transactions.by-type');


    // =================== DESTRUCTION SUBMISSIONS API ===================

    // Get all destruction submissions
    Route::get('/destruction-submissions', function () {
        return \App\Models\DestructionSubmission::with(['item', 'transactionLog'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
    })->name('api.destruction-submissions.index');

    // Get submissions by status
    Route::get('/destruction-submissions/status/{status}', function ($status) {
        return \App\Models\DestructionSubmission::with(['item', 'transactionLog'])
            ->where('status_pengajuan', $status)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
    })->name('api.destruction-submissions.by-status');


    // =================== LEGACY API ROUTES (Sudah ada) ===================
    // Route-route ini sementara dipertahankan untuk compatibility dengan existing code

    // API untuk materials pusat (DataTable)
    Route::get('/pusat-materials', [PusatController::class, 'getPusatMaterials'])
        ->name('api.pusat.materials')
        ->middleware('can:manage data playang');

    // API untuk materials facility (DataTable)
    Route::get('/facility-materials/{facility}', [MaterialController::class, 'getFacilityMaterials'])
        ->name('api.facility.materials')
        ->middleware('can:manage transaksi');

    // API untuk transaksi facilities (DataTable)
    Route::get('/transaksi-facilities', [TransactionController::class, 'getTransaksiFacilities'])
        ->name('api.transaksi.facilities')
        ->middleware('can:manage transaksi');

    // API untuk aktivitas transaksi (DataTable)
    Route::get('/aktivitas-transaksi', [AktivitasHarianController::class, 'getAktivitasTransaksi'])
        ->name('api.aktivitas.transaksi')
        ->middleware('can:manage aktivitas harian');

    // API untuk UPP materials (DataTable)
    Route::get('/upp-materials', [UppMaterialController::class, 'getUppMaterials'])
        ->name('api.upp.materials')
        ->middleware('can:manage data playang');


    

    // =================== INITIAL STOCKS API ===================

    // Get initial stocks
    Route::get('/stocks/initial', function () {
        return \App\Models\InitialStock::with('item')->get();
    })->name('api.stocks.initial');

    // Get initial stocks by item
    Route::get('/stocks/initial/item/{itemId}', function ($itemId) {
        return \App\Models\InitialStock::where('item_id', $itemId)->get();
    })->name('api.stocks.initial.by-item');

});

// =================== PUBLIC API (No Authentication Required) ===================

// Login API untuk Postman/Testing - Updated to use Sanctum tokens
Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials'
    ], 401);
})->name('api.login');

// Logout API - Updated for Sanctum
Route::post('/logout', function (Illuminate\Http\Request $request) {
    if (Auth::check()) {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
    }

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully'
    ]);
})->name('api.logout');

// Check authentication status - Updated for Sanctum
use Illuminate\Http\Request;
Route::get('/check-auth', function (Request $request) {
    $user = $request->user();
    if ($user) {
        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    return response()->json([
        'authenticated' => false,
        'message' => 'Not authenticated'
    ], 401);
})->middleware('auth:sanctum')->name('api.check-auth');

// Get all destination sales (public for testing)
Route::get('/destination-sales', function () {
    return \App\Models\DestinationSale::all();
})->name('api.destination-sales.index');

// Get regions (public for testing)
Route::get('/regions-public', function () {
    return \App\Models\Region::all();
})->name('api.regions.public');

// Get items (public for testing)
Route::get('/items-public', function () {
    return \App\Models\Item::all();
})->name('api.items.public');

// Get current stocks (public for testing)
Route::get('/stocks-public', function () {
    return \App\Models\CurrentStock::with('item')->get();
})->name('api.stocks.public');

// Simple debug API for testing using new models
Route::get('/pusat-materials-debug', function () {
    try {
        // Test data dari model baru
        $regions = \App\Models\Region::all();
        $items = \App\Models\Item::all();
        $stocks = \App\Models\CurrentStock::with('item')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'regions' => $regions,
                'items' => $items,
                'current_stocks' => $stocks
            ],
            'message' => 'API working with new models!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ]);
    }
})->name('api.pusat.materials.debug');

// Legacy route untuk compatibility (mungkin error)
Route::get('/pusat-materials-debug-legacy', [PusatController::class, 'getPusatMaterials'])
    ->name('api.pusat.materials.debug.legacy');