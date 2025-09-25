<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\FaqController;

// Home minimalista
Route::get('/', [HomeController::class, 'index'])->name('home');

// FAQs (nomes SEMPRE no plural)
Route::get('/duvidas', [FaqController::class, 'index'])->name('faqs.index');
Route::get('/d/{faq:slug}', [FaqController::class, 'show'])->name('faqs.show');
