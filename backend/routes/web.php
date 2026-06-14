<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UstadzController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PendaftaranController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', [HomeController::class, 'tentang'])->name('tentang');
Route::get('/info', [HomeController::class, 'info'])->name('info');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // limit to 5 attempts per minute
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');

// Protected Routes - Monitoring (requires login)
Route::get('/monitoring', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isUstadz()) {
        return redirect()->route('ustadz.dashboard');
    } elseif ($user->isOrangTua()) {
        return redirect()->route('parent.dashboard');
    }
    return redirect()->route('home');
})->name('monitoring');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Santri Management
    Route::get('/santri', [AdminController::class, 'santri'])->name('santri');
    Route::get('/santri/create', [AdminController::class, 'createSantri'])->name('santri.create');
    Route::post('/santri', [AdminController::class, 'storeSantri'])->name('santri.store');
    Route::get('/santri/{id}/edit', [AdminController::class, 'editSantri'])->name('santri.edit');
    Route::put('/santri/{id}', [AdminController::class, 'updateSantri'])->name('santri.update');
    Route::delete('/santri/{id}', [AdminController::class, 'destroySantri'])->name('santri.destroy');
    
    // Hafalan Management
    Route::get('/hafalan', [AdminController::class, 'hafalan'])->name('hafalan');
    Route::get('/hafalan/export/pdf', [AdminController::class, 'exportHafalanPdf'])->name('hafalan.export-pdf');
    Route::get('/hafalan/santri/{santriId}', [AdminController::class, 'hafalanSantriShow'])->name('hafalan.santri.show');
    Route::get('/hafalan/santri/{santriId}/pdf', [AdminController::class, 'exportHafalanPdfPerSantri'])->name('hafalan.santri.pdf');

    // Admin Pendaftaran Routes
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('/pendaftaran/{id}/approve', [PendaftaranController::class, 'approve'])->name('pendaftaran.approve');
    Route::delete('/pendaftaran/{id}', [PendaftaranController::class, 'reject'])->name('pendaftaran.reject');
});

// Ustadz Routes
Route::middleware(['auth', 'role:ustadz'])->prefix('ustadz')->name('ustadz.')->group(function () {
    Route::get('/dashboard', [UstadzController::class, 'dashboard'])->name('dashboard');
    Route::get('/santri', [UstadzController::class, 'index'])->name('santri.index');
    Route::get('/santri/{id}', [UstadzController::class, 'show'])->name('santri.show');
    Route::get('/santri/{santriId}/hafalan/create', [UstadzController::class, 'create'])->name('hafalan.create');
    Route::post('/hafalan', [UstadzController::class, 'store'])->name('hafalan.store');
    Route::get('/hafalan/{id}/edit', [UstadzController::class, 'edit'])->name('hafalan.edit');
    Route::put('/hafalan/{id}', [UstadzController::class, 'update'])->name('hafalan.update');
    Route::delete('/hafalan/{id}', [UstadzController::class, 'destroy'])->name('hafalan.destroy');
});

// Parent Routes
Route::middleware(['auth', 'role:orang_tua'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    Route::get('/santri/{id}', [ParentController::class, 'showSantri'])->name('santri.show');
    Route::get('/hafalan/{hafalanId}/listen', [ParentController::class, 'listenAudio'])->name('hafalan.listen');
    Route::get('/hafalan/{hafalanId}/download', [ParentController::class, 'downloadAudio'])->name('hafalan.download');
    Route::get('/notifications', [ParentController::class, 'notifications'])->name('notifications');
    Route::post('/notification/{notificationId}/read', [ParentController::class, 'markNotificationAsRead'])->name('notification.read');
});
