<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Blog\ArticleController;
use App\Http\Controllers\WebhookController;

// Blog routes
Route::get('/', [ArticleController::class, 'index'])->name('home');
Route::get('/article/{slug}', [ArticleController::class, 'show'])->name('article.show');

// Webhook routes
Route::post('/webhooks/storyblok', [WebhookController::class, 'storyblok'])->name('webhooks.storyblok');
