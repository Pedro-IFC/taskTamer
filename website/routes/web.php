<?php

use App\Http\Controllers\MachineController;
use App\Http\Controllers\ProfileController;
use App\Models\Machine;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $maquinas = Machine::where('user_id', Auth::id())->get();
    return view('dashboard', ["maquinas"=>$maquinas]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard/{id}', [MachineController::class, 'view'])->middleware(['auth', 'verified'])->name('dashboard.maquina');
Route::get('/machine/deletar/{id}', [MachineController::class, 'delete'])->middleware(['auth', 'verified'])->name('deletar.maquina');

Route::post('/machine/', [MachineController::class, 'register'])->middleware(['auth', 'verified'])->name('register-machine');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
