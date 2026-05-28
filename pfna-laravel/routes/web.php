<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\CvController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\PageEditorController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SectionContentController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/blog', [SiteController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [SiteController::class, 'post'])->name('blog.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/page', [PageEditorController::class, 'edit'])->name('page.edit');
        Route::post('/page', [PageEditorController::class, 'update'])->name('page.update');
        Route::post('/upload', [PageEditorController::class, 'upload'])->name('upload');
        Route::get('/sections', [SectionContentController::class, 'edit'])->name('sections.edit');
        Route::put('/sections', [SectionContentController::class, 'update'])->name('sections.update');
        Route::get('/cv', [CvController::class, 'edit'])->name('cv.edit');
        Route::post('/cv', [CvController::class, 'update'])->name('cv.update');
        Route::post('/cv/upload', [CvController::class, 'upload'])->name('cv.upload');
        Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('experiences', ExperienceController::class)->except(['show']);
        Route::resource('achievements', AchievementController::class)->except(['show']);
    });
});
