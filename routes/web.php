<?php

use App\Http\Controllers\LineLoginController;
use App\Http\Controllers\LineWebhookController;
use App\Http\Controllers\PriceReportController;
use App\Http\Controllers\WatchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PriceReportController::class, 'index'])->name('reports.index');
Route::get('/search', [PriceReportController::class, 'search'])->name('reports.search');
Route::post('/reports', [PriceReportController::class, 'store'])
    ->name('reports.store')
    ->middleware('throttle:5,1');
Route::get('/sitemap.xml', [PriceReportController::class, 'sitemap'])->name('sitemap');
Route::view('/about', 'about')->name('about');

// LINE連携（ウォッチ業者の新規相見積もり額口コミ通知）
Route::get('/line/login', [LineLoginController::class, 'redirect'])->name('line.login');
Route::get('/line/callback', [LineLoginController::class, 'callback'])->name('line.callback');
Route::post('/watches', [WatchController::class, 'toggle'])
    ->name('watches.toggle')
    ->middleware('throttle:10,1');
Route::post('/line/webhook', [LineWebhookController::class, 'handle'])->name('line.webhook');
