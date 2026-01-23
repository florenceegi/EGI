<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Company Routes - Business Profile & Showcase
|--------------------------------------------------------------------------
| 🎨 Corporate Palette: Blue #1E3A5F, Gold #C9A227, Green #2D7D46
|
| Stessa struttura di Creator, ma per aziende.
| Le aziende creano EGI (prodotti certificati su blockchain) come i creator.
*/

Route::prefix('company')->name('company.')->group(function () {

    // Index - Lista tutte le aziende
    Route::get('/', [CompanyHomeController::class, 'index'])->name('index');

    // Home (redirect to portfolio)
    Route::get('/{id}', [CompanyHomeController::class, 'home'])
        ->where('id', '[0-9]+')
        ->name('home');

    // Portfolio EGI
    Route::get('/{id}/portfolio', [CompanyHomeController::class, 'portfolio'])
        ->where('id', '[0-9]+')
        ->name('portfolio');

    // Collections
    Route::get('/{id}/collections', [CompanyHomeController::class, 'collections'])
        ->where('id', '[0-9]+')
        ->name('collections');

    // About
    Route::get('/{id}/about', [CompanyHomeController::class, 'about'])
        ->where('id', '[0-9]+')
        ->name('about');

    // Update About (only owner)
    Route::patch('/{id}/about', [CompanyHomeController::class, 'updateAbout'])
        ->where('id', '[0-9]+')
        ->name('about.update')
        ->middleware('auth');

    // Impact (EPP)
    Route::get('/{id}/impact', [CompanyHomeController::class, 'impact'])
        ->where('id', '[0-9]+')
        ->name('impact');

    // Route per nick_name
    Route::get('/{nick_name}', [CompanyHomeController::class, 'home'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('home.nickname');

    Route::get('/{nick_name}/portfolio', [CompanyHomeController::class, 'portfolio'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('portfolio.nickname');

    Route::get('/{nick_name}/collections', [CompanyHomeController::class, 'collections'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('collections.nickname');

    Route::get('/{nick_name}/about', [CompanyHomeController::class, 'about'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('about.nickname');

    Route::get('/{nick_name}/impact', [CompanyHomeController::class, 'impact'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('impact.nickname');
});
