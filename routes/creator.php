<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BiographyWebController;


// Creator Home Routes
// routes/web.php - Aggiungi queste routes

// Creator Home Routes
Route::prefix('creator')->name('creator.')->group(function () {

    // Home page del creator
    Route::get('/{id}', [CreatorHomeController::class, 'home'])
        ->where('id', '[0-9]+')
        ->middleware('creator.nickname')
        ->name('home');

    // Home page del creator
    Route::get('/', [CreatorHomeController::class, 'index'])->name('index');

    // Sezioni già funzionanti
    Route::get('/{id}/collections', [CreatorHomeController::class, 'collections'])
        ->where('id', '[0-9]+')
        ->middleware('creator.nickname')
        ->name('collections');

    Route::get('/{id}/collection/{collection}', [CreatorHomeController::class, 'showCollection'])
        ->where(['id' => '[0-9]+', 'collection' => '[0-9]+']) // Validiamo entrambi come numerici
        ->middleware('creator.nickname')
        ->name('collection.show');

    // Portfolio reale
    Route::get('/{id}/portfolio', [CreatorHomeController::class, 'portfolio'])
        ->where('id', '[0-9]+')
        ->middleware('creator.nickname')
        ->name('portfolio');

    Route::get('/{id}/biography', [BiographyWebController::class, 'show'])
        ->where('id', '[0-9]+')
        ->middleware('creator.nickname')
        ->name('biography');

    Route::get('/{id}/journey', [CreatorHomeController::class, 'underConstruction'])
        ->where('id', '[0-9]+')
        ->middleware('creator.nickname')
        ->name('journey');

    Route::get('/{id}/impact', [CreatorHomeController::class, 'underConstruction'])
        ->where('id', '[0-9]+')
        ->middleware('creator.nickname')
        ->name('impact');

    Route::get('/{id}/community', [CreatorHomeController::class, 'underConstruction'])
        ->where('id', '[0-9]+')
        ->middleware('creator.nickname')
        ->name('community');

    // Route per nick_name (stringhe alfanumeriche con trattini, underscore, spazi e caratteri codificati URL)
    Route::get('/{nick_name}', [CreatorHomeController::class, 'home'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('home.nickname');

    Route::get('/{nick_name}/collections', [CreatorHomeController::class, 'collections'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('collections.nickname');

    Route::get('/{nick_name}/collection/{collection}', [CreatorHomeController::class, 'showCollection'])
        ->where(['nick_name' => '[a-zA-Z][a-zA-Z0-9_%20+ -]*', 'collection' => '[0-9]+'])
        ->name('collection.show.nickname');

    Route::get('/{nick_name}/portfolio', [CreatorHomeController::class, 'portfolio'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('portfolio.nickname');

    Route::get('/{nick_name}/biography', [BiographyWebController::class, 'show'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('biography.nickname');

    Route::get('/{nick_name}/journey', [CreatorHomeController::class, 'underConstruction'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('journey.nickname');

    Route::get('/{nick_name}/impact', [CreatorHomeController::class, 'underConstruction'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('impact.nickname');

    Route::get('/{nick_name}/community', [CreatorHomeController::class, 'underConstruction'])
        ->where('nick_name', '[a-zA-Z][a-zA-Z0-9_%20+ -]*')
        ->name('community.nickname');
});
