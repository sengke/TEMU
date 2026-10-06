<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/buy', [PropertyController::class, 'buy'])->name('buy');
Route::get('/rent', [PropertyController::class, 'rent'])->name('rent');
Route::get('/commercial', [PropertyController::class, 'commercial'])->name('commercial');
Route::get('/properties/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');

Route::get('/list-your-property', [SubmissionController::class, 'create'])->name('list.create');
Route::post('/list-your-property', [SubmissionController::class, 'store'])->middleware('throttle:5,1')->name('list.store');

Route::post('/inquiries', [InquiryController::class, 'store'])->middleware('throttle:10,1')->name('inquiry.store');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
