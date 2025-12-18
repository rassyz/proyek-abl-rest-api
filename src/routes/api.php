<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\ProdiController;
use App\Http\Controllers\Api\Admin\TahunAkademikController;
use App\Http\Controllers\Api\Admin\RuanganController;
use App\Http\Controllers\Api\Admin\MataKuliahController;
use App\Http\Controllers\Api\Admin\DosenController;
use App\Http\Controllers\Api\Admin\MahasiswaController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\Api\Admin\KelasController;
use App\Http\Controllers\Api\Admin\JadwalController;
use App\Http\Controllers\Api\JadwalViewController;
use App\Http\Controllers\Api\KrsController;
use App\Http\Controllers\Api\NilaiController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Authentication Routes
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});

// Admin Routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::apiResource('prodi', ProdiController::class);
    Route::apiResource('tahun-akademik', TahunAkademikController::class);
    Route::apiResource('ruangan', RuanganController::class);
    Route::apiResource('mata-kuliah', MataKuliahController::class);

    // tambahan untuk prasyarat MK
    Route::put('mata-kuliah/{mata_kuliah}/prasyarat', [MataKuliahController::class, 'syncPrasyarat']);

    Route::apiResource('dosen', DosenController::class);
    Route::apiResource('mahasiswa', MahasiswaController::class);
    Route::apiResource('staff', StaffController::class);

    Route::apiResource('kelas', KelasController::class)
        ->parameters(['kelas' => 'kelas']);
    Route::apiResource('jadwal', JadwalController::class);
});

// ===== BISNIS (di luar admin) =====
Route::middleware(['auth:sanctum'])->group(function () {

    // KRS (mahasiswa)
    Route::middleware(['role:mahasiswa'])->group(function () {
        Route::get('/krs', [KrsController::class, 'index']);
        Route::post('/krs/ambil', [KrsController::class, 'ambil']);
        Route::delete('/krs/drop/{krsDetail}', [KrsController::class, 'drop']);
        Route::post('/krs/submit', [KrsController::class, 'submit']);
    });

    // Nilai (dosen/admin)
    Route::middleware(['role:dosen|admin'])->group(function () {
        Route::get('/nilai/kelas/{kelas}', [NilaiController::class, 'indexByKelas']);
        Route::post('/nilai', [NilaiController::class, 'storeBatch']);
        Route::post('/nilai/finalisasi', [NilaiController::class, 'finalisasi']);
    });

    // Nilai (mahasiswa)
    Route::middleware(['role:mahasiswa'])->group(function () {
        Route::get('/nilai', [NilaiController::class, 'indexMyGrades']);
    });

    // Jadwal view role-based (semua role)
    Route::middleware(['role:admin|dosen|mahasiswa'])->group(function () {
        Route::get('/jadwal', [JadwalViewController::class, 'index']);
    });
});
