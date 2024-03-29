<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [StudentController::class, 'index']);
Route::get('/view_grades', [StudentController::class, 'view_grades']);
Route::post('/import_grades', [StudentController::class, 'import_grades']);
Route::post('/condition_grades_db', [StudentController::class, 'condition_grades_db']);
