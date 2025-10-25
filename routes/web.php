<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaslonController;
use App\Http\Controllers\Admin\TokenController as AdminTokenController;
use App\Http\Controllers\Admin\PklStudentController;
use App\Http\Controllers\Admin\VoteSummaryController;
use App\Http\Controllers\Auth\TokenLoginController;
use App\Http\Controllers\Auth\PklLoginController;
use App\Http\Controllers\Voter\DashboardController as VoterDashboardController;
use App\Http\Controllers\Voter\VoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [TokenLoginController::class, 'showLoginForm'])->name('voter.login');
    Route::post('/login', [TokenLoginController::class, 'login'])->name('voter.login.submit');

    Route::get('/pkl/login', [PklLoginController::class, 'showLoginForm'])->name('pkl.login');
    Route::post('/pkl/login', [PklLoginController::class, 'login'])->name('pkl.login.submit');
});

Route::middleware('token.auth')->group(function () {
    Route::get('/dashboard', VoterDashboardController::class)->name('voter.dashboard');
    Route::post('/vote', [VoteController::class, 'store'])->name('voter.vote');
});

Route::get('/thanks', function (Request $request) {
    if (! $request->session()->has('voted_paslon_name')) {
        return redirect()->route('voter.login');
    }

    $paslonName = $request->session()->get('voted_paslon_name');
    $request->session()->forget('voted_paslon_name');

    return view('voter.thanks', compact('paslonName'));
})->name('voter.thanks');

Route::post('/logout', [TokenLoginController::class, 'logout'])->name('voter.logout');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::resource('paslon', PaslonController::class)->except(['show']);

        Route::get('tokens/summary', VoteSummaryController::class)->name('tokens.summary');
        Route::get('tokens/print', [AdminTokenController::class, 'print'])->name('tokens.print');
        Route::resource('tokens', AdminTokenController::class)->only(['index', 'store', 'destroy']);

        Route::get('pkl-students/template', [PklStudentController::class, 'template'])->name('pkl-students.template');
        Route::post('pkl-students/import', [PklStudentController::class, 'import'])->name('pkl-students.import');
        Route::resource('pkl-students', PklStudentController::class)->only(['index', 'store', 'destroy']);
    });
});
