<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegularController;
use App\Http\Controllers\API\CheckController;
use App\Http\Controllers\API\MainController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\API\DiaryController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/creategroup', [RegularController::class, 'createGroup']);
Route::post('/editgroup', [RegularController::class, 'editGroup']);
Route::post('/deletegroup', [RegularController::class, 'deleteGroup']);

Route::get('/getregular', [RegularController::class, 'getRegular']);
Route::post('/createaffair', [RegularController::class, 'createAffair']);
Route::post('/editaffair', [RegularController::class, 'editAffair']);
Route::post('/deleteaffair', [RegularController::class, 'deleteAffair']);

Route::post('createorupdatecheck', [CheckController::class, 'createOrUpdateCheck']);

Route::post('addtodo', [DiaryController::class, 'addTodo']);
Route::post('deletetodo', [DiaryController::class, 'deleteTodo']);
Route::get('getdiary', [DiaryController::class, 'getDiary']);

Route::post('savenote', [NoteController::class, 'saveNote']);
Route::post('deletenote', [NoteController::class, 'deleteNote']);
Route::get('getnotes', [NoteController::class, 'getNotes']);

Route::get('statlistupdate', [StatisticsController::class, 'statListUpdate']);
Route::get('getcalendar', [MainController::class, 'getCalendar']);

Route::post('addtask', [TaskController::class, 'addTask']);
Route::post('edittask', [TaskController::class, 'editTask']);
Route::post('deletetask', [TaskController::class, 'deleteTask']);
Route::get('gettasks', [TaskController::class, 'getTasks']);