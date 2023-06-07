<?php

use App\Http\Controllers\Book\ShowBookListViewController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Products\ShowProductListViewController;
use App\Http\Controllers\User\CreateUserController;
use App\Http\Controllers\User\DeleteUserController;
use App\Http\Controllers\User\EditUserController;
use App\Http\Controllers\User\ShowUserListViewController;
use App\Http\Controllers\Informations\ShowInformationsListViewController;
use App\Http\Controllers\Informations\CreateInformationsController;
use App\Http\Controllers\Informations\EditInformationsController;
use App\Http\Controllers\Informations\DeleteInformationsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('repositories', fn() => redirect('repositories/users'));
Route::get('informations', fn() => redirect('repositories/informations'));

Route::get('repositories/users', ShowUserListViewController::class)->name('user:list');
Route::get('repositories/books', ShowBookListViewController::class)->name('book:list');
Route::get('repositories/products', ShowProductListViewController::class)->name('product:list');
Route::get('repositories/informations', ShowInformationsListViewController::class)->name('informations:list');

Route::post('repositories/users/{user}/delete', DeleteUserController::class);
Route::post('repositories/users/{user}/edit', EditUserController::class);
Route::post('repositories/users/create', CreateUserController::class);
Route::post('repositories/informations/create', CreateInformationsController::class);
Route::post('repositories/informations/{informations}/edit', EditInformationsController::class);
Route::post('repositories/informations/{informations}/delete', DeleteInformationsController::class);
