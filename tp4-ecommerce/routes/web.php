<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


// Routes d'authentification temporaires
Route::get('/login', function () {
    return "Page de connexion - À implémenter";
})->name('login');

Route::get('/register', function () {
    return "Page d'inscription - À implémenter";
})->name('register');

// SUPPRIMEZ ou COMMENCEZ cette ligne :
// require __DIR__.'/auth.php';